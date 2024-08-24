<?php

namespace DA\Framework\DI;

use Exception;

/**
 * Implements a standard Dependency Container
 * 
 * @author Björn Bosse
 */
class DIContainer implements DIContainerInterface {
    protected $container = [];

    /**
     * Constructor
     */
    public function __construct(array $container = []) {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): mixed {
        if (array_key_exists($id, $this->container)) {
            return call_user_func($this->container[$id], $this);
        }
        throw new Exception("Dependency not found: $id");
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool {
        return isset($this->container[$id]);
    }
}