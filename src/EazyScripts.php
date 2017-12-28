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

    const USER_LEVEL_DOCTOR = 2;
    const USER_LEVEL_PATIENT = 3;

    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    const TYPE_HOME = 1;
    const TYPE_WORK = 2;
    const TYPE_FAX = 3;

    const PLATFORM_SERVER = "SERVER";

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
        $request = new Request(sprintf("/patients/%s/info", $id), Request::DEFAULT_HEADERS);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Add a new patient record.
     * 
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function addPatient($body)
    {
        $default = [
            "Level" => self::USER_LEVEL_PATIENT,
        ];

        $body = Body::json(array_merge($default, $body));

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
        $body = Body::json($body);

        $request = new Request(sprintf("/patients/%s/info", $id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Get all of the prescriber specialties
     * 
     * @return EazyScripts\Http\Response
     */
    public function getPrescriberSpecialties()
    {
        $request = new Request("/prescribers/specialties");

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all of the prescriber specialty 
     * qualifier types.
     * 
     * @return EazyScripts\Http\Response
     */
    public function getPrescriberSpecialtyQualifiers()
    {
        $request = new Request("/prescribers/specialty-qualifiers");

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all prescribers.
     * 
     * @return EazyScripts\Http\Response
     */
    public function getPrescribers()
    {
        $request = new Request("/prescribers");

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get a single prescriber.
     * 
     * @param  string $id
     * @return EazyScripts\Http\Response
     */
    public function getPrescriber($id)
    {
        $request = new Request(sprintf("/prescribers/%s/info", $id));

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Create a new prescriber.
     * 
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function addPrescriber($body)
    {   
        $default = [
            "Level" => self::USER_LEVEL_DOCTOR,
        ];

        $body = Body::json(array_merge($default, $body));

        $request = new Request("/users", Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->put();
    }

    /**
     * Updated a single prescriber.
     * 
     * @param  string $id
     * @param  array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePrescriber($id, $body)
    {
        $body = Body::json($body);

        $request = new Request(sprintf("/prescribers/%s/info", $id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

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