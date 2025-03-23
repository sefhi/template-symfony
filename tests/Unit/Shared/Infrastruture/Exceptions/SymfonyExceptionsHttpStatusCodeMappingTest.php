<?php

namespace Tests\Unit\Shared\Infrastruture\Exceptions;

use App\Shared\Infrastructure\Exceptions\SymfonyExceptionsHttpStatusCodeMapping;
use PHPUnit\Framework\TestCase;

class SymfonyExceptionsHttpStatusCodeMappingTest extends TestCase
{
    private SymfonyExceptionsHttpStatusCodeMapping $exceptionMappings;

    protected function setUp(): void
    {
        $this->exceptionMappings = new SymfonyExceptionsHttpStatusCodeMapping();
    }

    /**
     * @test
     *
     * @dataProvider exceptionsAndCode
     */
    public function itShouldRegisterExceptionsCorrectlyAndReturnStatusCode(string $exceptionClass, int $httpStatusCode): void
    {
        $this->exceptionMappings->register($exceptionClass, $httpStatusCode);

        self::assertSame($httpStatusCode, $this->exceptionMappings->statusCodeFor($exceptionClass));
    }

    public static function exceptionsAndCode(): \Iterator
    {
        yield [
            \RuntimeException::class,
            999,
        ];

        yield [
            \DomainException::class,
            777,
        ];
    }

    /** @test */
    public function itShouldReturnAnStatusCode500WhenExceptionNotMapping(): void
    {
        self::assertSame(500, $this->exceptionMappings->statusCodeFor(\RuntimeException::class));
    }
}
