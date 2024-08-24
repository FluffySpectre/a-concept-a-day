<?php

namespace DA\Framework\Routing;

/**
 * Interface which describes a ResponseDispatcher
 * 
 * @author Björn Bosse
 */
interface ResponseDispatcherInterface {
    /**
     * Outputs the given response
     * 
     * @param ResponseInterface $response The response to dispatch
     */
    function dispatch(ResponseInterface $response);
}
