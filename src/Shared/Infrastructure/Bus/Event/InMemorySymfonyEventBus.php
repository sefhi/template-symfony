<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBus;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final readonly class InMemorySymfonyEventBus implements EventBus
{
    public function __construct(
        private MessageBusInterface $eventBus,
    ) {
    }

    public function publish(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            try {
                $this->eventBus->dispatch(
                    (new Envelope($event))->with(new DispatchAfterCurrentBusStamp()),
                );
            } catch (\Exception $e) {
                // TODO implement failover
            }
        }
    }
}
