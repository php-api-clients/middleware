<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;

/**
 * Middleware, when a new request is made an instance specifically for that request is made for each request.
 *
 * Priority ranging from 0 to 1000. Where 1000 will be executed first on `pre`/`post`/`error`
 * and 0 last on `pre`/`post`/`error`.
 */
interface MiddlewareInterface
{
    /**
     * Return the processed $request via a fulfilled promise.
     * When implementing cache or other feature that returns a response, do it with a rejected promise.
     * If neither is possible, e.g. on some kind of failure, resolve the unaltered request.
     *
     * @param  RequestInterface            $request
     * @param  array                       $options
     * @param  string                      $transactionId
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface;

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
