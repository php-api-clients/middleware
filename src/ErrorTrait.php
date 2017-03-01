<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

use Throwable;
use React\Promise\CancellablePromiseInterface;

use function React\Promise\reject;

trait ErrorTrait
{
    /**
     * @param Throwable $throwable
     * @param array $options
     * @return CancellablePromiseInterface
     */
    public function error(Throwable $throwable, array $options = []): CancellablePromiseInterface
    {
        return reject($throwable);
    }
}
