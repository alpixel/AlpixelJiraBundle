<?php

namespace Alpixel\Bundle\JiraBundle\Response;

use Alpixel\Bundle\JiraBundle\Transformer\JsonToArrayTransformer;
use Alpixel\Bundle\JiraBundle\Transformer\TransformerInterface;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
abstract class AbstractResponse
{
    /**
     * @var string
     */
    protected $curlStringError;

    /**
     * @var bool
     */
    protected $isCurlError = false;

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var resource (curl)
     */
    protected $curlResource;

    /**
     * @var int
     */
    protected $httpStatusCode;

    /**
     * @var bool
     */
    protected $isSuccessHttpStatusCode;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $originalData;

    /**
     * @return mixed
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }

    /**
     * @param mixed $originalData
     * @return AbstractResponse
     */
    public function setOriginalData($originalData)
    {
        $this->originalData = $originalData;

        return $this;
    }

    /**
     * @return TransformerInterface
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    /**
     * @param TransformerInterface $transformer
     * @return $this
     */
    public function setTransformer(TransformerInterface $transformer)
    {
        $this->data = null;
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @param resource (curl) $curlResource
     * @param string $curlStringError
     * @param mixed $data
     */
    public function setResponse($curlResource, string $curlStringError = '', $data)
    {
        $this->setCurlResource($curlResource);
        $this->transformCurlResource($curlResource);
        $this->setOriginalData($data);

        if (!empty($curlStringError)) {
            $this->setCurlStringError($curlStringError);
            $this->isCurlError = true;
        }
    }

    /**
     * @param string $error
     * @return $this
     */
    protected function setCurlStringError(string $error)
    {
        $this->curlStringError = $error;

        return $this;
    }

    /**
     * @return boolean
     */
    protected function isCurlError(): bool
    {
        return $this->isCurlError;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return (!$this->isCurlError() && $this->isSuccessHttpStatusCode());
    }

    /**
     * @param int $httpStatusCode
     * @return $this
     */
    protected function setHttpStatusCode(int $httpStatusCode)
    {
        $this->httpStatusCode = (int) $httpStatusCode;

        $isSuccess = ($httpStatusCode >= 200 && $httpStatusCode < 300 || $httpStatusCode === 304);
        $this->isSuccessHttpStatusCode = $isSuccess;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSuccessHttpStatusCode()
    {
        return $this->isSuccessHttpStatusCode;
    }

    /**
     * @return int
     */
    public function getHttpStatusCode()
    {
        return $this->httpStatusCode;
    }

    /**
     * @param resource $curlResource A curl resource
     * @return $this
     */
    protected function setCurlResource($curlResource)
    {
        if (!is_resource($curlResource) || get_resource_type($curlResource) !== 'curl') {
            throw new \InvalidArgumentException('The parameter "$curlResource" must be a ressource of curl.');
        }

        $this->curlResource = $curlResource;

        return $this;
    }

    /**
     * @param $curlRessource
     * @return $this
     */
    public function transformCurlResource($curlRessource)
    {
        $this->setHttpStatusCode(curl_getinfo($curlRessource, CURLINFO_HTTP_CODE));

        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        if ($this->data === null) {
            if ($this->getTransformer() === null) {
                $this->setTransformer(new JsonToArrayTransformer());
            }

            $originalData = $this->getOriginalData();
            $this->data = $this->getTransformer()->transformData($originalData);
        }

        return $this->data;
    }

}
