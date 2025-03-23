<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Api\CreateBook;

use App\BookStore\Application\Command\CreateBook\CreateBookCommand;
use Ramsey\Uuid\Uuid;

final readonly class CreateBookRequest
{
    public function __construct(
        public string $title,
        public string $author,
        public string $isbn,
        public int $stock,
    ) {
    }

    public function mapToCreateBookCommand(): CreateBookCommand
    {
        return new CreateBookCommand(
            id: Uuid::uuid7()->toString(),
            title: $this->title,
            author: $this->author,
            isbn: $this->isbn,
            stock: $this->stock,
        );
    }
}
