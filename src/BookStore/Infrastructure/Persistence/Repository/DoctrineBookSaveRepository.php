<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Persistence\Repository;

use App\BookStore\Domain\Entities\Book;
use App\BookStore\Domain\Repositories\BookSaveRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;

final readonly class DoctrineBookSaveRepository extends DoctrineRepository implements BookSaveRepository
{
    public function save(Book $book): void
    {
        $this->persist($book);
    }
}
