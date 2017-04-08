<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

final class Priority
{
    const FIRST       = 1000;
    const SECOND      = 999;
    const THIRD       = 998;
    const EARLY       = 750;
    const DEFAULT     = 500;
    const LATE        = 250;
    const THIRD_LAST  = 2;
    const SECOND_LAST = 1;
    const LAST        = 0;
}
