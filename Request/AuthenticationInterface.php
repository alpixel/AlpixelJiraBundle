<?php

namespace Alpixel\Bundle\JiraBundle\Request;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
interface AuthenticationInterface
{
    /**
     * @param resource (curl) $curlRessource
     * @return mixed
     */
    public function applyAuthentication($curlRessource);
}
