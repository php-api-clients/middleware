<?php

namespace ApiClients\Tests\Foundation\Middleware\Locator;

use ApiClients\Foundation\Middleware\Locator\ContainerLocator;
use ApiClients\Tests\Foundation\Middleware\DummyMiddleware;
use Interop\Container\ContainerInterface;

class ContainerLocatorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetSuccess()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(DummyMiddleware::class)
            ->willReturn(new DummyMiddleware());
        $container->expects($this->once())
            ->method('has')
            ->with(DummyMiddleware::class)
            ->willReturn(true);
        $locator = new ContainerLocator($container);

        $this->assertInstanceOf(DummyMiddleware::class, $locator->get(DummyMiddleware::class));
    }

    /**
     * @expectedException \ApiClients\Foundation\Middleware\Locator\InvalidMiddlewareException
     */
    public function testNotFound()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with(DummyMiddleware::class)
            ->willReturn(true);
        $locator = new ContainerLocator($container);
        $locator->get(DummyMiddleware::class);
    }

    /**
     * @expectedException \ApiClients\Foundation\Middleware\Locator\InvalidMiddlewareException
     */
    public function testInvalidMiddleware()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->willReturn(true);

        $container->expects($this->once())
            ->method('has')
            ->with(\stdClass::class)
            ->willReturn(new \stdClass);

        $locator = new ContainerLocator($container);
        $locator->get(\stdClass::class);
    }
}
