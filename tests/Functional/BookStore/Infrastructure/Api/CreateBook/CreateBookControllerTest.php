<?php

declare(strict_types=1);

namespace Tests\Functional\BookStore\Infrastructure\Api\CreateBook;

use PHPUnit\Framework\Attributes\Test;
use Tests\Functional\BaseApiTestCase;

final class CreateBookControllerTest extends BaseApiTestCase
{
    #[Test]
    public function itShouldCreateABook(): void
    {
        // GIVEN

        $payload = [
            'title'  => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
            'isbn'   => '978-0544003415',
            'stock'  => 1,
        ];

        // WHEN

        $this->client()
            ->request(
                'POST',
                'api/books',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );

        $this->client()->getResponse();

        // THEN

        self::assertResponseIsSuccessful();
    }
}
