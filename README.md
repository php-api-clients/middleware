# API Client middleware for PHP 7.x

[![Build Status](https://travis-ci.org/php-api-clients/middleware.svg?branch=master)](https://travis-ci.org/php-api-clients/middleware)
[![Latest Stable Version](https://poser.pugx.org/api-clients/middleware/v/stable.png)](https://packagist.org/packages/api-clients/middleware)
[![Total Downloads](https://poser.pugx.org/api-clients/middleware/downloads.png)](https://packagist.org/packages/api-clients/middleware/stats)
[![Code Coverage](https://scrutinizer-ci.com/g/php-api-clients/middleware/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-api-clients/middleware/?branch=master)
[![License](https://poser.pugx.org/api-clients/middleware/license.png)](https://packagist.org/packages/api-clients/middleware)
[![PHP 7 ready](http://php7ready.timesplinter.ch/php-api-clients/middleware/badge.svg)](https://appveyor-ci.org/php-api-clients/middleware)

This library contains the a async `MiddlewareRunner` used in our api clients. It 
provides an interface for all middlewares. Middlewares are ordered by priority
when they where added to the `MiddlewareRunner`. Order of middlewares with the
same priority is not guaranteed.

A number of traits are provided for your convenience, if your middleware
implementation does not require all the methods defined in the 
`MiddlewareInterface`.

## Locator
The locator can be used by your application to fetch middleware instances.
It will check whether the created instance implements the `MiddlewareInterface`.
Currently the only provided locator is the `ContainerLocator` which accepts a
`Psr\Container\ContainerInterface` to fetch your middleware instances.

## Example
```php
    $container = /* ... */;
    $locator = new ContainerLocator($container);
    $middlewares = [];
    
    $config = [
        'middlewares' => [/*...*/]
        'options' => [/*...*/]
    ];
       
    foreach ($config['middlewares'] as $middleware) {
       $middlewares[] = $locator->get($middleware);
    }
    
    $runner = new MiddlewareRunner($config['options'], $middelwares);
    
    $runner->pre($request)->then(function ($request) use ($options) {
        return resolve($this->browser->send(
            $request
        ));
    }, function (ResponseInterface $response) {
        return resolve($response);
    })->then(function (ResponseInterface $response) use ($runner) {
        return $runner->post($response);
    })->otherwise(function (Throwable $throwable) use ($runner) {
        return reject($runner->error($throwable));
    });
```


# License

The MIT License (MIT)

Copyright (c) 2017 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
