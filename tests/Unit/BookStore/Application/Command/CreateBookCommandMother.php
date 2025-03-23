<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Command;

use Ramsey\Uuid\Uuid;

final class CreateBookCommandMother
{
    public static function random(array $overrides = []): CreateBookCommand
    {
        $randomData = [
            'id'     => Uuid::uuid7()->toString(),
            'title'  => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
            'isbn'   => '978-0544003415',
            'stock'  => rand(0, 100),
        ];

        $finalData = array_merge($randomData, $overrides);

        return new CreateBookCommand(
            title: $finalData['title'],
            author: $finalData['author'],
            isbn: $finalData['isbn'],
            stock: $finalData['stock'],
        );
    }
}
