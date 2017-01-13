<?php

namespace Alpixel\Bundle\JiraBundle\Transformer;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
interface TransformerInterface
{
    /**
     * @param $dataToTransform
     * @return mixed
     */
    public function transformData($dataToTransform);
}
