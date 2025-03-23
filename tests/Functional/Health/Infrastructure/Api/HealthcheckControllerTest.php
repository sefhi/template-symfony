<?php

namespace Tests\Functional\Health\Infrastructure\Api;

use PHPUnit\Framework\Attributes\Test;
use Tests\Functional\BaseApiTestCase;

class HealthcheckControllerTest extends BaseApiTestCase
{
    #[Test]
    public function itShouldReturnAnOk(): void
    {
        // GIVEN

        // WHEN

        $this->client()
            ->request(
                'GET',
                'api/health',
            );

        $response = $this->client()->getResponse();

        // THEN

        self::assertResponseIsSuccessful();
        self::assertEquals('OK', json_decode($response->getContent(), true)['status']);
    }
}
