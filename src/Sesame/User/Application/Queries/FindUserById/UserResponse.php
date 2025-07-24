<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Queries\FindUserById;

use App\Sesame\User\Domain\Entities\User;
use App\Shared\Domain\Bus\Query\QueryResponse;

final readonly class UserResponse implements \JsonSerializable, QueryResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromUser(User $user): self
    {
        return new self(
            $user->id()->toString(),
            $user->nameValue(),
            $user->emailValue(),
            $user->createdAt(),
            $user->updatedAt(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'email'     => $this->email,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
