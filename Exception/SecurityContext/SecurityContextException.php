<?php

namespace Alpixel\Bundle\JiraBundle\Exception\SecurityContext;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class SecurityContextException extends \Exception
{
    public function __construct(string $message = '',  array $parameters = [], $code = null, \Exception $previous = null)
    {
        if (empty($message)) {
            $message = 'You must check your credentials parameters or auth method.';
        }

        if (!empty($parameters)) {
            $message = str_replace(['%s'], $parameters, $message);
        }

        parent::__construct($message, $code, $previous);
    }

}
