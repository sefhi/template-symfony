<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Entities;

use Ramsey\Uuid\Uuid;
use Tests\Unit\BookStore\Application\Command\CreateBookCommand;

final class BookMother
{
    public static function random(array $overrides = []): Book
    {
        $randomData = [
            'id'     => Uuid::uuid7()->toString(),
            'title'  => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
            'isbn'   => '978-0544003415',
            'stock'  => rand(0, 100),
        ];

        $finalData = array_merge($randomData, $overrides);

        return Book::create(
            id: $finalData['id'],
            title: $finalData['title'],
            author: $finalData['author'],
            isbn: $finalData['isbn'],
            stock: $finalData['stock'],
        );
    }

    public static function fromCommand(CreateBookCommand $command): Book
    {
        return self::random(
            [
                'title'  => $command->title(),
                'author' => $command->author(),
                'isbn'   => $command->isbn(),
                'stock'  => $command->stock(),
            ]
        );
    }
}
