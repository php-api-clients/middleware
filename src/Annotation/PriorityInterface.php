<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware\Annotation;

interface PriorityInterface
{
    /**
     * @return int
     */
    public function priority(): int;
}
