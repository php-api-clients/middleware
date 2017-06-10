<?php declare(strict_types=1);

namespace ApiClients\Tests\Foundation\Middleware\Annotation;

use ApiClients\Foundation\Middleware\Annotation\Priority as PriorityAnnotation;
use ApiClients\Foundation\Middleware\Priority;
use ApiClients\Tools\TestUtilities\TestCase;

class PriorityTest extends TestCase
{
    public function testPriority()
    {
        $priority = new PriorityAnnotation([Priority::FIRST]);
        self::assertSame(Priority::FIRST, $priority->property());
    }
}
