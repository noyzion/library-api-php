<?php

class Database { 
    // Database connection parameters
    private const HOST = "localhost";
    private const DB_NAME = "library_db";
    private const USERNAME = "root";
    private const PASSWORD = "";

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
                // Data Source Name (DSN) defines the connection details
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DB_NAME . ";charset=utf8";
                
                // Initialize the PDO connection
                self::$instance = new PDO($dsn, self::USERNAME, self::PASSWORD);
                
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