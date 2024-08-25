<?php

namespace DA\Framework\Routing;

/**
 * Implements a Request
 * 
 * @author Björn Bosse
 */
class Request implements RequestInterface {
    protected string $basePath;
    protected string $url;
    protected array $body;
    protected array $urlParams;
    protected array $header;
    protected string $requestMethod;
    protected array $serverVars;
    protected string $remoteAddress;
    protected array $cookieParams;

    /**
     * Constructor
     */
    public function __construct($basePath, $url, $body, $urlParams, $header, $requestMethod, $serverVars, $cookieParams) {
        $this->basePath = $basePath;
        $this->url = $url;
        $this->body = $body ? $body : [];
        $this->urlParams = $urlParams ? $urlParams : [];
        $this->header = $header ? $header : [];
        $this->requestMethod = $requestMethod;
        $this->serverVars = $serverVars ? $serverVars : [];
        $this->remoteAddress = $serverVars["REMOTE_ADDR"] ?? "";
        $this->cookieParams = $cookieParams ? $cookieParams : [];
    }

    /**
     * @inheritDoc
     */
    public function getBody(): array {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function getURLParams(): array {
        return $this->urlParams;
    }

    /**
     * @inheritDoc
     */
    public function getHeader(): array {
        return $this->header;
    }

    /**
     * @inheritDoc
     */
    public function getRequestMethod(): string {
        return $this->requestMethod;
    }

    /**
     * @inheritDoc
     */
    public function getServerVars(): array {
        return $this->serverVars;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string {
        return parse_url($this->url, PHP_URL_PATH);
    }

    /**
     * @inheritDoc
     */
    public function getBasePath(): string {
        return $this->basePath;
    }

    /**
     * @inheritDoc
     */
    public function getRemoteAddress(): string {
        return $this->remoteAddress;
    }

    /**
     * @inheritDoc
     */
    public function getCookieParams(): array {
        return $this->cookieParams;
    }
}
