<?php

namespace DA\Framework\Routing;

use DA\Framework\Routing\MiddlewareInterface;
use DA\Framework\Routing\RequestInterface;
use DA\Framework\Routing\ResponseInterface;
use Throwable;

/**
 * Implements an error handler middleware which catches 
 * unhandled exceptions in the router
 * 
 * @author Björn Bosse
 */
class ErrorHandlerMiddleware implements MiddlewareInterface {
    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next) {
        try {
            return $next($request, $response);
        } catch (Throwable $e) {
            return $this->handleException($response, $e);
        }
    }

    /**
     * The error handler
     * 
     * @param ResponseInterface $response
     * @param Throwable $e
     */
    private function handleException(ResponseInterface $response, Throwable $e) {
        return $response->status(HTTPCodes::INTERNAL_ERROR)->json(["message" => $e->getMessage()]);
    }
}
