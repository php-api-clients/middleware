<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;

interface PostMiddlewareInterface
{
    /**
     * Return the processed $response via a promise.
     *
     * @param  ResponseInterface           $response
     * @param  array                       $options
     * @param  string                      $transactionId
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface;
}
