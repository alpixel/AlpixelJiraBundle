<?php

namespace Alpixel\Bundle\JiraBundle\Data;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class JsonToArrayTransformer
{
    protected $originalData;
    protected $data;

    /**
     * @return mixed
     */
    public function getOriginalData()
    {
        return $this->originalData;
    }

    /**
     * @param mixed $originalData
     * @return ArrayTransformer
     */
    public function setOriginalData($originalData)
    {
        $this->originalData = $originalData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return ArrayTransformer
     */
    public function setData($data)
    {
        $this->originalData = $data;
        $this->data = json_decode($data, true);

        return $this;
    }
}
