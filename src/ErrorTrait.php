<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use React\Promise\CancellablePromiseInterface;
use Throwable;
use function React\Promise\reject;

trait ErrorTrait
{
    /**
     * @param  Throwable                   $throwable
     * @param  array                       $options
     * @return CancellablePromiseInterface
     * @deprecated Will be removed in the next major version.
     */
    public function error(
        Throwable $throwable,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        return reject($throwable);
    }
}
