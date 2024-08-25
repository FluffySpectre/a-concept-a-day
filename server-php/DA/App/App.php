<?php

namespace DA\App;

use DA\Framework\DI\DIContainer;
use DA\Framework\Routing\RequestInterface;
use DA\Framework\Routing\ResponseInterface;
use DA\Framework\Routing\Router;
use DA\Framework\Routing\Middleware\CORSMiddleware;
use DA\Framework\Routing\Middleware\ErrorHandlerMiddleware;

/**
 * The main app which sets up all dependencies and routes
 * 
 * @author Björn Bosse
 */
class App {
    public function run() {
        // setup dependencies
        $dependencies = [];
        $diContainer = new DIContainer($dependencies);

        // $basePath = Config::get("basepath");
        $basePath = "server-php/public";
        $router = new Router($diContainer, $basePath);
        $router->setAllowedHTTPMethods(["GET"]);

        // middleware
        $router->addGlobalMiddleware(new ErrorHandlerMiddleware());
        $router->addGlobalMiddleware(new CORSMiddleware());

        // setup all needed routes
        $router->get("", function (RequestInterface $request, ResponseInterface $response) {
            // $view = require __DIR__ . "/Views/ViewIndex.php";
            // return $response->text($view);

            $view = "<h1>Hello World from index!</h1>";
            return $response->text($view);
        });
        $router->get("json", function (RequestInterface $request, ResponseInterface $response) {
            // $view = require __DIR__ . "/Views/ViewIndex.php";
            // return $response->text($view);

            $view = ["name" => "Algorithm name", "summary" => "Algorithm summary"];
            return $response->json($view);
        });

        $router->run();
    }
}
