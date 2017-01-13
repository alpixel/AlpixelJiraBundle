<?php

namespace Alpixel\Bundle\JiraBundle\Request;

use Alpixel\Bundle\JiraBundle\Exception\SecurityContext\AuthMethodException;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AuthenticationProvider
{

    /**
     * @var string
     */
    protected $authMethod;

    /**
     * @var array
     */
    protected $authParameters;

    public function __construct(string $authMethod, array $authParameters)
    {
        $this->setAuthMethod($authMethod);
        $this->setAuthParameters($authParameters);
    }

    private function setAuthMethod(string $authMethod)
    {
        $this->authMethod = $authMethod;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthMethod(): string
    {
        return $this->authMethod;
    }

    /**
     * @return mixed
     */
    public function getAuthParameters(): array
    {
        return $this->authParameters;
    }

    /**
     * @param array $authParameters
     * @return $this
     */
    private function setAuthParameters(array $authParameters)
    {
        $this->authParameters = $authParameters;

        return $this;
    }
}
