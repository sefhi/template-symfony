<?php

declare(strict_types=1);

namespace App\Health\Infrastructure\Persistence\Repository;

use App\Health\Domain\DatabaseNotHealthyException;
use App\Health\Domain\Health;
use App\Health\Domain\HealthRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

final readonly class DoctrineHealthRepository extends DoctrineRepository implements HealthRepository
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
    ) {
        parent::__construct($entityManager);
    }

    public function health(): Health
    {
        try {
            $connection = $this->entityManager()->getConnection();
            $stmt       = $connection->prepare('SELECT 1+1');

            return Health::create($stmt->executeQuery()->fetchOne());
        } catch (Exception $e) {
            $this->logger->error('Database is not healthy', ['error' => $e->getMessage()]);
            throw new DatabaseNotHealthyException($e->getMessage());
        }
    }
}
