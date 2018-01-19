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
    protected static $specialty_id;
    protected static $qualifier_id;

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
                getenv('EAZYSCRIPTS_KEY'),
                getenv('EAZYSCRIPTS_SECRET'),
                getenv('EAZYSCRIPTS_SUBDOMAIN')
            )
        );
    }

    public function testCanAuthenticate()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $response = $api->authenticate([
            'Email'        => getenv('EAZYSCRIPTS_EMAIL'),
            'Password'     => getenv('EAZYSCRIPTS_PASSWORD'),
            'Subdomain'    => getenv('EAZYSCRIPTS_SUBDOMAIN'),
            'PlatformType' => EazyScripts::PLATFORM_SERVER,
        ]);

        self::$token = $response->getToken();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
        $this->assertNotFalse(self::$token);
    }

    public function testCanAddPatient()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
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

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());

        $this->assertObjectHasAttribute('id', $response->getBody());

        self::$patient_id = $response->getBody()->id;
        self::$patient_address_id = $response->getBody()->Patient->HomeAddress;
        self::$patient_phone_id = $response->getBody()->Patient->HomePhoneNumber;
    }

    public function testCanGetPatients()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatients();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetPatient()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPatient(self::$patient_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanUpdatePatient()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePatient(self::$patient_id, [
            "Email" => time() . "testing+patientUpdated@testemail.com",
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function canUpdatePatientAddress()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
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
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePatientPhone(self::$patient_id, self::$patient_phone_id, [
            "Number"    => "4155552672",
            "Extension" => "+1",
            "Type"      => EazyScripts::TYPE_HOME,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetPrescriberSpecialties()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberSpecialties();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());

        $this->assertNotEmpty($response->getBody());

        self::$specialty_id = $response->getBody()[0]->value;
    }

    public function testCanGetPrescriberSpecialtyQualifiers()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberSpecialtyQualifiers();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());

        $this->assertNotEmpty($response->getBody());

        self::$qualifier_id = $response->getBody()[0]->value;
    }

    public function testCanAddPrescriber()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->addPrescriber([
            "FirstName"   => "Testing",
            "LastName"    => "Doctor",
            "Email"       => time() . "testing+doctor@testemail.com",
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
                    "NewRx"               => false,
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

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());

        $this->assertObjectHasAttribute('id', $response->getBody());

        self::$prescriber_id = $response->getBody()->id;
    }

    public function testCanGetPrescribers()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescribers();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetPrescriber()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriber(self::$prescriber_id);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanUpdatePrescriber()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->updatePrescriber(self::$prescriber_id, [
            "Npi"                => "1234567890",
            "Specialty"          => self::$specialty_id,
            "SpecialtyQualifier" => self::$qualifier_id,
        ]);

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetPharmacies()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPharmacies();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanSearchMedicines()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getMedicines(new SearchQuery("Advil", 1, 0));

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanAddPrescriberLocation()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        // TODO: Work out why this isn't working....
        $response = $api->addPrescriberLocation(self::$prescriber_id, [
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
                "NewRx"               => false,
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
        ]);

        var_dump($response);

        die();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetPrescriberLocations()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        $response = $api->getPrescriberLocations();

        $this->assertObjectNotHasAttribute('error', (object)$response->getBody());
        $this->assertObjectNotHasAttribute('errors', (object)$response->getBody());
    }

    public function testCanGetNewPrescriptionUrl()
    {
        $api = new EazyScripts(
            getenv('EAZYSCRIPTS_KEY'),
            getenv('EAZYSCRIPTS_SECRET'),
            getenv('EAZYSCRIPTS_SUBDOMAIN')
        );

        $api->setToken(self::$token);

        try {
            $url = $api->getNewPrescriptionUrl([
                "PatientId" => self::$patient_id,
            ]);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }

        $this->assertTrue(!empty($url));
    }
}
