<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use ApiClients\Foundation\Middleware\Annotation\Priority as PriorityAnnotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;
use ReflectionMethod;
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
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * MiddlewareRunner constructor.
     * @param array $options
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $options, MiddlewareInterface ...$middlewares)
    {
        $this->options = $options;
        $this->middlewares = $middlewares;
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * Sort the middlewares by priority
     *
     * @param string $method
     * @param MiddlewareInterface[] $middlewares
     * @return array
     */
    protected function orderMiddlewares(string $method, MiddlewareInterface ...$middlewares): array
    {
        usort($middlewares, function (MiddlewareInterface $left, MiddlewareInterface $right) use ($method) {
            return $this->getPriority($method, $right) <=> $this->getPriority($method, $left);
        });

        return $middlewares;
    }

    private function getPriority(string $method, MiddlewareInterface $middleware): int
    {
        $methodReflection = new ReflectionMethod($middleware, $method);
        $annotation = $this->annotationReader->getMethodAnnotation($methodReflection, PriorityAnnotation::class);

        if ($annotation !== null &&
            get_class($annotation) === PriorityAnnotation::class
        ) {
            return $annotation->property;
        }

        return $middleware->priority();
    }

    /**
     * @param RequestInterface $request
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request
    ): CancellablePromiseInterface {
        $promise = resolve($request);

        $middlewares = $this->middlewares;
        $middlewares = $this->orderMiddlewares('pre', ...$middlewares);

        foreach ($middlewares as $middleware) {
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

        $middlewares = $this->middlewares;
        $middlewares = $this->orderMiddlewares('post', ...$middlewares);
        $middlewares = array_reverse($middlewares);

        foreach ($middlewares as $middleware) {
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

        $middlewares = $this->middlewares;
        $middlewares = $this->orderMiddlewares('error', ...$middlewares);
        $middlewares = array_reverse($middlewares);

        foreach ($middlewares as $middleware) {
            $errorMiddleware = $middleware;
            $promise = $promise->then(null, function (Throwable $throwable) use ($errorMiddleware) {
                return reject($errorMiddleware->error($throwable, $this->options));
            });
        }

        return $promise;
    }
}
