<?php

class Response
{
    public static function success($data, $message, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        $response = [
            "success" => true,
            "data" => $data,
            "message" => $message
        ];

        echo json_encode($response);
        exit;
    }
    public static function error($errorMessage, $code = 404)     
        {
            header('Content-Type: application/json');
            http_response_code($code);
            $response = [
                "success" => false,
                "error" => $errorMessage,
                "code" => $code
            ];

            echo json_encode($response);
            exit;
        }


}