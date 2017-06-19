<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\MiddlewareRunner;
use ApiClients\Tests\Foundation\Middleware\TestMiddlewares\OneMiddleware;
use ApiClients\Tests\Foundation\Middleware\TestMiddlewares\ThreeMiddleware;
use ApiClients\Tests\Foundation\Middleware\TestMiddlewares\TwoMiddleware;
use ApiClients\Tools\TestUtilities\TestCase;
use Closure;
use Exception;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phake;
use React\EventLoop\Factory;
use Throwable;
use function Clue\React\Block\await;
use function React\Promise\reject;
use function React\Promise\resolve;

class MiddlewareRunnerTest extends TestCase
{
    public function testAll()
    {
        $loop = Factory::create();
        $request = new Request('GET', 'https://example.com/');
        $response = new Response(200);
        $exception = new Exception();
        $options = [];

        $middlewareOne = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareOne)->priority()->thenReturn(1000);
        $middlewareTwo = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareTwo)->priority()->thenReturn(500);
        $middlewareThree = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareThree)->priority()->thenReturn(0);
        $args = [
            $options,
            $middlewareThree,
            $middlewareOne,
            $middlewareTwo,
        ];

        $executioner = new MiddlewareRunner(...$args);
        $id = Closure::bind(function ($executioner) {
            return $executioner->id;
        }, null, MiddlewareRunner::class)($executioner);

        Phake::when($middlewareOne)->pre($request, $options, $id)->thenReturn(resolve($request));
        Phake::when($middlewareOne)->post($response, $options, $id)->thenReturn(resolve($response));
        Phake::when($middlewareOne)->error($exception, $options, $id)->thenReturn(reject($exception));

        Phake::when($middlewareTwo)->pre($request, $options, $id)->thenReturn(resolve($request));
        Phake::when($middlewareTwo)->post($response, $options, $id)->thenReturn(resolve($response));
        Phake::when($middlewareTwo)->error($exception, $options, $id)->thenReturn(reject($exception));

        Phake::when($middlewareThree)->pre($request, $options, $id)->thenReturn(resolve($request));
        Phake::when($middlewareThree)->post($response, $options, $id)->thenReturn(resolve($response));
        Phake::when($middlewareThree)->error($exception, $options, $id)->thenReturn(reject($exception));

        self::assertSame($request, await($executioner->pre($request), $loop));
        self::assertSame($response, await($executioner->post($response), $loop));
        try {
            await($executioner->error($exception), $loop);
        } catch (Throwable $throwable) {
            self::assertSame($exception, $throwable);
        }

        Phake::inOrder(
            Phake::verify($middlewareOne)->pre($request, $options, $id),
            Phake::verify($middlewareTwo)->pre($request, $options, $id),
            Phake::verify($middlewareThree)->pre($request, $options, $id),
            Phake::verify($middlewareOne)->post($response, $options, $id),
            Phake::verify($middlewareTwo)->post($response, $options, $id),
            Phake::verify($middlewareThree)->post($response, $options, $id),
            Phake::verify($middlewareOne)->error($exception, $options, $id),
            Phake::verify($middlewareTwo)->error($exception, $options, $id),
            Phake::verify($middlewareThree)->error($exception, $options, $id)
        );
    }

    public function testAnnotations()
    {
        $loop = Factory::create();
        $request = new Request('GET', 'https://example.com/');
        $response = new Response(200);
        $exception = new Exception();
        $options = [];

        $middlewareOne = new OneMiddleware();
        $middlewareTwo = new TwoMiddleware();
        $middlewareThree = new ThreeMiddleware();

        $args = [
            $options,
            $middlewareOne,
            $middlewareTwo,
            $middlewareThree,
        ];

        $executioner = new MiddlewareRunner(...$args);
        self::assertSame($request, await($executioner->pre($request), $loop));
        self::assertSame($response, await($executioner->post($response), $loop));
        try {
            await($executioner->error($exception), $loop);
        } catch (Throwable $throwable) {
            self::assertSame($exception, $throwable);
        }

        $calls = array_merge_recursive(
            $middlewareOne->getCalls(),
            $middlewareTwo->getCalls(),
            $middlewareThree->getCalls()
        );
        ksort($calls);

        self::assertSame([
            ThreeMiddleware::class . ':pre',
            TwoMiddleware::class . ':pre',
            OneMiddleware::class . ':pre',
            OneMiddleware::class . ':post',
            TwoMiddleware::class . ':post',
            ThreeMiddleware::class . ':post',
            OneMiddleware::class . ':error',
            ThreeMiddleware::class . ':error',
            TwoMiddleware::class . ':error',
        ], array_values($calls));
    }
}
