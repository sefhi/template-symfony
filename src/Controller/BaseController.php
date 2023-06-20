<?php

declare(strict_types=1);

namespace App\Controller;

use Shared\Infrastructure\Exceptions\ExceptionsHttpStatusCodeMapping;

use function Lambdish\Phunctional\each;

use Shared\Domain\Bus\Command\CommandBusInterface;
use Shared\Domain\Bus\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function __construct(
        ExceptionsHttpStatusCodeMapping $exceptionMapping,
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus,
    ) {
        each(
            fn (int $httpCode, string $exceptionClass) => $exceptionMapping->register($exceptionClass, $httpCode),
            $this->exceptions()
        );
    }

    abstract protected function exceptions(): array;
}
