<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ExceptionsHttpStatusCodeMapping
{
    private const DEFAULT_STATUS_CODE = Response::HTTP_INTERNAL_SERVER_ERROR;
    private array $exceptions         = [
        \InvalidArgumentException::class => Response::HTTP_BAD_REQUEST,
        NotFoundHttpException::class     => Response::HTTP_NOT_FOUND,
    ];

    public function register(string $exceptionClass, int $statusCode): void
    {
        $this->exceptions[$exceptionClass] = $statusCode;
    }

    public function statusCodeFor(string $exceptionClass): int
    {
        $statusCode = $this->exceptions[$exceptionClass] ?? self::DEFAULT_STATUS_CODE;

        if (null === $statusCode) {
            throw new \InvalidArgumentException("There are no status code mapping for <$exceptionClass>");
        }

        return $statusCode;
    }
}
