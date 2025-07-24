<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Listener;

use App\Shared\Infrastructure\Exceptions\SymfonyExceptionsHttpStatusCodeMapping;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

readonly class SymfonyExceptionListener
{
    public function __construct(private SymfonyExceptionsHttpStatusCodeMapping $exceptionMapping)
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

        if ($exception instanceof UnprocessableEntityHttpException) {
            $body['errors'] = $this->parseErrorMessage($exception->getMessage());
            unset($body['message']);
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

    /**
     * @param string $errorMessage
     *
     * @return array<array<string, string>>
     */
    private function parseErrorMessage(string $errorMessage): array
    {
        $lines = array_filter(
            explode("\n", $errorMessage),
            fn ($error) => !empty(trim($error))
        );

        $errors = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('/<(\w+)>/', $line, $matches)) {
                $field   = $matches[1];
                $message = 'The ' . preg_replace('/<(\w+)>/', $matches[1], $line);
            } elseif (preg_match('/^(\w+)\s+/', $line, $matches)) {
                $field   = $matches[1];
                $message = 'The ' . $line;
            } else {
                $field   = 'unknown';
                $message = $line;
            }

            $errors[] = [
                'field'   => $field,
                'message' => $message,
            ];
        }

        return $errors;
    }
}
