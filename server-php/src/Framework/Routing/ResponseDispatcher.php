<?php

namespace DA\Framework\Routing;

/**
 * Implements a ResponseDispatcher
 * 
 * @author Björn Bosse
 */
class ResponseDispatcher implements ResponseDispatcherInterface {
    /**
     * @inheritDoc
     */
    public function dispatch(ResponseInterface $response) {
        // render the output
        http_response_code((int) $response->getStatus());
        foreach ($response->getHeader() as $header) {
            header($header);
        }
        echo $response->getBody();
    }
}
