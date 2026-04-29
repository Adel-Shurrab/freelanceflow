<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseException extends RuntimeException
{
    public function __construct(
        string $message = '',
        protected readonly array $context = [],
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * HTTP status code for API responses.
     * Override this method in child exceptions when needed.
     */
    public function httpStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
