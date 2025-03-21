<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Listener;

use App\Shared\Infrastructure\Exceptions\SymfonyExceptionsHttpStatusCodeMapping;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class SymfonyExceptionListener
{
    public function __construct(private readonly SymfonyExceptionsHttpStatusCodeMapping $exceptionMapping)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $body = [
            'message' => $exception->getMessage(),
        ];

        // Exception on constraints
        if ($exception instanceof ValidationFailedException) {
            foreach (iterator_to_array($exception->getViolations()) as $violation) {
                $body['errors'] = [$violation->getPropertyPath() => $violation->getMessageTemplate()];
            }
        }

        // Exception on buses
        if ($exception instanceof HandlerFailedException) {
            $nestedExceptions = $exception->getWrappedExceptions();
            if (!empty($nestedExceptions)) {
                $exception       = current($nestedExceptions);
                $body['message'] = $exception->getMessage();
            }
        }

        $statusCodeFor = $this->exceptionMapping->statusCodeFor(get_class($exception));

        if (Response::HTTP_INTERNAL_SERVER_ERROR === $statusCodeFor) {
            $body['message'] = 'Unexpected API error';
        }

        $event->setResponse(
            new JsonResponse(
                $body,
                $statusCodeFor
            )
        );
    }
}
