<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware\TestMiddlewares;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;
use function React\Promise\resolve;

class TwoMiddleware implements MiddlewareInterface
{
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
     */
    public function pre(
        RequestInterface $request,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':pre';

        return resolve($request);
    }

    /**
     * @param  ResponseInterface           $response
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function post(
        ResponseInterface $response,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':post';

        return resolve($response);
    }

    /**
     * @param  Throwable                   $throwable
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function error(
        Throwable $throwable,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        usleep(100);
        $this->calls[(string)microtime(true)] = __CLASS__ . ':error';

        return reject($throwable);
    }
}
