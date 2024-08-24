<?php

namespace DA\Framework\Routing;

use DA\Framework\DI\DIContainerInterface;

/**
 * Implements functionality to resolve a callable function from a string
 * 
 * @author Björn Bosse
 */
class CallableResolver {
    protected $container;

    public function __construct(DIContainerInterface $container) {
        $this->container = $container;
    }

    public function resolve($toResolve): callable {
        if (is_callable($toResolve)) {
            return $this->bindToContainer($toResolve);
        }

        if (is_array($toResolve)) {
            $class = $toResolve[0];
            $method = "__invoke";
            if (count($toResolve) > 1) {
                $method = $toResolve[1];
            }
            $instance = new $class($this->container);
            return [$instance, $method];
        }

        throw new \InvalidArgumentException("Cant resolve callable!");
    }

    private function bindToContainer(callable $callable): callable {
        if (is_array($callable) && $callable[0] instanceof Closure) {
            $callable = $callable[0];
        }
        if ($this->container) {
            if ($callable instanceof \Closure) {
                $callable = $callable->bindTo($this->container);
            }
        }
        return $callable;
    }
}