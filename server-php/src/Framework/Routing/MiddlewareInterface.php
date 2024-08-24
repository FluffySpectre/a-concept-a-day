<?php

namespace DA\Framework\Routing;

use DA\Framework\Routing\RequestInterface;
use DA\Framework\Routing\ResponseInterface;

/**
 * Describes a middleware
 * 
 * @author Björn Bosse
 */
interface MiddlewareInterface {
    /**
     * Gets called by the router before the core app has run
     */
    function __invoke(RequestInterface $request, ResponseInterface $response, callable $next);
}
