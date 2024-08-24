<?php

namespace DA\Framework\Routing;

/**
 * Implements a Response
 * 
 * @author Björn Bosse
 */
class Response implements ResponseInterface {
    protected $body = "";
    protected $header = array();
    protected $status = HTTPCodes::OK;

    /**
     * @inheritDoc
     */
    public function getBody(): string {
        return $this->body;
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
    public function getStatus(): int {
        return $this->status;
    }

    /**
     * @inheritDoc 
     */
    public function text($text): ResponseInterface {
        $this->body = $text;
        $this->header[] = "Content-Type: text/html; charset=utf-8";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function json($data): ResponseInterface {
        $this->body = json_encode($data);
        $this->header[] = "Content-Type: application/json; charset=utf-8";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function status($status): ResponseInterface {
        $this->status = $status;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function cookie(string $name, string $value, int|null $maxAge, string|null $path): ResponseInterface {
        $maxAgeAttribute = $maxAge ? "; Max-Age=$maxAge" : "";
        $pathAttribute = $path ? "; Path=$path" : "";
        $this->header[] = "Set-Cookie: $name=$value; HttpOnly $maxAgeAttribute $pathAttribute";
        return $this;
    }
}
