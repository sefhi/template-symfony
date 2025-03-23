<?php

declare(strict_types=1);

namespace App\BookStore\Application\Command\CreateBook;

use App\BookStore\Domain\Entities\Book;
use App\BookStore\Domain\Repositories\BookSaveRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Event\EventBus;

final readonly class CreateBookHandler implements CommandHandler
{
    public function __construct(
        private BookSaveRepository $repository,
        private EventBus $eventBus,
    ) {
    }

    public function __invoke(CreateBookCommand $command): void
    {
        $book = Book::create(
            id: $command->id,
            title: $command->title,
            author: $command->author,
            isbn: $command->isbn,
            stock: $command->stock,
        );

        $this->repository->save($book);
        $this->eventBus->publish(...$book->pullDomainEvents());
    }
}
