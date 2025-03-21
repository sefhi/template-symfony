<?php

declare(strict_types=1);

namespace App\Shared\Domain\Utils;

use IteratorAggregate;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 */
abstract readonly class Collection implements \Countable, \IteratorAggregate
{
    /**
     * @param array<int, T> $items
     */
    public function __construct(private array $items)
    {
        $this->assertArrayOf($this->type(), $items);
    }

    /**
     * @return \Traversable<int, T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items());
    }

    public function count(): int
    {
        return count($this->items());
    }

    /**
     * @param callable(int, T): bool $fn
     */
    public function exists(callable $fn): bool
    {
        return array_any($this->items(), fn ($item, $key) => $fn($key, $item));
    }

    abstract protected function type(): string;

    /**
     * @return array<int, T>
     */
    protected function items(): array
    {
        return $this->items;
    }

    /**
     * @param array<int, T> $items
     */
    private function assertArrayOf(string $class, array $items): void
    {
        foreach ($items as $item) {
            if (!$item instanceof $class) {
                throw new \InvalidArgumentException(sprintf('The object <%s> is not an instance of <%s>', $class, $item::class));
            }
        }
    }
}
