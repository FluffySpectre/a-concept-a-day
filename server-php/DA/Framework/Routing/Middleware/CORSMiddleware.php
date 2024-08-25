<?php

namespace DA\Framework\Routing\Middleware;

use DA\Framework\Routing\MiddlewareInterface;
use DA\Framework\Routing\RequestInterface;
use DA\Framework\Routing\ResponseInterface;

/**
 * Implements an middleware to add CORS headers to the response
 * 
 * @author Björn Bosse
 */
class CORSMiddleware implements MiddlewareInterface {
    private array $allowedMethods;

    /**
     * Constructor
     * 
     * @param array $allowedMethods Supported http methods
     */
    public function __construct($allowedMethods = ["GET", "OPTIONS"]) {
        $this->allowedMethods = $allowedMethods;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next) {
        // do CORS setup
        $serverVars = $request->getServerVars();
        if (isset($serverVars["HTTP_ORIGIN"])) {
            $response = $response
                ->header("Access-Control-Allow-Origin", $serverVars["HTTP_ORIGIN"])
                ->header("Access-Control-Allow-Credentials", "true")
                ->header("Access-Control-Max-Age", "0"); // no cache
        }

        // access-control headers are received during OPTIONS requests
        if ($serverVars["REQUEST_METHOD"] == "OPTIONS") {
            if (isset($serverVars["HTTP_ACCESS_CONTROL_REQUEST_METHOD"]))
                $response = $response->header("Access-Control-Allow-Methods", implode(", ", $this->allowedMethods));

            if (isset($serverVars["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]))
                $response = $response->header("Access-Control-Allow-Headers", "{$serverVars["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"]}");

            return $response;

            // exit(0);
        }

        return $next($request, $response);
    }
}
