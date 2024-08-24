<?php

namespace DA\Framework\Routing;

/**
 * Implements a factory to create a request instance
 * 
 * @author Björn Bosse
 */
class RequestFactory {
    /**
     * Returns an instance of an request and initializes it with PHP super globals
     * 
     * @param string $basePath
     * @return Request
     */
    public static function create(string $basePath) {
        $uri = $_SERVER["REQUEST_URI"];
        $method = $_SERVER["REQUEST_METHOD"];

        $parsedUrl = parse_url($uri);
        $path = "";
        if (isset($parsedUrl["path"])) {
            $path = trim($parsedUrl["path"], "/");
        }

        return new Request($basePath, $path, $_POST, $_GET, getallheaders(), $method, $_SERVER, $_COOKIE);
    }
}