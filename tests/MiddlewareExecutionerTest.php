<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware;

use ApiClients\Foundation\Middleware\MiddlewareExecutioner;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Phake;
use function React\Promise\resolve;

class MiddlewareExecutionerTest extends TestCase
{
    public function testAll()
    {
        $request = new Request('GET', 'https://example.com/');
        $response = new Response(200);
        $options = [];

        $middlewareOne = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareOne)->priority()->thenReturn(1000);
        Phake::when($middlewareOne)->pre($request, $options)->thenReturn(resolve($request));
        Phake::when($middlewareOne)->post($response, $options)->thenReturn(resolve($response));

        $middlewareTwo = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareTwo)->priority()->thenReturn(500);
        Phake::when($middlewareTwo)->pre($request, $options)->thenReturn(resolve($request));
        Phake::when($middlewareTwo)->post($response, $options)->thenReturn(resolve($response));

        $middlewareThree = Phake::mock(MiddlewareInterface::class);
        Phake::when($middlewareThree)->priority()->thenReturn(0);
        Phake::when($middlewareThree)->pre($request, $options)->thenReturn(resolve($request));
        Phake::when($middlewareThree)->post($response, $options)->thenReturn(resolve($response));

        $args = [
            $options,
            $middlewareThree,
            $middlewareOne,
            $middlewareTwo,
        ];

        $executioner = new MiddlewareExecutioner(...$args);
        $executioner->pre($request);
        $executioner->post($response);

        Phake::inOrder(
            Phake::verify($middlewareOne)->pre($request, $options),
            Phake::verify($middlewareTwo)->pre($request, $options),
            Phake::verify($middlewareThree)->pre($request, $options),
            Phake::verify($middlewareThree)->post($response, $options),
            Phake::verify($middlewareTwo)->post($response, $options),
            Phake::verify($middlewareOne)->post($response, $options)
        );
    }
}
