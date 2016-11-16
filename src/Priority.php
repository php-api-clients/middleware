<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

final class Priority
{
    const FIRST = 1000;
    const EARLY = 750;
    const DEFAULT = 500;
    const LATE = 250;
    const LAST = 0;
}
