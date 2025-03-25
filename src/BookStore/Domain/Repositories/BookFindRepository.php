<?php

namespace App\BookStore\Domain\Repositories;

use App\BookStore\Domain\Entities\Book;
use Ramsey\Uuid\UuidInterface;

interface BookFindRepository
{
    public function findById(UuidInterface $id): ?Book;
}
