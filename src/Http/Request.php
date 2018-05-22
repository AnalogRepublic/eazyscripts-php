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
     * The application key we pass into the headers
     *
     * @var string
     */
    private static $app_key = '';

    /**
     * The application secret we pass into the headers
     *
     * @var string
     */
    private static $app_secret = '';

    /**
     * The primary pattern we're using for the api url.
     *
     * @var string
     */
    protected $path_pattern = "https://%s.eazyscripts.com/api/public/v3/%s/%s";

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
        "Content-Type" => "application/json;charset=utf-8",
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

        $this->headers = array_merge([
            "ApplicationKey"    => self::$app_key,
            "ApplicationSecret" => self::$app_secret,
        ], $headers);

        $this->body = $body;

        $this->verifySSLCertificate((bool) env('VERIFY_SSL_CERT', true));
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
     * Set the application keys.
     *
     * @param string $key
     * @param string $secret
     */
    public static function setApplicationKeys($key, $secret)
    {
        self::$app_key = $key;
        self::$app_secret = $secret;
    }

    /**
     * Encode the body as json & encode the data so that it's
     * suitable to be sent up to the API.
     *
     * @param  object|array $what
     * @return string
     */
    public static function json($what)
    {
        return json_encode(encode_request_data($what), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Make a post request given the information that we have.
     *
     * @return EazyScripts\Http\Response
     */
    public function post()
    {
        $unirestResponse = UnirestRequest::post($this->path, $this->headers, $this->body);

        return new Response($unirestResponse, $this->path);
    }

    /**
     * Make a get request given the information that we have.
     *
     * @return EazyScripts\Http\Response
     */
    public function get()
    {
        $unirestResponse = UnirestRequest::get($this->path, $this->headers, $this->body);

        return new Response($unirestResponse, $this->path);
    }

    /**
     * Make a put request given the information that we have.
     *
     * @return EazyScripts\Http\Response
     */
    public function put()
    {
        $unirestResponse = UnirestRequest::put($this->path, $this->headers, $this->body);

        return new Response($unirestResponse, $this->path);
    }

    /**
     * Get the url we're going to be requesting.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->path;
    }

    /**
     * Set the option to verify the SSL certificate on APIs
     * that we interact with.
     *
     * @param  bool $verify
     * @return void
     */
    public function verifySSLCertificate($verify)
    {
        UnirestRequest::verifyPeer($verify);
    }

    private function buildPath($path)
    {
        return sprintf($this->path_pattern, self::$subdomain, self::$subdomain, trim($path, '/'));
    }
}
