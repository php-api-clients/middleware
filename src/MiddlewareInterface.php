<?php declare(strict_types=1);

namespace ApiClients\Foundation\Middleware;

/**
 * Middleware, when a new request is made an instance specifically for that request is made for each request.
 *
 * Priority ranging from 0 to 1000. Where 1000 will be executed first on `pre`/`post`/`error`
 * and 0 last on `pre`/`post`/`error`.
 */
interface MiddlewareInterface extends PreMiddlewareInterface, PostMiddlewareInterface, ErrorMiddlewareInterface
{
}
