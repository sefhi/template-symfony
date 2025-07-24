<?php

declare(strict_types=1);

namespace App\Sesame\User\Domain\Entities;

use App\Sesame\User\Domain\ValueObjects\UserName;
use App\Sesame\User\Domain\ValueObjects\UserPassword;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Domain\ValueObjects\Timestamps;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class User extends AggregateRoot
{
    private bool $isPasswordHashed = false;

    private function __construct(
        private UuidInterface $id,
        private UserName $name,
        private Email $email,
        private UserPassword $password,
        private Timestamps $timestamps,
    ) {
    }

    public static function make(
        string $id,
        string $name,
        string $email,
        string $password,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null,
    ): self {
        return new self(
            Uuid::fromString($id),
            new UserName($name),
            new Email($email),
            new UserPassword($password),
            Timestamps::create(
                $createdAt,
                $updatedAt,
                $deletedAt,
            )
        );
    }

    public static function create(
        string $id,
        string $name,
        string $email,
        string $password,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            Uuid::fromString($id),
            new UserName($name),
            new Email($email),
            new UserPassword($password),
            Timestamps::create(
                $createdAt,
                null,
                null
            )
        );
    }

    public function withPasswordHashed(string $password): self
    {
        $user = new self(
            $this->id,
            $this->name,
            $this->email,
            new UserPassword($password),
            $this->timestamps,
        );

        $user->isPasswordHashed = true;

        return $user;
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function nameValue(): string
    {
        return $this->name->value();
    }

    public function emailValue(): string
    {
        return $this->email->value();
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): UserPassword
    {
        return $this->password;
    }

    public function timestamps(): Timestamps
    {
        return $this->timestamps;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->timestamps->createdAt();
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->timestamps->updatedAt();
    }

    public function update(
        string $name,
        string $email,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
        ?\DateTimeImmutable $deletedAt,
    ): void {
        $this->name       = new UserName($name);
        $this->email      = new Email($email);
        $this->timestamps = Timestamps::create(
            $createdAt,
            $updatedAt,
            $deletedAt,
        );
    }

    public function deletedAt(): ?\DateTimeImmutable
    {
        return $this->timestamps->deletedAt();
    }

    public function delete(): void
    {
        $this->timestamps = $this->timestamps->delete();
    }

    public function isPasswordHashed(): bool
    {
        return $this->isPasswordHashed;
    }

    public function passwordValue(): string
    {
        return $this->password->value();
    }
}
