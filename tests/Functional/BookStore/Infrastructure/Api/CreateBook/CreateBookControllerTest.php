<?php

declare(strict_types=1);

namespace Tests\Functional\BookStore\Infrastructure\Api\CreateBook;

use PHPUnit\Framework\Attributes\Test;
use Tests\Functional\BaseApiTestCase;
use Tests\Utils\MotherCreator;

final class CreateBookControllerTest extends BaseApiTestCase
{
    #[Test]
    public function itShouldCreateABook(): void
    {
        // GIVEN

        $payload = [
            'title'  => MotherCreator::title(),
            'author' => MotherCreator::author(),
            'isbn'   => MotherCreator::isbn(),
            'stock'  => MotherCreator::stock(),
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
