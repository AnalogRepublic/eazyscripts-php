<?php

namespace EazyScripts\Http;

use Unirest\Response as UnirestResponse;

/**
 * Represent the response we get back from the
 * API requests.
 */
class Response
{
    /**
     * Store the actual response object we have.
     *
     * @var Unirest\Response (aliased to UnirestResponse)
     */
    protected $response;

    /**
     * The url we made a request to in order
     * to get this response.
     *
     * @var string
     */
    public $url;

    /**
     * When we create the EazyScripts response
     * wrapper instance we'll pass in the raw Unirest
     * response so that we can only reveal an API
     * relevant to our EazyScripts implementation.
     *
     * @param UnirestResponse $response
     * @param string          $url
     */
    public function __construct(UnirestResponse $response, $url)
    {
        $this->response = $response;
        $this->url = $url;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getHeaders()
    {
        return $this->getResponse()->getHeaders();
    }

    public function getRawBody()
    {
        return $this->getResponse()->raw_body;
    }

    public function getBody()
    {
        return $this->getResponse()->body;
    }

    public function getCode()
    {
        return $this->getResponse()->code;
    }

    public function getToken()
    {
        if (!isset($this->getBody()->token)) {
            return false;
        }

        return $this->getBody()->token;
    }

    public function getErrors()
    {
        if (!isset($this->getBody()->errors)) {
            return [];
        }

        return $this->getBody()->errors;
    }
}
