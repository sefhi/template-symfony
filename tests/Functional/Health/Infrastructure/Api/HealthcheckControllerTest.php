<?php

namespace App\Tests\Functional\Health\Infrastructure\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HealthcheckControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /** @test */
    public function itShouldReturnAnOk(): void
    {
        // GIVEN

        // WHEN

        $this->client
            ->request(
                'GET',
                'api/health',
            );

        $response = $this->client->getResponse();

        // THEN

        self::assertResponseIsSuccessful();
        self::assertEquals('OK', json_decode($response->getContent(), true)['status']);
    }
}
