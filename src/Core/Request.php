<?php

class Request
{

public static function getMethod()
{
    return $_SERVER['REQUEST_METHOD'];
}

public static function getQueryParams()
{
    return $_GET;
}

public static function getBody()
{
    $rawInput = file_get_contents("php://input");
    
    $data = json_decode($rawInput, true);
    
    return $data ?? [];
}
}