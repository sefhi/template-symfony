<?php

declare(strict_types=1);

namespace App\Shared\Listener;

use App\Shared\ExceptionsHttpStatusCodeMapping;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function __construct(private readonly ExceptionsHttpStatusCodeMapping $exceptionMapping)
    {
    }

    public function onException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $event->setResponse(
            new JsonResponse(
                [
                    'message' => $exception->getMessage(),
                ],
                $this->exceptionMapping->statusCodeFor(get_class($exception))
            )
        );
    }
}
