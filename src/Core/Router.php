<?php

class Router {
    private $routes = [];

    public function add($method, $path, $controller, $action) {
        $this->routes[] = [
            'method' => $method,
            'path' => trim($path, '/'),
            'controller' => $controller,
            'action' => $action
        ];
    }

    public function dispatch($requestedUri, $requestedMethod) {
        // 1. Clean the URI from query strings and trailing slashes
        $urlPath = parse_url($requestedUri, PHP_URL_PATH);
        $urlPath = trim($urlPath, '/');
        
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        $urlPath = str_replace($scriptName, '', $urlPath);
        $urlPath = trim($urlPath, '/');
        // 2. Break the requested URL into segments (e.g., ['books', '123'])
        $urlParts = $urlPath === '' ? [] : explode('/', $urlPath);

        foreach ($this->routes as $route) {
            // Break the registered route path into segments (e.g., ['books', '{id}'])
            $routeParts = $route['path'] === '' ? [] : explode('/', $route['path']);
            // Check if segments count and HTTP method match
            if (count($urlParts) === count($routeParts) && $route['method'] === $requestedMethod) {
                
                $params = [];
                $match = true;

                // Loop through segments to find matches or parameters
                for ($i = 0; $i < count($routeParts); $i++) {
                    // Check if the segment is a dynamic parameter (starts with '{')
                    if (strpos($routeParts[$i], '{') === 0) {
                        $params[] = $urlParts[$i]; // Capture the value (e.g., 123)
                    } 
                    // Check if static segments match exactly
                    elseif ($routeParts[$i] !== $urlParts[$i]) {
                        $match = false;
                        break;
                    }
                }

                if ($match) {
                    $controllerName = $route['controller'];
                    $action = $route['action'];

                    // Instantiate the controller and call the action with parameters
                    $controller = new $controllerName();
                    call_user_func_array([$controller, $action], $params);
                    return;
                }
            }
        }

        // If no route matches, return a 404 error using our Response class
        Response::error("Page not found", 404);
    }
}