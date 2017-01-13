<?php

namespace Alpixel\Bundle\JiraBundle\Request;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class BasicAuthentication implements AuthenticationInterface
{
    /**
     * @var  AuthenticationProvider
     */
    private $authenticationProvider;

    /**
     * @var string
     */
    private $credentials;

    public function __construct(AuthenticationProvider $authenticationProvider)
    {
        $this->authenticationProvider = $authenticationProvider;
        $this->setCredentials($authenticationProvider->getAuthParameters());
    }

    public function getAuthenticationProvider(): AuthenticationProvider
    {
        return $this->authenticationProvider;
    }

    public function setCredentials(array $authParameters)
    {
        if (!isset($authParameters['username']) || !isset($authParameters['password'])) {
            throw new \InvalidArgumentException(sprintf('You must set "username", "password" under "auth.parameters" configuration for "alpixel_jira" in your config.yml to use the authentication class "%s"', self::class));
        }

        $credentials = [
            $authParameters['username'],
            $authParameters['password'],
        ];

        $this->credentials = implode(':', $credentials);

        return $this;
    }


    protected function getCredentials()
    {
        return $this->credentials;
    }

    public function applyAuthentication($curlResource)
    {
        curl_setopt($curlResource, CURLOPT_USERPWD, $this->getCredentials());
    }
}
