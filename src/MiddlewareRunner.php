<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;
use function React\Promise\resolve;

final class MiddlewareRunner
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var MiddlewareInterface[]
     */
    private $middlewares;

    /**
     * MiddlewareRunner constructor.
     * @param array $options
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $options, MiddlewareInterface ...$middlewares)
    {
        $this->options = $options;
        $this->middlewares = $this->orderMiddlewares(...$middlewares);
    }

    /**
     * Sort the middlewares by priority
     *
     * @param MiddlewareInterface[] $middlewares
     * @return array
     */
    protected function orderMiddlewares(MiddlewareInterface ...$middlewares): array
    {
        usort($middlewares, function (MiddlewareInterface $left, MiddlewareInterface $right) {
            return $right->priority() <=> $left->priority();
        });

        return $middlewares;
    }

    /**
     * @param RequestInterface $request
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request
    ): CancellablePromiseInterface {
        $promise = resolve($request);

        foreach ($this->middlewares as $middleware) {
            $requestMiddleware = $middleware;
            $promise = $promise->then(function (RequestInterface $request) use ($requestMiddleware) {
                return $requestMiddleware->pre($request, $this->options);
            });
        }

        return $promise;
    }

    /**
     * @param ResponseInterface $response
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response
    ): CancellablePromiseInterface {
        $promise = resolve($response);

        $this->middlewares = array_reverse($this->middlewares);

        foreach ($this->middlewares as $middleware) {
            $responseMiddleware = $middleware;
            $promise = $promise->then(function (ResponseInterface $response) use ($responseMiddleware) {
                return $responseMiddleware->post($response, $this->options);
            });
        }

        return $promise;
    }

    /**
     * @param Throwable $throwable
     * @return CancellablePromiseInterface
     */
    public function error(
        Throwable $throwable
    ): CancellablePromiseInterface {

        $promise = reject($throwable);

        $this->middlewares = array_reverse($this->middlewares);

        foreach ($this->middlewares as $middleware) {
            $errorMiddleware = $middleware;
            $promise = $promise->then(null, function (Throwable $throwable) use ($errorMiddleware) {
                return reject($errorMiddleware->error($throwable, $this->options));
            });
        }

        return $promise;
    }
}
