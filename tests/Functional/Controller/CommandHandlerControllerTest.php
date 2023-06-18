<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CommandHandlerControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
    }

    /** @test */
    public function itShouldExecuteCommandHandler(): void
    {
        // GIVEN
        $parameters = [
            'foo' => 'foo',
        ];

        // WHEN

        $this->client->request(
            'POST',
            '/api/commands',
            $parameters
        );

        $response = $this->client->getResponse();
        $result   = json_decode($response->getContent());

        // THEN

        self::assertResponseIsSuccessful();
    }
}
