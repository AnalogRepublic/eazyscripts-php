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

        $this->assertNotFalse($response->getToken());
    }
}
