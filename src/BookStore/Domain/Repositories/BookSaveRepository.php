<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Repositories;

use App\BookStore\Domain\Entities\Book;

interface BookSaveRepository
{
    public function save(Book $book): void;
}
