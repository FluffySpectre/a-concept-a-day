<?php

namespace DA\Framework\Routing;

/**
 * Interface which describes a Request
 * 
 * @author Björn Bosse
 */
interface RequestInterface {
    /**
     * Returns the body of the Request
     * 
     * @return array
     */
    function getBody(): array;

    /**
     * Returns the URL parameters of the Request
     * 
     * @return array
     */
    function getURLParams(): array;

    /**
     * Returns the header list of the request
     * 
     * @return array
     */
    function getHeader(): array;

    /**
     * Returns the request method like GET, POST etc.
     * 
     * @return string
     */
    function getRequestMethod(): string;

    /**
     * Returns the server variables
     * 
     * @return array
     */
    function getServerVars(): array;

    /**
     * Returns the URI path
     * 
     * @return string
     */
    function getPath(): string;

    /**
     * Returns the URI base path
     * 
     * @return string
     */
    function getBasePath(): string;

    /**
     * Returns the remote address of the client
     * 
     * @return string
     */
    function getRemoteAddress(): string;

    /**
     * Returns the cookie parameters
     * 
     * @return array
     */
    function getCookieParams(): array;
}
