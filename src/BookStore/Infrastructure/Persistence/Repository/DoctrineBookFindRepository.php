<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Persistence\Repository;

use App\BookStore\Domain\Entities\Book;
use App\BookStore\Domain\Repositories\BookFindRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;
use Ramsey\Uuid\UuidInterface;

final readonly class DoctrineBookFindRepository extends DoctrineRepository implements BookFindRepository
{
    public function findById(UuidInterface $id): ?Book
    {
        $book = $this->repository(Book::class)->find($id);

        return $book instanceof Book ? $book : null;
    }
}
