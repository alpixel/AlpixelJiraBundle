<?php

namespace Alpixel\Bundle\JiraBundle\Request;

use Alpixel\Bundle\JiraBundle\Data\JsonToArrayTransformer;
use Alpixel\Bundle\JiraBundle\Response\Response;
use Psr\Log\LoggerInterface;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class Request
{

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const TIMEOUT = 300;

    /**
     * @var SecurityContext
     */
    private $security;

    /**
     * @var array curl options
     */
    private $options;

    /**
     * @var LoggerInterface
     */
    private $monolog;

    protected $baseUrl;

    public function __construct(SecurityContext $security, string $baseUrl, LoggerInterface $monolog)
    {
        $this->security = $security;

        if (substr($baseUrl, (strlen($baseUrl) -1 )) !== '/') {
            $baseUrl .= '/';
        }

        $this->baseUrl = $baseUrl;
        $this->monolog = $monolog;
    }

    protected function getMonolog()
    {
        return $this->monolog;
    }

    protected function getSecurity() : SecurityContext
    {
        return $this->security;
    }

    protected function applyAuthentication()
    {
        $security = $this->getSecurity();
        if ($security->getAuthMethod() === SecurityContext::METHOD_BASIC) {
            $parameters = $security->getCredentials();
            $credentials = implode(':', $parameters);
            $this->options[CURLOPT_USERPWD] = $credentials;
        } else {
            $this->getMonolog()->error('Alpixel JIRA API [Authentication] invalid format of credentials : '.$error, [
                'file' => __FILE__,
                'line' => __LINE__,
            ]);
            throw new \Exception('Invalid authentification.');
        }
    }

    public function exec(string $url, array $opt = [])
    {
        $this->applyAuthentication();

        $urlRequest = $this->baseUrl.$url;

        $monolog  = $this->getMonolog();
        $monolog->info('Alpixel JIRA API [Request] : '.$urlRequest, [
            'file' => __FILE__,
            'line' => __LINE__,
        ]);

        $options = array_replace([
            CURLOPT_URL => $urlRequest,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ], $this->options, $opt);

        $ch = curl_init();
        curl_setopt_array($ch, $options);

        $data = curl_exec($ch);
        $error = curl_error($ch);

        if (!empty($error)) {
            $monolog->error('Alpixel JIRA API [Response] curl error : '.$error, [
                'file' => __FILE__,
                'line' => __LINE__,
            ]);
            throw new \RuntimeException(sprintf('curl reports the following errors : "%s"', $error));
        }

        $response = new Response(new JsonToArrayTransformer());
        $response->setResponse($data, $error);

        return $response;
    }

    public function buildRequest(string $method, string $url, $parameters = [], array $opt = [])
    {
        switch ($method) {
            case self::METHOD_GET:
                $this->options[CURLOPT_HTTPGET] = true;
                if (!empty($parameters)) {
                    $queryParams = http_build_query($parameters);
                    $url .= '?'.$queryParams;
                }
                break;
            case self::METHOD_POST:
                $this->options[CURLOPT_POST] = true;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown HTTP method "%s"'), $method);
        }

        return $this->exec($url, $opt);
    }

    public function get(string $url, array $parameters = [], array $opt = [])
    {
        return $this->buildRequest(self::METHOD_GET, $url, $parameters, $opt);
    }
}
