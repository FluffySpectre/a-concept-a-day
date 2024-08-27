<?php

namespace DA\App;

use DA\Framework\Config\Config;
use DA\Framework\DI\DIContainer;
use DA\Framework\Routing\RequestInterface;
use DA\Framework\Routing\ResponseInterface;
use DA\Framework\Routing\Router;
use DA\Framework\Routing\Middleware\CORSMiddleware;
use DA\Framework\Routing\Middleware\ErrorHandlerMiddleware;
use DA\Framework\Templating\TemplateRenderer;

/**
 * The main app which sets up all dependencies and routes
 * 
 * @author Björn Bosse
 */
class App {
    public function run() {
        // Setup dependencies
        $dependencies = [
            "AlgorithmRepository" => function () {
                return new AlgorithmRepository(__DIR__ . "/../../algorithms");
            }
        ];
        $diContainer = new DIContainer($dependencies);

        $basePath = Config::get("BASE_PATH");
        $router = new Router($diContainer, $basePath);
        $router->setAllowedHTTPMethods(["GET"]);

        // Middleware
        $router->addGlobalMiddleware(new ErrorHandlerMiddleware());
        $router->addGlobalMiddleware(new CORSMiddleware());

        // Setup all needed routes
        $router->get("", function (RequestInterface $request, ResponseInterface $response) {
            $templateRenderer = new TemplateRenderer(__DIR__ . "/views/index");

            $algorithmRepository = $this->get("AlgorithmRepository");
            $algorithm = $algorithmRepository->getLatestAlgorithm();

            $renderedView = $templateRenderer->render($algorithm);
            return $response->text($renderedView);
        });

        $router->get("json", function (RequestInterface $request, ResponseInterface $response) {
            $algorithmRepository = $this->get("AlgorithmRepository");
            $algorithm = $algorithmRepository->getLatestAlgorithm();
            if (!$algorithm) {
                return $response->status(404)->text("No daily algorithm found!");
            }

            return $response->json($algorithm);
        });

        $router->get("rss", function (RequestInterface $request, ResponseInterface $response) {
            $templateRenderer = new TemplateRenderer(__DIR__ . "/views/rss");

            $algorithmRepository = $this->get("AlgorithmRepository");
            $algorithms = $algorithmRepository->getAlgorithms();

            $renderedView = $templateRenderer->render(["algorithms" => $algorithms]);
            return $response
                ->body($renderedView)
                ->header("Content-Type", "application/rss+xml");
        });

        $router->get("prev/(.*)", function (RequestInterface $request, ResponseInterface $response, $args) {
            // TODO: validate date with validator middleware
            $date = $args[0];
            
            $algorithmRepository = $this->get("AlgorithmRepository");
            $algorithm = $algorithmRepository->getAlgorithmOfDate($date);
            if (!$algorithm) {
                // Redirect to index page, if no algorithm for given date was found
                return $response->status(301)->redirect("/");
            }

            $templateRenderer = new TemplateRenderer(__DIR__ . "/views/index");
            $renderedView = $templateRenderer->render($algorithm);
            return $response->text($renderedView);
        });

        $router->run();
    }
}
