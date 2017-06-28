<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use ApiClients\Foundation\Middleware\Annotation\PriorityInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use ReflectionMethod;
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
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * @var string
     */
    private $id;

    /**
     * MiddlewareRunner constructor.
     * @param array                 $options
     * @param MiddlewareInterface[] $middlewares
     */
    public function __construct(array $options, MiddlewareInterface ...$middlewares)
    {
        $this->options = $options;
        $this->id = bin2hex(random_bytes(32));
        $this->middlewares = $middlewares;
        $this->annotationReader = new AnnotationReader();
    }

    /**
     * @param  RequestInterface            $request
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
                return $requestMiddleware->pre($request, $this->id, $this->options);
            });
        }

        return $promise;
    }

    /**
     * @param  ResponseInterface           $response
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response
    ): CancellablePromiseInterface {
        $promise = resolve($response);

        $middlewares = $this->middlewares;
        $middlewares = $this->orderMiddlewares('post', ...$middlewares);

        foreach ($middlewares as $middleware) {
            $responseMiddleware = $middleware;
            $promise = $promise->then(function (ResponseInterface $response) use ($responseMiddleware) {
                return $responseMiddleware->post($response, $this->id, $this->options);
            });
        }

        return $promise;
    }

    /**
     * @param  Throwable                   $throwable
     * @return CancellablePromiseInterface
     */
    public function error(
        Throwable $throwable
    ): CancellablePromiseInterface {
        $promise = reject($throwable);

        $middlewares = $this->middlewares;
        $middlewares = $this->orderMiddlewares('error', ...$middlewares);

        foreach ($middlewares as $middleware) {
            $errorMiddleware = $middleware;
            $promise = $promise->then(null, function (Throwable $throwable) use ($errorMiddleware) {
                return reject($errorMiddleware->error($throwable, $this->id, $this->options));
            });
        }

        return $promise;
    }

    /**
     * Sort the middlewares by priority.
     *
     * @param  string                $method
     * @param  MiddlewareInterface[] $middlewares
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
        $annotations = $this->annotationReader->getMethodAnnotations($methodReflection);

        foreach ($annotations as $annotation) {
            if (!is_subclass_of($annotation, PriorityInterface::class)) {
                continue;
            }

            return $annotation->priority();
        }

        return Priority::DEFAULT;
    }
}
