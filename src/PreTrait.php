<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

trait PreTrait
{
    /**
     * @param RequestInterface $request
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request,
        array $options = [],
        string $transactionId = null
    ): CancellablePromiseInterface {
        return resolve($request);
    }
}
