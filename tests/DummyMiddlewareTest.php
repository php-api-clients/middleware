<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware;

use ApiClients\Tools\TestUtilities\TestCase;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use function Clue\React\Block\await;

class DummyMiddlewareTest extends TestCase
{
    public function testPre()
    {
        $middleware = new DummyMiddleware();
        $request = $this->prophesize(RequestInterface::class)->reveal();
        $this->assertSame(
            $request,
            await(
                $middleware->pre($request, 'abc'),
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
                $middleware->post($response, 'abc'),
                Factory::create()
            )
        );
    }

    public function testError()
    {
        $middleware = new DummyMiddleware();
        $exception = new Exception('Throwable or anything extending it');
        self::expectException(Exception::class);
        self::expectExceptionMessage('Throwable or anything extending it');
        await(
            $middleware->error($exception, 'abc'),
            Factory::create()
        );
    }
}
