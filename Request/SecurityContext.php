<?php

namespace Alpixel\Bundle\JiraBundle\Request;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class SecurityContext
{
    /**
     * @var string
     */
    private $authClassName;

    /**
     * @var AuthenticationInterface
     */
    private $authObject;

    public function __construct(string $authClassName, AuthenticationProvider $authProvider)
    {
        $this->authClassName = $authClassName;
        $this->authObject = $this->createAuthObject($authClassName, $authProvider);
    }

    /**
     * @return string
     */
    public function getAuthClassName(): string
    {
        return $this->authClassName;
    }

    /**
     * @return AuthenticationInterface
     */
    public function getAuthObject(): AuthenticationInterface
    {
        return $this->authObject;
    }

    /**
     * @param string $authClassName
     * @param AuthenticationProvider $authProvider
     * @return object
     */
    private function createAuthObject(string $authClassName, AuthenticationProvider $authProvider)
    {
        return new $authClassName($authProvider);
    }


    public function applyAuthentication($curlResource)
    {
        $this->getAuthObject()->applyAuthentication($curlResource);

        return $this;
    }
}
