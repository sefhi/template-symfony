<?php

declare(strict_types=1);

namespace App\Shared\Api;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Shared\Domain\Bus\Command\CommandResponse;
use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\QueryBusInterface;
use App\Shared\Domain\Bus\Query\QueryResponse;
use App\Shared\Infrastructure\Exceptions\SymfonyExceptionsHttpStatusCodeMapping;

use function Lambdish\Phunctional\each;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController extends AbstractController
{
    public function __construct(
        SymfonyExceptionsHttpStatusCodeMapping $exceptionMapping,
        protected readonly CommandBusInterface $commandBus,
        protected readonly QueryBusInterface $queryBus,
        protected readonly SerializerInterface $serializer,
        protected readonly ValidatorInterface $validator,
    ) {
        each(
            fn (int $httpCode, string $exceptionClass) => $exceptionMapping->register($exceptionClass, $httpCode),
            $this->exceptions()
        );
    }

    public function ask(Query $query): ?QueryResponse
    {
        return $this->queryBus->ask($query);
    }

    public function dispatch(Command $command): ?CommandResponse
    {
        return $this->commandBus->dispatch($command);
    }

    protected function deserialize(Request $request, string $class): mixed
    {
        $content = null;

        if ('json' === $request->getContentTypeFormat() || !empty($request->getContent())) {
            $content = $request->getContent();
        } elseif (!empty($request->request->all())) {
            $content = json_encode($request->request->all(), JSON_THROW_ON_ERROR);
        } elseif (!empty($request->query->all())) {
            $content = json_encode($request->query->all(), JSON_THROW_ON_ERROR);
        }

        return $this->serializer->deserialize(
            $content,
            $class,
            'json'
        );
    }

    protected function validationRequest(Request $request, string $class): mixed
    {
        $objectDto = $this->deserialize($request, $class);

        $validationErrors = $this->validator->validate($objectDto);

        if ($validationErrors->count() > 0) {
            throw new ValidationFailedException($objectDto::class, $validationErrors);
        }

        return $objectDto;
    }

    abstract protected function exceptions(): array;
}
