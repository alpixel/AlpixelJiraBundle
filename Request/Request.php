<?php

namespace Alpixel\Bundle\JiraBundle\Request;

use Alpixel\Bundle\JiraBundle\Response\Response;
use Psr\Log\LoggerInterface;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class Request
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @var array curl options
     */
    private $curlOptions;

    /**
     * @var LoggerInterface
     */
    private $monolog;

    /**
     * @var string
     */
    protected $baseUrlAPI;

    public function __construct(string $baseUrlAPI, SecurityContext $securityContext, LoggerInterface $monolog)
    {
        $this->setBaseUrlAPI($baseUrlAPI);
        $this->setSecurityContext($securityContext);
        $this->setMonolog($monolog);
    }

    /**
     * @return SecurityContext
     */
    public function getSecurityContext(): SecurityContext
    {
        return $this->securityContext;
    }

    /**
     * @param SecurityContext $securityContext
     * @return Request
     */
    public function setSecurityContext(SecurityContext $securityContext): Request
    {
        $this->securityContext = $securityContext;

        return $this;
    }

    /**
     * @return array
     */
    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    /**
     * @param array $curlOptions
     * @return Request
     */
    public function setCurlOptions(array $curlOptions): Request
    {
        $this->curlOptions = $curlOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseUrlAPI(): string
    {
        return $this->baseUrlAPI;
    }

    /**
     * @param string $baseUrl
     * @return Request
     */
    protected function setBaseUrlAPI(string $baseUrl): Request
    {
        if (substr($baseUrl, (strlen($baseUrl) -1 )) !== '/') {
            $baseUrl .= '/';
        }

        $this->baseUrlAPI = $baseUrl;

        return $this;
    }

    /**
     * @param LoggerInterface $monolog
     * @return LoggerInterface
     */
    protected function setMonolog(LoggerInterface $monolog)
    {
        $this->monolog = $monolog;

        return $this->monolog;
    }

    /**
     * @return LoggerInterface
     */
    private function getMonolog()
    {
        return $this->monolog;
    }

    protected function applyAuthentication($ch)
    {
        $this->getSecurityContext()->applyAuthentication($ch);
    }

    protected function resolveCurlContext(array $context = [])
    {
        return array_replace([
            CURLOPT_TIMEOUT => 300,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ], $context);
    }

    public function createUrl($urlAPIEndpoint)
    {
        if (substr($urlAPIEndpoint, 0, 1) === '/') {
            $urlAPIEndpoint = substr($urlAPIEndpoint, 1);
        }

        return $this->getBaseUrlAPI().$urlAPIEndpoint;
    }

    public function executeRequest(string $urlAPIEndpoint, array $curlOptions = [])
    {
        $url = $this->createUrl($urlAPIEndpoint);

        $this->getMonolog()->info('Alpixel JIRA API [Request] : '.$urlAPIEndpoint);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        $curlContext = $this->resolveCurlContext($curlOptions);
        curl_setopt_array($ch, $curlContext);

        $this->applyAuthentication($ch);

        $data = curl_exec($ch);
        $error = curl_error($ch);

        $response = new Response();
        $response->setResponse($ch, $error, $data);

        return $response;
    }

    public function buildRequest(string $method, string $url, $parameters = [], array $opt = [])
    {
        switch ($method) {
            case self::METHOD_GET:
                $this->curlOptions[CURLOPT_HTTPGET] = true;
                if (!empty($parameters)) {
                    $queryParams = http_build_query($parameters);
                    $url .= '?'.$queryParams;
                }
                break;
            case self::METHOD_POST:
                $this->curlOptions[CURLOPT_POST] = true;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown HTTP method "%s"'), $method);
        }

        return $this->executeRequest($url, $opt);
    }

    public function get(string $url, array $parameters = [], array $opt = [])
    {
        return $this->buildRequest(self::METHOD_GET, $url, $parameters, $opt);
    }
}
