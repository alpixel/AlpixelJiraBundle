<?php

namespace Alpixel\Bundle\JiraBundle\Request;

use Alpixel\Bundle\JiraBundle\Exception\SecurityContext\AuthMethodException;
use Symfony\Component\OptionsResolver\OptionsResolver;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class SecurityContext
{
    const METHOD_BASIC = 'basic';
    const METHOD_OAUTH = 'oauth';

    private $authMethod;
    private $credentials;

    public function __construct(array $auth)
    {
        $resolver = new OptionsResolver();
        $this->configureAuthMethod($resolver);
        $resolver->resolve($auth);
        $this->authMethod = strtolower(key($auth['method']));

        $resolver = new OptionsResolver();
        $this->configureCredentials($resolver, $this->getAuthMethod());
        $this->credentials = $resolver->resolve($auth['method'][$this->getAuthMethod()]);
    }

    public static function getAvailablesAuthMethods()
    {
        return [
            self::METHOD_BASIC,
            self::METHOD_OAUTH,
        ];
    }

    protected function configureAuthMethod(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('method')
            ->setAllowedTypes('method', 'array')
            ->setAllowedValues('method', function ($value) {
                $method = strtolower(key($value));

                if (!in_array($method, self::getAvailablesAuthMethods())) {
                    throw new AuthMethodException();
                }

                return $method;
            });
    }

    protected function configureCredentials(OptionsResolver $resolver, string $authMethod)
    {
        if ($authMethod === self::METHOD_BASIC)  {
            $resolver
                ->setRequired('username')
                ->setAllowedTypes('username', 'string')
                ->setRequired('password')
                ->setAllowedTypes('password', 'string');
        } else if ($authMethod === self::METHOD_OAUTH) {
            $resolver
                ->setRequired('id')
                ->setAllowedTypes('id', 'string')
                ->setRequired('key')
                ->setAllowedTypes('key', 'string');
        } else {
            throw new AuthMethodException();
        }
    }

    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }
}
