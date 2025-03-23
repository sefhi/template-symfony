<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command\CreateBook;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateBookCommand implements Command
{
    public function __construct(
        public string $id,
        public string $title,
        public string $author,
        public string $isbn,
        public int $stock,
    ) {
    }
}
