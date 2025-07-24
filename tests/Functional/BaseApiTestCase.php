<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Infrastructure\Security\UserAdapter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\DoctrinePersistence;
use Tests\Utils\Factory\PersistenceInterface;
use Tests\Utils\Factory\User\UserFactory;

class BaseApiTestCase extends WebTestCase
{
    private ?KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private PersistenceInterface $factoryPersistence;
    private User $userLogged;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();

        $this->client             = self::createClient();
        $this->entityManager      = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->factoryPersistence = new DoctrinePersistence($this->entityManager);

        $this->entityManager->beginTransaction();

        $this->ensureAuthenticatedInTest();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
    }

    public function client(): KernelBrowser
    {
        return $this->client;
    }

    public function factoryPersistence(): PersistenceInterface
    {
        return $this->factoryPersistence;
    }

    protected function getUserLogged(): User
    {
        return $this->userLogged;
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    private function ensureAuthenticatedInTest(): void
    {
        $this->userLogged = UserMother::admin();
        $userAdmin        = new UserFactory($this->factoryPersistence);
        $userAdmin->createOne($this->userLogged);
        $this->client->loginUser(new UserAdapter($this->userLogged));
    }
}
