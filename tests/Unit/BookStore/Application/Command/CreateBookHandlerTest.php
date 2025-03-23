<?php

declare(strict_types=1);

namespace Tests\Unit\BookStore\Application\Command;

use App\BookStore\Application\Command\CreateBook\CreateBookHandler;
use App\BookStore\Domain\Repositories\BookSaveRepository;
use App\Shared\Domain\Bus\Event\EventBus;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\BookStore\Domain\Entities\BookMother;

final class CreateBookHandlerTest extends TestCase
{
    private BookSaveRepository|MockObject $repository;
    private MockObject|EventBus $eventBus;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(BookSaveRepository::class);
        $this->eventBus   = $this->createMock(EventBus::class);
    }

    #[Test]
    public function itShouldCreateABook(): void
    {
        // GIVEN

        $command      = CreateBookCommandMother::random();
        $bookExpected = BookMother::fromCommand($command);
        //        $bookEventExpected = BookCreatedEventMother::fromBook($bookExpected);

        // WHEN

        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with($bookExpected);

        $this->eventBus
            ->expects(self::once())
            ->method('publish');
        //            ->with($bookEventExpected);

        $handler = new CreateBookHandler(
            $this->repository,
            $this->eventBus,
        );

        // THEN

        $handler($command);
    }
}
