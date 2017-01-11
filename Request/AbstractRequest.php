<?php

namespace Alpixel\Bundle\JiraBundle\Request;

use Alpixel\Bundle\JiraBundle\Response\Response;
use Psr\Log\LoggerInterface;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
abstract class AbstractRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    protected $baseUrlAPI;
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

    public function __construct(string $baseUrlAPI, SecurityContext $securityContext, LoggerInterface $monolog)
    {
        $this->setBaseUrlAPI($baseUrlAPI);
        $this->setSecurityContext($securityContext);
        $this->setMonolog($monolog);
    }

    /**
     * @return string
     */
    public function getBaseUrlAPI(): string
    {
        return $this->baseUrlAPI;
    }

    /**
     * @param string $baseUrlAPI
     * @return $this
     */
    protected function setBaseUrlAPI(string $baseUrlAPI)
    {
        if (substr($baseUrlAPI, -1) !== '/') {
            $baseUrlAPI .= '/';
        }

        $this->baseUrlAPI = $baseUrlAPI;

        return $this;
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
     * @return $this
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }

    /**
     * @return LoggerInterface
     */
    private function getMonolog()
    {
        return $this->monolog;
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
     * @return array
     */
    public function getCurlOptions(): array
    {
        return $this->curlOptions;
    }

    /**
     * @param array $curlOptions
     * @return $this
     */
    public function setCurlOptions(array $curlOptions)
    {
        $this->curlOptions = $curlOptions;

        return $this;
    }

    public function get(string $url, array $urlParameters = [], array $curlOptions = [])
    {
        return $this->buildRequest(self::METHOD_GET, $url, $urlParameters, $curlOptions);
    }

    public function buildRequest(string $method, string $url, $urlParameters = [], array $curlOptions = [])
    {
        switch ($method) {
            case self::METHOD_GET:
                $curlOptions[CURLOPT_HTTPGET] = true;
                if (!empty($urlParameters)) {
                    $url .= '?' . http_build_query($urlParameters);
                }
                break;
            case self::METHOD_POST:
                $curlOptions[CURLOPT_POST] = true;
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown HTTP method "%s"'), $method);
        }

        return $this->executeRequest($url, $curlOptions);
    }

    /**
     * Create final url to API
     *
     * @param $urlAPIEndpoint
     * @return string
     */
    public function createUrl($urlAPIEndpoint)
    {
        if (substr($urlAPIEndpoint, 0, 1) === '/') {
            $urlAPIEndpoint = substr($urlAPIEndpoint, 1);
        }

        return $this->getBaseUrlAPI() . $urlAPIEndpoint;
    }

    /**
     * @param array $curlOptions
     * @return array
     */
    protected function resolveCurlOptions(array $curlOptions = [])
    {
        return array_replace([
            CURLOPT_TIMEOUT => 300,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ], $curlOptions);
    }

    /**
     * @param string $urlAPIEndpoint
     * @param array $curlOptions
     * @return Response
     */
    public function executeRequest(string $urlAPIEndpoint, array $curlOptions = [])
    {
        $url = $this->createUrl($urlAPIEndpoint);

        $this->getMonolog()->info('Alpixel JIRA API [Request] : ' . $urlAPIEndpoint);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        $curlOptions = $this->resolveCurlOptions($curlOptions);
        curl_setopt_array($ch, $curlOptions);

        $this->getSecurityContext()->applyAuthentication($ch);

        $data = curl_exec($ch);
        $error = curl_error($ch);

        $response = new Response();
        $response->setResponse($ch, $error, $data);

        return $response;
    }
}
