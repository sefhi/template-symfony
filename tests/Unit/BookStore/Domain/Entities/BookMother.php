<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Domain\Entities;

use App\BookStore\Application\Command\CreateBook\CreateBookCommand;
use App\BookStore\Domain\Entities\Book;
use Tests\Utils\MotherCreator;

final class BookMother
{
    public static function random(array $overrides = []): Book
    {
        $randomData = [
            'id'     => MotherCreator::id(),
            'title'  => MotherCreator::title(),
            'author' => MotherCreator::author(),
            'isbn'   => MotherCreator::isbn(),
            'stock'  => MotherCreator::stock(),
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
                'id'     => $command->id,
                'title'  => $command->title,
                'author' => $command->author,
                'isbn'   => $command->isbn,
                'stock'  => $command->stock,
            ]
        );
    }
}
