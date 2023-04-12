<?php

declare(strict_types=1);

namespace Shared\Domain\Bus\Event;

use Ramsey\Uuid\Uuid;

abstract class DomainEvent
{
    private readonly string $eventId;
    private readonly string $occurredOn;

    public function __construct(private readonly string $aggregateId, string $eventId = null, string $occurredOn = null)
    {
        $date             = new \DateTimeImmutable();
        $this->eventId    = $eventId ?: (string) Uuid::uuid7();
        $this->occurredOn = $occurredOn ?: $date->format(\DateTimeInterface::ATOM);
    }

    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn
    ): self;

    abstract public static function eventName(): string;

    abstract public function toPrimitives(): array;

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function eventId(): string
    {
        return $this->eventId;
    }

    public function occurredOn(): string
    {
        return $this->occurredOn;
    }
}
