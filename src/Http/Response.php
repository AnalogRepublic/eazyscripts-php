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
     * When we create the EazyScripts response 
     * wrapper instance we'll pass in the raw Unirest 
     * response so that we can only reveal an API
     * relevant to our EazyScripts implementation.
     * 
     * @param UnirestResponse $response
     */
    public function __construct(UnirestResponse $response)
    {
        $this->response = $response;
    }

    public function getBody()
    {
        return $this->response->body;
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