<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Api\CreateBook;

final readonly class CreateBookRequest
{
    public function __construct(
        public string $title,
        public string $author,
        public string $isbn,
        public int $stock,
    ) {
    }
}
