<?php

namespace Integration\BookStore\Infrastructure\Persistence\Repository;

use App\BookStore\Infrastructure\Persistence\Repository\DoctrineBookFindRepository;
use App\BookStore\Infrastructure\Persistence\Repository\DoctrineBookSaveRepository;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Unit\BookStore\Domain\Entities\BookMother;

final class DoctrineBookSaveRepositoryTest extends KernelTestCase
{
    private DoctrineBookSaveRepository $repositorySave;
    private DoctrineBookFindRepository $repositoryFind;

    protected function setUp(): void
    {
        $entityManager        = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->repositorySave = new DoctrineBookSaveRepository($entityManager);
        $this->repositoryFind = new DoctrineBookFindRepository($entityManager);
    }

    #[Test]
    public function itShouldSaveBook(): void
    {
        // GIVEN

        $book = BookMother::random();

        // WHEN

        $this->repositorySave->save($book);

        // THEN

        self::assertEquals($this->repositoryFind->findById($book->id()), $book);
    }
}
