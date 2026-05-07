<?php

/**
 * Main Entry Point - index.php
 * This file handles all incoming requests by loading the core classes,
 * registering the routes, and dispatching the request to the correct controller.
 */

// 2. Load Core classes
require_once __DIR__ . '/src/Core/Response.php';
require_once __DIR__ . '/src/Core/Request.php';
require_once __DIR__ . '/src/Core/Router.php';

// 3. Load Database configuration and Models
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/src/Models/Book.php';
require_once __DIR__ . '/src/Models/Member.php';
require_once __DIR__ . '/src/Models/Loan.php';

// 4. Load Controllers
require_once __DIR__ . '/src/Controllers/BookController.php';
require_once __DIR__ . '/src/Controllers/MemberController.php';
require_once __DIR__ . '/src/Controllers/LoanController.php';

// 5. Initialize the Router
$router = new Router();

// 6. Books Routes
$router->add('GET', 'books', 'BookController', 'index');
$router->add('GET', 'books/{id}', 'BookController', 'show');
$router->add('POST', 'books', 'BookController', 'store');
$router->add('PUT', 'books/{id}', 'BookController', 'update');
$router->add('DELETE', 'books/{id}', 'BookController', 'destroy');

// 7. Members Routes
$router->add('GET', 'members', 'MemberController', 'index');
$router->add('GET', 'members/{id}', 'MemberController', 'show');
$router->add('POST', 'members', 'MemberController', 'store');
$router->add('PUT', 'members/{id}', 'MemberController', 'update');
$router->add('DELETE', 'members/{id}', 'MemberController', 'destroy');

// 8. Loans Routes
$router->add('GET', 'loans', 'LoanController', 'index');
$router->add('POST', 'loans', 'LoanController', 'store');
$router->add('PUT', 'loans/{id}/return', 'LoanController', 'returnBook'); 
$router->add('GET', 'overdue/loans', 'LoanController', 'overdue');

// 9. Dispatch the request based on URL and HTTP Method
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);