<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
final class Priority implements PriorityInterface
{
    /**
     * @var int
     */
    protected $priority;

    /**
     * @param array $priorities
     */
    public function __construct(array $priorities)
    {
        $this->priority = current($priorities);
    }

    /**
     * @return int
     */
    public function priority(): int
    {
        return $this->priority;
    }
}
