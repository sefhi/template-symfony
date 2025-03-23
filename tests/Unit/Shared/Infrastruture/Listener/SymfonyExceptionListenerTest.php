<?php

namespace Tests\Unit\Shared\Infrastruture\Listener;

use App\Shared\Infrastructure\Exceptions\SymfonyExceptionsHttpStatusCodeMapping;
use App\Shared\Infrastructure\Listener\SymfonyExceptionListener;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SymfonyExceptionListenerTest extends TestCase
{
    private EventDispatcher $dispatcher;
    private SymfonyExceptionsHttpStatusCodeMapping|MockObject $exceptionMapping;
    private HttpKernelInterface|MockObject $httpKernel;

    protected function setUp(): void
    {
        $this->dispatcher       = new EventDispatcher();
        $this->exceptionMapping = $this->createMock(SymfonyExceptionsHttpStatusCodeMapping::class);
        $this->httpKernel       = $this->createMock(HttpKernelInterface::class);
    }

    /** @test */
    public function itShouldGetAnErrorMessageInJsonResponse(): void
    {
        // GIVEN

        $this->exceptionMapping
            ->expects(self::once())
            ->method('statusCodeFor')
            ->willReturn(Response::HTTP_BAD_REQUEST);

        $listener = new SymfonyExceptionListener($this->exceptionMapping);
        $this->dispatcher->addListener('onException', [$listener, 'onException']);
        $exception = new \InvalidArgumentException('test exception');
        $request   = Request::create('/');
        $event     = new ExceptionEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // WHEN

        $this->dispatcher->dispatch($event, 'onException');

        // THEN

        self::assertInstanceOf(JsonResponse::class, $event->getResponse());
    }

    /** @test */
    public function itShouldGetAnErrorMessageUnexpectedApiErrorWhenStatusIs500(): void
    {
        // GIVEN

        $this->exceptionMapping
            ->expects(self::once())
            ->method('statusCodeFor')
            ->willReturn(Response::HTTP_INTERNAL_SERVER_ERROR);

        $listener = new SymfonyExceptionListener($this->exceptionMapping);
        $this->dispatcher->addListener('onException', [$listener, 'onException']);
        $exception = new \InvalidArgumentException('test exception');
        $request   = Request::create('/');
        $event     = new ExceptionEvent(
            $this->httpKernel,
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );

        // WHEN

        $this->dispatcher->dispatch($event, 'onException');

        // THEN

        self::assertInstanceOf(JsonResponse::class, $event->getResponse());
        self::assertStringContainsString(
            'Unexpected API error',
            $event->getResponse()->getContent()
        );
    }
}
