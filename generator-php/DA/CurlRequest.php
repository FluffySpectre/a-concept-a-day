<?php

namespace DA;

/**
 * Provides functions to submit a HTTP request
 * 
 * Available HTTP methods: GET, POST, PUT, DELETE
 * 
 * @author Björn Bosse
 */
class CurlRequest {
    private $header = array();
    private $timeout = 15;

    /**
     * Sets the HTTP header of this request
     * 
     * @param array $header The header array
     */
    public function setHeader($header) {
        $this->header = $header;
    }

    /**
     * Adds a single header to the header array
     * 
     * @param string $header The header line to add
     */
    public function addHeader($header) {
        $this->header[] = $header;
    }
    
    /**
     * Sets the maximum time a request has to execute
     * 
     * @param integer $timeout The time in seconds
     */
    public function setTimeout($timeout) {
        $this->timeout = $timeout;
    }

    /**
     * Dispatches a POST-Request
     * 
     * @param string $url The URL of the request
     * @param array $fields The POST-Parameter
     * @param string $bodyType The type of serialization
     * @return string The response from the server
     */
    public function post($url, $fields, $bodyType = "form") {
        // encode the parameters
        $fieldsString = $this->getParameterString($fields, $bodyType);

        // open connection
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if (count($this->header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        // execute post
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        // close connection
        curl_close($ch);

        return array($result, $responseInfo);
    }
    
    /**
     * Dispatches a GET-Request
     * 
     * @param string $url The URL of the request
     * @param array $fields The GET-Parameter
     * @return string The response from the server
     */
    public function get($url, $fields = array()) {
        // encode the parameters and add them to the url
        if (is_array($fields) && count($fields) > 0) {
            $fieldsString = $this->getParameterString($fields);
            $url .= "?" . $fieldsString;
        }

        // open connection
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if (count($this->header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        // execute post
        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        // close connection
        curl_close($ch);

        return array($result, $responseInfo);
    }
    
    /**
     * Dispatches a PUT-Request
     * 
     * @param string $url The URL of the request
     * @param array $fields The PUT-Parameter
     * @return string The response from the server
     */
    public function put($url, $fields = array()) {
        // encode the parameters
        $fieldsString = $this->getParameterString($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if (count($this->header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        curl_close($ch);

        return array($result, $responseInfo);
    }
    
    /**
     * Dispatches a DELETE-Request
     * 
     * @param string $url The URL of the request
     * @param array $fields The DELETE-Parameter
     * @return array The response and resultinfo from the server
     */
    public function delete($url, $fields = array()) {
        // encode the parameters
        $fieldsString = $this->getParameterString($fields);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout); 
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        if (count($this->header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->header);
        }

        $result = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        curl_close($ch);

        return array($result, $responseInfo);
    }
    
    /**
     * Converts a parameter array into a GET parameter string
     * 
     * @param array $params The parameter array
     * @return string The parameter string
     */
    private function getParameterString($params, $formatType = "form") {
        if (!is_array($params)) return "";

        if ($formatType === "json") {
            return json_encode($params);

        } else {
            if (count($params) > 0) {
                $paramsString = "";
                foreach ($params as $key => $value) {
                    $paramsString .= $key . "=" . urlencode($value) . "&";
                }
                rtrim($paramsString, "&");
    
                return $paramsString;
            }
        }

        return "";
    }
}

// Exceptions

/**
 * Will be thrown if a curl request fails
 * 
 * @author Björn Bosse
 */
class CurlRequestException extends \Exception {}