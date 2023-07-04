<?php

declare(strict_types=1);

namespace App\Health\Application\Query\Response;

use App\Shared\Domain\Bus\Query\QueryResponse;

final class GetHealthQueryResponse implements QueryResponse, \JsonSerializable
{
    private function __construct(
        private readonly string $status,
        private readonly string $message,
    ) {
    }

    public static function create(
        string $status,
        string $message,
    ): self {
        return new self($status, $message);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
