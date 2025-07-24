<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SymfonyExceptionsHttpStatusCodeMapping
{
    private const DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @var array<class-string<\Throwable>, int>
     */
    private array $exceptions = [
        \InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
        \DomainException::class          => Response::HTTP_BAD_REQUEST,
        NotFoundHttpException::class     => Response::HTTP_NOT_FOUND,
    ];

    /**
     * @param class-string<\Throwable> $exceptionClass
     */
    public function register(string $exceptionClass, int $statusCode): void
    {
        $this->exceptions[$exceptionClass] = $statusCode;
    }

    /**
     * @param class-string<\Throwable> $exceptionClass
     */
    public function statusCodeFor(string $exceptionClass): int
    {
        return $this->exceptions[$exceptionClass] ?? self::DEFAULT_STATUS_CODE;
    }
}
