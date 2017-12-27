<?php 

namespace EazyScripts;

use EazyScripts\Http\Request;

/**
 * The primary handler for the EazyScripts
 * API, this is how we interact with it.
 */
class EazyScripts
{   
    /**
     * Our API key which we need to access the API.
     * 
     * @var string
     */
    private $key;

    /**
     * The secret key for our application.
     * 
     * @var string
     */
    private $secret;

    /**
     * The subdomain our applications account resides on.
     * 
     * @var string
     */
    private $subdomain;

    /**
     * The token we're required to obtain and pass to the
     * API calls when we're fetching user information.
     * 
     * @var string
     */
    private $token;

    /**
     * Create our EazyScripts API handler with the required
     * API credentials.
     * 
     * @param string $key
     * @param string $secret
     * @param string $subdomain
     */
    public function __construct($key, $secret, $subdomain)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->subdomain = $subdomain;

        Request::setSubdomain($this->subdomain);
    }

    /**
     * Make a call to authenticate a user.
     * 
     * @return EazyScripts\Http\Response
     */
    public function authenticate()
    {
        $request = new Request("/account/authenticate", [], []);

        return $request->post();
    }

    /**
     * Get the token we are using to authenticate
     * our API requests.
     * 
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the token, any subsequent requests will use
     * this token in the request.
     * 
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
}