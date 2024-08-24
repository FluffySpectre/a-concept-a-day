<?php

namespace DA\Framework\Routing;

use DA\Framework\DI\DIContainerInterface;

/**
 * Implements basic routing functionality
 *
 * @example $router->post("api/users", function($request, $response) { return $response->json($myData); });
 * 
 * @author Björn Bosse
 */
class Router {
    private DIContainerInterface $container;
    private CallableResolver $callableResolver;
    private $routes = [];
    private $basePath;
    private $allowedHTTPMethods = ["GET"];
    private $globalMiddlewares = [];

    /**
     * Constructor
     * 
     * @param DIContainerInterface $container
     * @param string $basePath
     */
    public function __construct(DIContainerInterface $container, string $basePath = "") {
        $this->container = $container;
        $this->callableResolver = new CallableResolver($this->container);
        $this->setBasePath($basePath);
    }

    /**
     * Set the base path for the routing
     * 
     * @param string $basePath The base routing path (e.g. api/)
     */
    public function setBasePath($basePath) {
        $this->basePath = $basePath;
    }

    /**
     * Set the allowed HTTP methods for the routing 
     * 
     * @param array $allowedMethods The methods which are allowed to process
     */
    public function setAllowedHTTPMethods($allowedMethods) {
        $this->allowedHTTPMethods = $allowedMethods;
    }

    /**
     * Adds a global middleware function, which gets called on every route
     * 
     * @param callable $middleware The middleware function
     */
    public function addGlobalMiddleware($middleware) {
        $this->globalMiddlewares[] = $middleware;
    }

    /**
     * HTTP-GET function
     * 
     * @param string $expression The route
     * @param callable $function The route callback
     * @param array $middlewares Array of middlewares to be run for this route
     */
    public function get($expression, $function, array $middlewares = []) {
        $this->add($expression, "GET", $function, $middlewares);
    }

    /**
     * HTTP-POST function
     * 
     * @param string $expression The route
     * @param callable $function The route callback
     * @param array $middlewares Array of middlewares to be run for this route
     */
    public function post($expression, $function, array $middlewares = []) {
        $this->add($expression, "POST", $function, $middlewares);
    }

    /**
     * Low-level route add function
     * 
     * @param string $expression The route to add
     * @param string $method The HTTP-method (e.g. GET, POST, PUT etc.)
     * @param callable $function The route callback
     * @param array $middlewares Array of middlewares to be run for this route
     */
    public function add($expression, $method, $function, array $middlewares = []) {
        if (!in_array($method, $this->allowedHTTPMethods)) {
            return;
        }

        $this->routes[$method][] = [
            "expression" => $expression,
            "function" => $function,
            "middlewares" => $middlewares
        ];
    }

    /**
     * Runs the actual routing logic
     */
    public function run() {
        // construct request and reponse objects
        $request = RequestFactory::create($this->basePath);
        $response = new Response();

        foreach ($this->routes[$request->getRequestMethod()] as $route) {
            if ($this->basePath) {
                // add / if its not empty
                if ($route["expression"] != "") {
                    $route["expression"] = "/" . $route["expression"];
                }

                $route["expression"] = "(" . $this->basePath . ")" . $route["expression"];
            }

            // add "find string start" automatically
            $route["expression"] = "^" . $route["expression"];

            // add "find string end" automatically
            $route["expression"] = $route["expression"] . "$";

            // check match
            if (preg_match("#{$route["expression"]}#", $request->getPath(), $matches)) {
                // always remove first element. This contains the whole string
                array_shift($matches);

                if ($this->basePath) {
                    // remove basepath
                    array_shift($matches);
                }

                // merge the global middlewares with the ones from the current route
                $middlewares = array_merge($this->globalMiddlewares, $route["middlewares"]);

                // resolve callable route function
                $routeCallable = $this->callableResolver->resolve($route["function"]);

                // the actual route as last "middleware"
                $nextCallable = function ($req, $res) use ($routeCallable, $matches) {
                    return call_user_func($routeCallable, $req, $res, $matches);
                };

                // build middleware pipeline (from last to first)
                while ($middleware = array_pop($middlewares)) {
                    $resolvedMiddleware = $this->callableResolver->resolve($middleware);
                    $nextCallable = function ($request, $response) use ($resolvedMiddleware, $nextCallable) {
                        return call_user_func($resolvedMiddleware, $request, $response, $nextCallable);
                    };
                }

                // kick off the middleware pipeline
                $response = call_user_func($nextCallable, $request, $response);
            }
        }

        // dispatch the generated response
        (new ResponseDispatcher)->dispatch($response);
    }
}
