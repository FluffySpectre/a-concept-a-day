<?php

namespace DA\Framework\Routing;

/**
 * Interface which describes a Response
 * 
 * @author Björn Bosse
 */
interface ResponseInterface {
    /**
     * Returns the body of the Response
     * 
     * @return string
     */
    function getBody(): string;

    /**
     * Returns the header list of the Response
     * 
     * @return array
     */
    function getHeader(): array;

    /**
     * Returns the response code
     * 
     * @return int
     */
    function getStatus(): int;

    /**
     * Sets the body of the response to the given text
     * 
     * @param mixed $text
     * @return ResponseInterface
     */
    function text($text): ResponseInterface;

    /**
     * Encodes the data as JSON and sets it as the body of the response and returns it
     * 
     * @param mixed $data
     * @return ResponseInterface
     */
    function json($data): ResponseInterface;

    /**
     * Sets the body of the response to the given data without setting the content type header
     * 
     * @param mixed $data
     * @return ResponseInterface
     */
    function body($data): ResponseInterface;

    /**
     * Adds the given status code to the response and returns it
     * 
     * @param int $status
     * @return ResponseInterface
     */
    function status($status): ResponseInterface;

    /**
     * Adds a cookie to the response and returns it
     * 
     * @param string $name
     * @param string $value 
     * @param int|null $maxAge
     * @param string|null $path
     * @return ResponseInterface
     */
    function cookie(string $name, string $value, int|null $maxAge, string|null $path): ResponseInterface;

    /**
     * Adds the given header information to the response and returns it
     * 
     * @param string $name
     * @param string $value
     * @return ResponseInterface
     */
    function header(string $name, string $value): ResponseInterface;

    /**
     * Adds a location header to the response and returns it
     * 
     * @param string $url
     * @return ResponseInterface
     */
    function redirect(string $url): ResponseInterface;
}
