<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use ApiClients\Foundation\Middleware\PreTrait;

class DummyMiddleware implements MiddlewareInterface
{
    use PreTrait;
    use PostTrait;
}
