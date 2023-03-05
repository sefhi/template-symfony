<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Listener;

use Shared\Infrastructure\Exceptions\ExceptionsHttpStatusCodeMapping;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class SymfonyExceptionListener
{
    public function __construct(private readonly ExceptionsHttpStatusCodeMapping $exceptionMapping)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $body = [
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof ValidationFailedException) {
            foreach (iterator_to_array($exception->getViolations()) as $violation) {
                $body['errors'] = [$violation->getPropertyPath() => $violation->getMessageTemplate()];
            }
        }

        $event->setResponse(
            new JsonResponse(
                $body,
                $this->exceptionMapping->statusCodeFor(get_class($exception))
            )
        );
    }
}
