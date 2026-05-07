<?php

class Database { 


    // Holds the single instance of the PDO connection
    private static $instance = null;

    /**
     * Private constructor to prevent direct instantiation of the class.
     * This is a key requirement for the Singleton pattern.
     */
    private function __construct() {}

    /**
     * Returns the single instance of the database connection.
     * If no connection exists, it creates one using PDO.
     */
    public static function getConnection() {
        if (self::$instance === null) {
            try {
                $host = $_ENV['DB_HOST'] ?? 'localhost';
                $dbName = $_ENV['DB_NAME'] ?? 'library_db';
                $username = $_ENV['DB_USERNAME'] ?? 'root';
                $password = $_ENV['DB_PASSWORD'] ?? '';

                // Data Source Name (DSN) defines the connection details
                $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

                
                // Initialize the PDO connection
                self::$instance = new PDO($dsn, $username, $password);

                
                // Set error mode to Exceptions for better error handling
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);                
                // Set default fetch mode to Associative Array for easier JSON conversion
                self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
            } catch (PDOException $e) {
                // Terminate script execution and display the error message if connection fails
                Response::error("Database connection failed", 500);  
            }
        }
        
        // Return the existing or newly created connection instance
        return self::$instance;
    }
}