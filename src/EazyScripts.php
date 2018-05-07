<?php

namespace EazyScripts;

use EazyScripts\EazyScriptsException;
use EazyScripts\Http\Request;
use EazyScripts\SearchQuery;
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
        $body = Request::json($body);

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
     * Get a single patients addresses
     *
     * @param  string $id
     * @return EazyScripts\Http\Response
     */
    public function getPatientAddresses($id)
    {
        $request = new Request(sprintf("/patients/%s/addresses", $id), Request::DEFAULT_HEADERS);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get a single patients phone numbers
     *
     * @param  string $id
     * @return EazyScripts\Http\Response
     */
    public function getPatientPhoneNumbers($id)
    {
        $request = new Request(sprintf("/patients/%s/phone-numbers", $id), Request::DEFAULT_HEADERS);

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

        $body = Request::json(array_merge($default, $body));

        $request = new Request("/users", Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->put();
    }

    /**
     * Update an existing users info
     *
     * @param string $id
     * @param array $body
     * @return EazyScripts\Http\Response
     */
    public function updateUserInfo($id, $body)
    {
        $body = Request::json($body);

        $request = new Request(sprintf("/users/%s/info", $id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
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
        $body = Request::json($body);

        $request = new Request(sprintf("/patients/%s/info", $id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Update an existing patients address.
     *
     * @param  string $patientId
     * @param  string $addressId
     * @param  array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePatientAddress($patientId, $addressId, $body)
    {
        $body = Body::json($body);

        $request = new Request(sprintf("/patients/%s/addresses/%s", $patientId, $addressId), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Update an existing patients phone number.
     *
     * @param  string $patientId
     * @param  string $phoneId
     * @param  array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePatientPhoneNumber($patientId, $phoneId, $body)
    {
        $body = Body::json($body);

        $request = new Request(sprintf("/patients/%s/phone-numbers/%s", $patientId, $phoneId), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Get all of the prescriber specialties
     *
     * @param SearchQuery|null $search
     * @return EazyScripts\Http\Response
     */
    public function getPrescriberSpecialties($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/prescribers/specialties", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all of the prescriber specialty
     * qualifier types.
     *
     * @param SearchQuery|null $search
     * @return EazyScripts\Http\Response
     */
    public function getPrescriberSpecialtyQualifiers($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/prescribers/specialty-qualifiers", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all prescribers.
     *
     * @param SearchQuery|null $search
     * @return EazyScripts\Http\Response
     */
    public function getPrescribers($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/prescribers", Request::DEFAULT_HEADERS, $query);

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

        $body = Request::json(array_merge($default, $body));

        $request = new Request("/users", Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->put();
    }

    /**
     * Update a single prescriber.
     *
     * @param  string $id
     * @param  array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePrescriber($id, $body)
    {
        $body = Request::json($body);

        $request = new Request(sprintf("/prescribers/%s/info", $id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Add a new prescriber location for a specific prescriber
     *
     * @param string $prescriber_id
     * @param array  $body
     * @return EazyScripts\Http\Response
     */
    public function addPrescriberLocation($prescriber_id, $body)
    {
        $body = Request::json($body);

        $request = new Request(sprintf("/prescribers/%s/locations", $prescriber_id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->put();
    }

    /**
     * Update a prescribers location.
     *
     * @param  string $prescriber_id
     * @param  string $location_id
     * @param  array $body
     * @return EazyScripts\Http\Response
     */
    public function updatePrescriberLocation($prescriber_id, $location_id, $body)
    {
        $body = Request::json($body);

        $request = new Request(sprintf("/prescribers/%s/locations/%s", $prescriber_id, $location_id), Request::DEFAULT_HEADERS, $body);

        $request->withAuthorization($this->getToken(), true);

        return $request->post();
    }

    /**
     * Get the locations for a specific prescriber.
     *
     * @param  string $prescriber_id
     * @return EazyScripts\Http\Response
     */
    public function getPrescriberLocations($prescriber_id)
    {
        $request = new Request(sprintf("/prescribers/%s/locations", $prescriber_id));

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get the preferred prescriptions for a prescriber
     *
     * @return EazyScripts\Http\Response
     */
    public function getPrescribersPreferredPrescriptions()
    {
        $request = new Request("/prescriber/preferred-prescriptions");

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all of the pharmacies
     *
     * @param  SearchQuery|null $search
     * @return EazyScripts\Http\Response
     */
    public function getPharmacies($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/pharmacies", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get all medicinesaa
     *
     * @param  SearchQuery|null $search
     * @return EazyScripts\Http\Response
     */
    public function getMedicines($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/medicines", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get an auto-login url
     *
     * @param  array  $params
     * @return string
     * @throws EazyScriptsException
     */
    public function getAutoLoginUrl($params = [])
    {
        $request = new Request("/browser/auto-login");

        $query = http_build_query(array_merge([
            "Token"             => $this->getToken(),
            "ApplicationKey"    => $this->key,
            "ApplicationSecret" => $this->secret,
        ], $params));

        return http_build_url($request->getUrl(), [
            "query" => $query,
        ]);
    }

    /**
     * Get the url for the new-prescription browser API call.
     *
     * @param  array  $params
     * @return string
     * @throws EazyScriptsException
     */
    public function getNewPrescriptionUrl($params = [])
    {
        if (!isset($params["PatientId"])) {
            throw new EazyScriptsException("You must provide a PatientId when generating this url");
        }

        $request = new Request("/browser/new-prescription");

        $query = http_build_query(array_merge([
            "Token"             => $this->getToken(),
            "ApplicationKey"    => $this->key,
            "ApplicationSecret" => $this->secret,
        ], $params));

        return http_build_url($request->getUrl(), [
            "query" => $query,
        ]);
    }

    /**
     * Get the url for the prescription refill browser API call.
     *
     * @param  array  $params
     * @return string
     * @throws EazyScriptsException
     */
    public function getRefillUrl($params = [])
    {
        if (!isset($params["PatientId"])) {
            throw new EazyScriptsException("You must provide a PatientId when generating this url");
        }

        if (!isset($params['RefillRequestId'])) {
            throw new EazyScriptsException("You must provide a RefillRequestId when generating this url");
        }

        $request = new Request("/browser/refill");

        $query = http_build_query(array_merge([
            "Token"             => $this->getToken(),
            "ApplicationKey"    => $this->key,
            "ApplicationSecret" => $this->secret,
        ], $params));

        return http_build_url($request->getUrl(), [
            "query" => $query,
        ]);
    }

    /**
     * Get a patients active medications (prescriptions)
     *
     * @param  int $patient_id
     * @param  SearchQuery|null $search
     * @return EazyScripts\Http\Response
     * @throws EazyScriptsException
     */
    public function getActivePatientMedications(int $patient_id, $search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request(sprintf("/patients/%d/prescriptions/active", $patient_id), Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get the pending permissions.
     *
     * @param  SearchQuery|null $search
     * @return EazyScripts\Http\Response
     * @throws EazyScriptsException
     */
    public function getPendingPermissions($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/prescriber/permissions/pendings", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
    }

    /**
     * Get refill requests
     *
     * @param  SearchQuery|null $search
     * @return EazyScripts\Http\Response
     * @throws EazyScriptsException
     */
    public function getRefillRequests($search = null)
    {
        $query = [];

        if (!is_null($search) && $search instanceof SearchQuery) {
            $query = array_merge($query, $search->getRequestQuery());
        }

        $request = new Request("/requests/refills", Request::DEFAULT_HEADERS, $query);

        $request->withAuthorization($this->getToken(), true);

        return $request->get();
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
