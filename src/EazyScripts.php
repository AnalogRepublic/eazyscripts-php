<?php 

namespace EazyScripts;

use EazyScripts\Http\Request;
use Unirest\Request\Body;

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
        Request::setApplicationKeys($this->key, $this->secret);
    }

    /**
     * Make a call to authenticate a user.
     *
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function authenticate($body)
    {
        $body = Body::json($body);

        $request = new Request("/account/authenticate", Request::DEFAULT_HEADERS, $body);

        return $request->post();
    }

    /**
     * Grab a list of all the patients
     * 
     * @return EazyScripts\Http\Response
     */
    public function getPatients()
    {
        $request = new Request("/patients", Request::DEFAULT_HEADERS, []);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get a single patient record.
     * 
     * @param  string $id
     * @return EazyScripts\Http\Response
     */
    public function getPatient($id)
    {

    }

    /**
     * Add a new patient record.
     * 
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function addPatient($body)
    {
        $body = Body::json($body);

        $request = new Request("/users", Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->put();
    }

    /**
     * Update an existing patient
     *
     * @param string $id
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePatient($id, $body)
    {

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