<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\CreateUser;

use App\Sesame\User\Application\Commands\CreateUser\CreateUserCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank(message: '<id> is required')]
        #[Assert\Uuid(message: '<id> must be a valid UUID')]
        public string $id,
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
        #[\SensitiveParameter]
        #[Assert\NotBlank(message: '<password> is required')]
        #[Assert\Length(min: 8, minMessage: '<password> must be at least 8 characters')]
        public string $plainPassword,
        #[Assert\NotBlank(message: '<createdAt> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<createdAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public string $createdAt,
    ) {
    }

    public function toCreateUserCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            $this->id,
            $this->name,
            $this->email,
            $this->plainPassword,
            new \DateTimeImmutable($this->createdAt),
        );
    }
}
