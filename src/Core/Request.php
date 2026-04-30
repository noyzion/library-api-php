<?php

class Request
{
    /**
     * Get the current HTTP request method (GET, POST, etc.)
     */
    public static function getMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Retrieve all URL query parameters from $_GET
     */
    public static function getQueryParams()
    {
        return $_GET;
    }

    /**
     * Parse and return the JSON request body as an associative array
     */
    public static function getBody()
    {
        // Read raw data from the request body
        $rawInput = file_get_contents("php://input");
        
        // Decode JSON into an array
        $data = json_decode($rawInput, true);
        
        // Return data or an empty array if decoding fails
        return $data ?? [];
    }
}