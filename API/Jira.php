<?php

namespace Alpixel\Bundle\JiraBundle\API;

use Alpixel\Bundle\JiraBundle\Request\Request;
use Alpixel\Bundle\JiraBundle\Response\Response;


/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class Jira
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    private function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    public function search(string $jql, array $parameters = [])
    {
        $options = array_replace([
            'jql' => $jql
        ], $parameters);

        return $this->get('search', $options);
    }

    public function get(string $uri, array $parameters = [])
    {
        $this->setResponse($this->getRequest()->get($uri, $parameters));

        return $this->getResponse();
    }
}
