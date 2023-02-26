<?php

declare(strict_types=1);

namespace App\Controller;

use App\Shared\ExceptionsHttpStatusCodeMapping;

use function Lambdish\Phunctional\each;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class BaseController extends AbstractController
{
    public function __construct(ExceptionsHttpStatusCodeMapping $exceptionMapping)
    {
        each(
            fn (int $httpCode, string $exceptionClass) => $exceptionMapping->register($exceptionClass, $httpCode),
            $this->exceptions()
        );
    }

    abstract protected function exceptions(): array;
}
