<?php

namespace Alpixel\Bundle\JiraBundle\Transformer;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class JsonToArrayTransformer implements TransformerInterface
{
    /**
     * @var array
     */
    protected $context;

    /**
     * @var bool
     */
    protected $jsonHasError;

    public function __construct(array $context = [])
    {
        $this->setContext($context);
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @param array $context
     * @return JsonToArrayTransformer
     */
    public function setContext(array $context): JsonToArrayTransformer
    {
        $this->context = $context;

        return $this;
    }

    /**
     * @return bool
     */
    public function jsonHasError()
    {
        return $this->jsonHasError;
    }

    /**
     * @return array
     */
    protected function resolveContext()
    {
        return array_merge([
            'json_decode_associative_array' => true,
            'json_decode_depth' => 512,
            'json_decode_option' => 0,
        ], $this->context);
    }

    public function transformData($dataToTransform)
    {
        $context = $this->resolveContext();
        $transformedData = json_decode(
            $dataToTransform,
            $context['json_decode_associative_array'],
            $context['json_decode_depth'],
            $context['json_decode_option']
        );

        $this->jsonHasError = !(json_last_error() === JSON_ERROR_NONE);

        return $transformedData;
    }
}
