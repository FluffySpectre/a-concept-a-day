<?php

namespace DA\Framework\DI;

/**
 * Defines a Dependecy Container
 * 
 * @author Björn Bosse
 */
interface DIContainerInterface {
    /**
     * Returns the instance with the given id
     * 
     * @param string $id
     * @return mixed
     */
    public function get(string $id): mixed;

    /**
     * Checks if an instance with the given id
     * exists in the container
     * 
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool;
}
