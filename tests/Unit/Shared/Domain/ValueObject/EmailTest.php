<?php

declare(strict_types=1);

namespace App\Tests\Unit\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    /** @test */
    public function itShouldEqualsTwoEmails(): void
    {
        self::assertTrue(
            Email::fromString('email@email.es')
                ->isEqualTo(Email::fromString('email@email.es'))
        );
    }

    /** @test */
    public function itShouldReturnAnInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        Email::fromString('email');
    }
}
