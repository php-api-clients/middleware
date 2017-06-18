<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware\Locator;

use ApiClients\Foundation\Middleware\MiddlewareInterface;
use Psr\Container\ContainerInterface;

final class ContainerLocator implements Locator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $middleware): MiddlewareInterface
    {
        if ($this->container->has($middleware)) {
            $instance = $this->container->get($middleware);
            if ($instance instanceof MiddlewareInterface) {
                return $instance;
            }
        }

        throw new InvalidMiddlewareException();
    }
}
