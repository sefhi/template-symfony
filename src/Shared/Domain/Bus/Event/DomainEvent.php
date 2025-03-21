<?php

declare(strict_types=1);

namespace App\Shared\Domain\Bus\Event;

use Ramsey\Uuid\Uuid;

abstract readonly class DomainEvent
{
    private string $eventId;
    private string $occurredOn;

    public function __construct(
        private string $aggregateId,
        ?string $eventId = null,
        ?string $occurredOn = null,
    ) {
        $date             = new \DateTimeImmutable();
        $this->eventId    = $eventId ?: (string) Uuid::uuid7();
        $this->occurredOn = $occurredOn ?: $date->format(\DateTimeInterface::ATOM);
    }

    /**
     * @param string       $aggregateId
     * @param array<mixed> $body
     * @param string       $eventId
     * @param string       $occurredOn
     *
     * @return self
     */
    abstract public static function fromPrimitives(
        string $aggregateId,
        array $body,
        string $eventId,
        string $occurredOn,
    ): self;

    abstract public static function eventName(): string;

    /**
     * @return array<mixed>
     */
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
