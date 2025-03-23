<?php

declare(strict_types=1);

namespace App\BookStore\Domain\Entities;

use App\BookStore\Domain\ValueObjects\BookAuthor;
use App\BookStore\Domain\ValueObjects\BookIsbn;
use App\BookStore\Domain\ValueObjects\BookStock;
use App\BookStore\Domain\ValueObjects\BookTitle;
use App\Shared\Domain\Aggregate\AggregateRoot;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class Book extends AggregateRoot
{
    private function __construct(
        private UuidInterface $id,
        private BookTitle $title,
        private BookAuthor $author,
        private BookIsbn $isbn,
        private BookStock $stock,
    ) {
    }

    public static function create(
        string $id,
        string $title,
        string $author,
        string $isbn,
        int $stock,
    ): self {
        $book = new self(
            Uuid::fromString($id),
            new BookTitle($title),
            new BookAuthor($author),
            new BookIsbn($isbn),
            new BookStock($stock)
        );

        //        $book->record(
        //            new BookCreatedDomainEvent(
        //                $book->id()->toString(),
        //                $book->title()->value(),
        //                $book->author()->value(),
        //                $book->isbn()->value(),
        //                $book->stock()->value()
        //            )
        //        );

        return $book;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function title(): BookTitle
    {
        return $this->title;
    }

    public function author(): BookAuthor
    {
        return $this->author;
    }

    public function isbn(): BookIsbn
    {
        return $this->isbn;
    }

    public function stock(): BookStock
    {
        return $this->stock;
    }
}
