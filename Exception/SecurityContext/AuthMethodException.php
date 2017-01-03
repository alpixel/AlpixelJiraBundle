<?php

namespace Alpixel\Bundle\JiraBundle\Exception\SecurityContext;

use Alpixel\Bundle\JiraBundle\Request\SecurityContext;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class AuthMethodException extends SecurityContextException
{
    public function __construct()
    {
        $message = "Invalid authentication method, you must use these following method (%s)";
        $methods = implode(', ', SecurityContext::getAuthMethods());

        parent::__construct($message, [$methods]);
    }
}
