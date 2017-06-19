<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware\Locator;

use ApiClients\Foundation\Middleware\MiddlewareInterface;

interface Locator
{
    /**
     * Returns an instance of the requested middleware.
     *
     * @param  string                     $middleware
     * @throws InvalidMiddlewareException when instance cannot be created
     * @return MiddlewareInterface
     */
    public function get(string $middleware): MiddlewareInterface;
}
