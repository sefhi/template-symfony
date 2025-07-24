<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\UpdateUser;

use App\Sesame\User\Application\Commands\UpdateUser\UpdateUserCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: '<name> is required>')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: '<name> must be at least {{ limit }} characters long',
            maxMessage: '<name> cannot be longer than {{ limit }} characters'
        )]
        public string $name,
        #[Assert\NotBlank(message: '<email> is required')]
        #[Assert\Email(message: '<email> must be a valid email address')]
        public string $email,
        #[Assert\NotBlank(message: '<createdAt> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<createdAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public string $createdAt,
        #[Assert\NotNull(message: '<updatedAt> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<updateAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $updatedAt = null,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<deletedAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $deletedAt = null,
    ) {
    }

    public function toUpdateUserCommand(string $id): UpdateUserCommand
    {
        return new UpdateUserCommand(
            $id,
            $this->name,
            $this->email,
            new \DateTimeImmutable($this->createdAt),
            $this->updatedAt
                ? new \DateTimeImmutable($this->updatedAt)
                : new \DateTimeImmutable(),
            $this->deletedAt
                ? new \DateTimeImmutable($this->deletedAt)
                : null,
        );
    }
}
