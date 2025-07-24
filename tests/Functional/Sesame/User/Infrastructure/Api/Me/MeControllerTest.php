<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\User\Infrastructure\Api\Me;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;

final class MeControllerTest extends BaseApiTestCase
{
    #[Test]
    public function itShouldReturnCurrentUser(): void
    {
        // GIVEN
        // The BaseApiTestCase already sets up an authenticated admin user

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/users/me',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        self::assertArrayHasKey('id', $content);
        self::assertArrayHasKey('name', $content);
        self::assertArrayHasKey('email', $content);
        self::assertArrayHasKey('createdAt', $content);
        self::assertArrayHasKey('updatedAt', $content);

        self::assertEquals($this->getUserLogged()->id()->toString(), $content['id']);
        self::assertEquals($this->getUserLogged()->nameValue(), $content['name']);
        self::assertEquals($this->getUserLogged()->emailValue(), $content['email']);
    }
}
