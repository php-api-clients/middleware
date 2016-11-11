<?php
declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware;

use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;

class DummyMiddlewareTest extends TestCase
{
    public function testPriority()
    {
        $middleware = new DummyMiddleware();
        $this->assertSame(
            500,
            $middleware->priority()
        );
    }

    public function testPre()
    {
        $middleware = new DummyMiddleware();
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $this->assertSame(
            $request,
            await(
                $middleware->pre($request),
                Factory::create()
            )
        );
    }

    public function testPost()
    {
        $middleware = new DummyMiddleware();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $this->assertSame(
            $response,
            await(
                $middleware->post($response),
                Factory::create()
            )
        );
    }
}
