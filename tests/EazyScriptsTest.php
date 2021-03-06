<?php

namespace Tests;

use Dotenv\Dotenv;
use EazyScripts\EazyScripts;
use EazyScripts\EazyScriptsException;
use EazyScripts\SearchQuery;
use PHPUnit\Framework\TestCase;

/**
 * @covers EazyScripts\EazyScripts
 */
final class EazyScriptsTest extends TestCase
{
    protected static $token;
    protected static $patient_id;
    protected static $patient_address_id;
    protected static $patient_phone_id;
    protected static $prescriber_id;
    protected static $prescriber_email;
    protected static $specialty_id;
    protected static $qualifier_id;
    protected static $refill_request_id;

    public function setUp()
    {
        parent::setUp();

        // Load in env from .env.testing
        $dotenv = new Dotenv(__DIR__ . '/../', '.env.testing');
        $dotenv->load();
    }

    public function testCanBeCreatedWithValidCredentials()
    {
        $this->assertInstanceOf(
            EazyScripts::class,
            new EazyScripts(
                env('EAZYSCRIPTS_KEY'),
                env('EAZYSCRIPTS_SECRET'),
                env('EAZYSCRIPTS_SUBDOMAIN')
            )
        );
    }

    public function testCanAuthenticate()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => env('EAZYSCRIPTS_EMAIL'),
            'Password'     => env('EAZYSCRIPTS_PASSWORD'),
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        self::$token = $response->getToken();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
        $this->assertNotFalse(self::$token);
    }

    public function testCanAddPatient()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->addPatient([
            "FirstName"   => "Testing",
            "LastName"    => "Patient",
            "Email"       => time() . "testing+patient@testemail.com",
            "Password"    => "pa55word",
            "DateOfBirth" => "1970-2-1",
            "Gender"      => EazyScripts::GENDER_FEMALE,
            "Patient"     => [
                "HomeAddress" => [
                    "Address1" => "123 Test Road",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "60654",
                    "Type"     => EazyScripts::TYPE_HOME,
                ],
                "WorkAddress" => [
                    "Address1" => "123 Test Road",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "60654",
                    "Type"     => EazyScripts::TYPE_WORK,
                ],
                "HomePhoneNumber" => [
                    "Number"    => "4155552671",
                    "Extension" => "+1",
                    "Type"      => EazyScripts::TYPE_HOME,
                ],
                "WorkPhoneNumber" => [
                    "Number"    => "4155552671",
                    "Extension" => "+1",
                    "Type"      => EazyScripts::TYPE_WORK,
                ],
            ],
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $this->assertObjectHasAttribute('id', $response->getBody());

        self::$patient_id = $response->getBody()->id;
    }

    public function testCanGetPatients()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatients();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPatient()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatient(self::$patient_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPatientAddresses()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatientAddresses(self::$patient_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        self::$patient_address_id = current($response->getBody())->id;
    }

    public function testCanGetPatientPhoneNumbers()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatientPhoneNumbers(self::$patient_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        self::$patient_phone_id = current($response->getBody())->id;
    }

    public function testCanUpdatePatient()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePatient(self::$patient_id, [
            "OtherGenderIdentity" => "female",
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());

        $this->assertTrue($response->getBody()->otherGenderIdentity == "female", "The 'otherGenderIdentity' field must have updated to the value we've set.");
    }

    public function testCanUpdateUserInfo()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updateUserInfo(self::$patient_id, [
            "FirstName"   => "Testing",
            "MiddleName"  => "Update",
            "LastName"    => "User",
            "Email"       => time() . "testing+patient_updated@testemail.com",
            "Password"    => "pa55word",
            "DateOfBirth" => "1971-2-1",
            "Gender"      => EazyScripts::GENDER_MALE,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function canUpdatePatientAddress()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePatientAddress(self::$patient_id, self::$patient_address_id, [
            "Address1" => "123 Test Road Updated",
            "City"     => "San Diego",
            "State"    => "CA",
            "Country"  => "USA",
            "Zip"      => "60654",
            "Type"     => EazyScripts::TYPE_HOME,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function canUpdatePatientPhoneNumber()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePatientPhone(self::$patient_id, self::$patient_phone_id, [
            "Number"    => "4155552672",
            "Extension" => "+1",
            "Type"      => EazyScripts::TYPE_HOME,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPrescriberSpecialties()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberSpecialties();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $this->assertNotEmpty($response->getBody());

        self::$specialty_id = $response->getBody()[0]->value;
    }

    public function testCanGetPrescriberSpecialtyQualifiers()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberSpecialtyQualifiers();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $this->assertNotEmpty($response->getBody());

        self::$qualifier_id = $response->getBody()[0]->value;
    }

    public function testCanAddPrescriber()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        self::$prescriber_email = time() . "testing+doctor@testemail.com";

        $response = $api->addPrescriber([
            "FirstName"   => "Weiß",
            "LastName"    => "Gäben",
            "Email"       => self::$prescriber_email,
            "Password"    => "pa55word",
            "DateOfBirth" => "1970-3-1",
            "Gender"      => EazyScripts::GENDER_MALE,
            "Prescriber"  => [
                "Npi"                => "1234567890",
                "Specialty"          => self::$specialty_id,
                "SpecialtyQualifier" => self::$qualifier_id,
                "ClinicName"         => "Test Clinic",
                "Address"            => [
                    "Type"     => EazyScripts::TYPE_WORK,
                    "Address1" => "555 Noah Way",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "92117",
                ],
                "Permissions" => [
                    "NewRx"               => true,
                    "Refill"              => false,
                    "Change"              => false,
                    "Cancel"              => false,
                    "ControlledSubstance" => false,
                ],
                "PhoneNumbers" => [
                    [
                        "Number"    => "4155552671",
                        "Extension" => "+1",
                        "Type"      => EazyScripts::TYPE_WORK,
                    ],
                    [
                        "Number"    => "4155552671",
                        "Extension" => "+1",
                        "Type"      => EazyScripts::TYPE_FAX,
                    ]
                ],
            ],
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $this->assertObjectHasAttribute('id', $response->getBody());

        self::$prescriber_id = $response->getBody()->id;
    }

    public function testCanAddEPCSPrescriber()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $prescriber_email = time() . "testing+epcsdoctor@testemail.com";

        $response = $api->addPrescriber([
            "FirstName"   => "EPCS",
            "LastName"    => "Doctor",
            "Email"       => $prescriber_email,
            "Password"    => "pa55word",
            "DateOfBirth" => "1970-5-1",
            "Gender"      => EazyScripts::GENDER_MALE,
            "Prescriber"  => [
                "Npi"                           => "1234567891",
                "Specialty"                     => self::$specialty_id,
                "SpecialtyQualifier"            => self::$qualifier_id,
                "ClinicName"                    => "Test Clinic 2",
                "Address"                       => [
                    "Type"     => EazyScripts::TYPE_WORK,
                    "Address1" => "555 Noah Way",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "92117",
                ],
                "Permissions" => [
                    "NewRx"               => true,
                    "Refill"              => false,
                    "Change"              => false,
                    "Cancel"              => true,
                    "ControlledSubstance" => true,
                ],
                "PhoneNumbers" => [
                    [
                        "Number"    => "4155552671",
                        "Extension" => "+1",
                        "Type"      => EazyScripts::TYPE_WORK,
                    ],
                    [
                        "Number"    => "4155552671",
                        "Extension" => "+1",
                        "Type"      => EazyScripts::TYPE_FAX,
                    ]
                ],
            ],
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $this->assertObjectHasAttribute('id', $response->getBody());
    }

    public function testCanGetPrescribers()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescribers();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPrescriber()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriber(self::$prescriber_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanUpdatePrescriber()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePrescriber(self::$prescriber_id, [
            "Npi"                => "1234567890",
            "Specialty"          => self::$specialty_id,
            "SpecialtyQualifier" => self::$qualifier_id,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanAdd2FAUserId()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePrescriber(self::$prescriber_id, [
            "Npi"                           => "1234567890",
            "Specialty"                     => self::$specialty_id,
            "SpecialtyQualifier"            => self::$qualifier_id,
            "TwoFactorAuthenticationUserId" => self::$prescriber_email,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPendingPermissions()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        $response = $api->getPendingPermissions();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPharmacies()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPharmacies();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPharmaciesByZip()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPharmacies(new SearchQuery("78223"));

        $body = (object)$response->getBody();

        $this->assertObjectNotHasAttribute('error', $body, "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', $body, "We should not have received any errors");

        $this->assertFalse(strpos($body->data[0]->address->zip, "78223") === false);
    }

    public function testCanGetPharmaciesByCity()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPharmacies(new SearchQuery("San Antonio"));

        $body = (object)$response->getBody();

        $this->assertObjectNotHasAttribute('error', $body, "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', $body, "We should not have received any errors");

        $this->assertFalse(strpos(strtolower($body->data[0]->address->city), "san antonio") === false);
    }

    public function testCanSearchMedicines()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getMedicines(new SearchQuery("Advil", 1, 0));

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    // public function testCanAddPrescriberLocation()
    // {
    //     $api = new EazyScripts(
    //         env('EAZYSCRIPTS_KEY'),
    //         env('EAZYSCRIPTS_SECRET'),
    //         env('EAZYSCRIPTS_SUBDOMAIN')
    //     );

    //     $api->setToken(self::$token);

    //     // TODO: Work out why this isn't working....
    //     $response = $api->addPrescriberLocation(self::$prescriber_id, [
    //         "ClinicName"         => "Test Clinic " . time(),
    //         "Address"            => [
    //             "Type"     => EazyScripts::TYPE_WORK,
    //             "Address1" => "556 Noah Way",
    //             "City"     => "San Diego",
    //             "State"    => "CA",
    //             "Country"  => "USA",
    //             "Zip"      => "92118",
    //         ],
    //         "Permissions" => [
    //             "NewRx"               => false,
    //             "Refill"              => false,
    //             "Change"              => false,
    //             "Cancel"              => false,
    //             "ControlledSubstance" => false,
    //         ],
    //         "PhoneNumbers" => [
    //             [
    //                 "Number"    => "4155552673",
    //                 "Extension" => "+1",
    //                 "Type"      => EazyScripts::TYPE_WORK,
    //             ],
    //             [
    //                 "Number"    => "4155552673",
    //                 "Extension" => "+1",
    //                 "Type"      => EazyScripts::TYPE_FAX,
    //             ]
    //         ],
    //     ]);

    //     $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
    //     $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    // }

    public function testCanGetPrescriberLocations()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberLocations(self::$prescriber_id);

        $message = "We should not have received any errors";
        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), $message);
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), $message);

        $this->assertGreaterThanOrEqual(1, count($response->getBody()), "We should have at least 1 location returned");
    }

    public function testCanGetNewPrescriptionUrl()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        try {
            // Grab a url
            $url = $api->getNewPrescriptionUrl([
                "PatientId" => self::$patient_id,
            ]);
        } catch (\Exception $e) {
            $this->assertTrue(false, "An error should not have occured when generating a url");
        }

        // Make sure we got a url
        $this->assertTrue(!empty($url), "A url should have been generated");

        // Then check to see if the url we've generated is valid.
        $response = \Unirest\Request::get($url);
        $errored = isset($response->headers["Location"]) && strpos($response->headers["Location"], "error?") > -1;

        $this->assertFalse((bool) $errored, "We should have generated a valid url");
    }

    public function testCanGetRefillRequests()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        $response = $api->getRefillRequests();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");

        $body = $response->getBody();

        if (isset($body->data) && count($body->data) > 0) {
            $this->assertTrue(count($body->data) > 0, "We should have received some requests");
            $request = current($body->data);
            self::$refill_request_id = $request->id;
        }
    }

    public function testCanGetRefillUrl()
    {
        $this->assertTrue(!is_null(self::$refill_request_id), "We should have a refill request to generate a url for");

        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        try {
            // Grab a url
            $url = $api->getRefillUrl([
                "PatientId"       => self::$patient_id,
                "RefillRequestId" => self::$refill_request_id,
            ]);
        } catch (\Exception $e) {
            $this->assertTrue(false, "An error should not have occured when generating a url");
        }

        // Make sure we got a url
        $this->assertTrue(!empty($url), "A url should have been generated");

        // Then check to see if the url we've generated is valid.
        $response = \Unirest\Request::get($url);
        $errored = isset($response->headers["Location"]) && strpos($response->headers["Location"], "error?") > -1;

        $this->assertFalse((bool) $errored, "We should have generated a valid url");
    }

    public function testCanGetAutoLoginUrll()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        try {
            // Grab a url
            $url = $api->getAutoLoginUrl();
        } catch (\Exception $e) {
            $this->assertTrue(false, "An error should not have occured when generating a url");
        }

        // Make sure we got a url
        $this->assertTrue(!empty($url), "A url should have been generated");

        // Then check to see if the url we've generated is valid.
        $response = \Unirest\Request::get($url);
        $errored = isset($response->headers["Location"]) && strpos($response->headers["Location"], "error?") > -1;

        $this->assertFalse((bool) $errored, "We should have generated a valid url");
    }

    public function testCanGetActivePatientMedications()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getActivePatientMedications(self::$patient_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }

    public function testCanGetPrescriberPreferredPrescriptions()
    {
        $api = new EazyScripts(
            env('EAZYSCRIPTS_KEY'),
            env('EAZYSCRIPTS_SECRET'),
            env('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => self::$prescriber_email,
            'Password'     => 'pa55word',
            'Subdomain'    => env('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        $api->setToken($response->getBody()->token);

        $response = $api->getPrescribersPreferredPrescriptions();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody(), "We should not have received any errors");
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody(), "We should not have received any errors");
    }
}
