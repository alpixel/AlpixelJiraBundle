<?php

namespace Alpixel\Bundle\JiraBundle\Response;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class Response
{
    private $error;
    private $transformer;

    public function __construct($transformer)
    {
        $this->transformer = $transformer;
    }

    public function setResponse($data, $error)
    {
        $this->getTransformer()->setData($data);
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->getTransformer()->getData();
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return mixed
     */
    public function getTransformer()
    {
        return $this->transformer;
    }
}
