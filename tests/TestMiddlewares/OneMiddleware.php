<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware\TestMiddlewares;

use ApiClients\Foundation\Middleware\Annotation\Priority as PriorityAnnotation;
use ApiClients\Foundation\Middleware\DefaultPriorityTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\Priority;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;
use function React\Promise\resolve;

class OneMiddleware implements MiddlewareInterface
{
    use DefaultPriorityTrait;

    private $calls = [];

    /**
     * @return array
     */
    public function getCalls(): array
    {
        return $this->calls;
    }

    /**
     * @param  RequestInterface            $request
     * @param  array                       $options
     * @return CancellablePromiseInterface
     * @PriorityAnnotation(Priority::LAST);
     */
    public function pre(RequestInterface $request, array $options = [], string $transactionId = null): CancellablePromiseInterface
    {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':pre';

        return resolve($request);
    }

    /**
     * @param  ResponseInterface           $response
     * @param  array                       $options
     * @return CancellablePromiseInterface
     * @PriorityAnnotation(Priority::FIRST);
     */
    public function post(ResponseInterface $response, array $options = [], string $transactionId = null): CancellablePromiseInterface
    {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':post';

        return resolve($response);
    }

    /**
     * @param  Throwable                   $throwable
     * @param  array                       $options
     * @return CancellablePromiseInterface
     * @PriorityAnnotation(Priority::FIRST);
     */
    public function error(Throwable $throwable, array $options = [], string $transactionId = null): CancellablePromiseInterface
    {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':error';

        return reject($throwable);
    }
}
