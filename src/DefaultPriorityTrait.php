<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

trait DefaultPriorityTrait
{
    /**
     * @return int
     */
    public function priority(): int
    {
        return Priority::DEFAULT;
    }
}
