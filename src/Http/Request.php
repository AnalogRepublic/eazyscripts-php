<?php 

namespace EazyScripts\Http;

use EazyScripts\Http\Response;
use Unirest\Request as UnirestRequest;

/**
 * Our EazyScripts request wrapper; this layers on top
 * of the Unirest Request logic & passes in anything that
 * we specifically need for EazyScripts requests.
 */
class Request
{       
    /**
     * The subdomain we pass into the path.
     * 
     * @var string
     */
    private static $subdomain = '';

    /**
     * The primary pattern we're using for the api url.
     * 
     * @var string
     */
    protected $path_pattern = "/api/public/v3/%s/%s";

    /**
     * The path we're making a request to.
     * 
     * @var string
     */
    protected $path;

    /**
     * The headers we'll be sending in the requests.
     * 
     * @var array
     */
    protected $headers;

    /**
     * The body we'll be sending in the request.
     * 
     * @var array
     */
    protected $body;

    /**
     * Provide this as the "$headers" parameter
     * in the constructor when you don't want to 
     * set any headers in the request.
     */
    const NO_HEADERS = [];

    /**
     * Pass this in when you want to use default headers
     * in an EazyScripts request.
     */
    const DEFAULT_HEADERS = [
        "Content-Type" => "application/json",
    ];

    /**
     * Create a new request with the path relative to the EazyScripts
     * API url, optionally some headers and some body.
     * 
     * @param string $path
     * @param array  $headers
     * @param array  $body
     */
    public function __construct($path = "/", $headers = self::DEFAULT_HEADERS, $body = [])
    {
        $this->path = $this->buildPath($path);
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Pass the authorization header into the request
     * with a token, by default we'll include the "Bearer" keyword,
     * but the second parameter allows the implementation to disable this.
     * 
     * @param  string  $token
     * @param  boolean $is_bearer
     * @return EazyScripts\Http\Request
     */
    public function withAuthorization($token, $is_bearer = true)
    {
        $this->headers = array_merge([
            "Authorization" => ($is_bearer ? "Bearer " : '') . $token,
        ], $this->headers);

        return $this;
    }

    /**
     * Set the subdomain we're using.
     * 
     * @param string $subdomain
     */
    public static function setSubdomain($subdomain)
    {
        self::$subdomain = $subdomain;
    }

    /**
     * Make a post request given the information that we have.
     * 
     * @return EazyScripts\Http\Response
     */
    public function post()
    {
        $unirestResponse = UnirestRequest::post($this->path, $this->headers, $this->body);

        return new Response($unirestResponse);
    }

    private function buildPath($path)
    {
        return sprintf($this->path_pattern, self::$subdomain, trim($path, '/'));
    }
}