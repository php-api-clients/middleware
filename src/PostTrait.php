<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Psr\Http\Message\ResponseInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

trait PostTrait
{
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
        return resolve($response);
    }
}
