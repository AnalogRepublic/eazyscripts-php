<?php 

namespace Tests;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use EazyScripts\EazyScripts;
use EazyScripts\EazyScriptsException;

/**
 * @covers EazyScripts\EazyScripts
 */
final class EazyScriptsTest extends TestCase
{
    protected static $token;

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
            'PlatformType' => 'SERVER',
            'Subdomain'    => getenv('EAZYSCRIPTS_SUBDOMAIN')
        ]);

        self::$token = $response->getToken();

        $this->assertObjectNotHasAttribute('error', $response->getBody());
        $this->assertObjectNotHasAttribute('errors', $response->getBody());
        $this->assertNotFalse(self::$token);
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

        $this->assertObjectNotHasAttribute('error', $response->getBody());
        $this->assertObjectNotHasAttribute('errors', $response->getBody());
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
            "Level"       => 3,
            "FirstName"   => "Testing",
            "LastName"    => "Patient",
            "Email"       => "testing+patient@testemail.com",
            "Password"    => "pa55word",
            "DateOfBirth" => "1970-1-1",
            "Gender"      => 1,
            "Patient"     => [
                "HomeAddress" => [
                    "Address1" => "123 Test Road",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "60654",
                    "Type"     => 1,
                ],
                "WorkAddress" => [
                    "Address1" => "123 Test Road",
                    "City"     => "San Diego",
                    "State"    => "CA",
                    "Country"  => "USA",
                    "Zip"      => "60654",
                    "Type"     => 2,
                ],
                "HomePhoneNumber" => [
                    "Number"    => "0000000000",
                    "Extension" => "+1",
                    "Type"      => 1,
                ],
                "WorkPhoneNumber" => [
                    "Number"    => "0000000000",
                    "Extension" => "+1",
                    "Type"      => 2,
                ],
            ],
        ]);

        $this->assertObjectNotHasAttribute('error', $response->getBody());
        $this->assertObjectNotHasAttribute('errors', $response->getBody());
    }

    // public function testCanGetPatient()
    // {

    // }

    // public function testCanUpdatePatient()
    // {

    // }
}
