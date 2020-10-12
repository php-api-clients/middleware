<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use React\Promise\CancellablePromiseInterface;
use Throwable;

interface ErrorMiddlewareInterface
{
    /**
     * Transform the throwable into another throwable or exception,
     * but never turn it into a successful promise again.
     *
     * @param  Throwable                   $throwable
     * @param  array                       $options
     * @param  string                      $transactionId
     * @return CancellablePromiseInterface
     */
    public function error(
        Throwable $throwable,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface;
}
