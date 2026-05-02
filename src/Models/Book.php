<?php

// Include the Database Singleton class
require_once __DIR__ . '/../../config/database.php';

class Book {
    private $db;
    private $table = "books";

    public function __construct() {
        // Initialize the database connection
        $this->db = Database::getConnection();
    }

    /**
     * Get all books with optional genre filtering
     */
    public function getAll($genre = null) {
        $query = "SELECT * FROM " . $this->table;
        
        if ($genre) {
            $query .= " WHERE genre = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$genre]);
        } else {
            $stmt = $this->db->prepare($query);
            $stmt->execute();
        }
    
        return $stmt->fetchAll();
    }

    /**
     * Get a single book by its ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Insert a new book into the database
     */
    public function createBook($data) {
        $query = "INSERT INTO " . $this->table . " (title, author, isbn, published_year, genre, total_copies, available_copies)
                  VALUES (:title, :author, :isbn, :published_year, :genre, :total_copies, :available_copies)";

        $stmt = $this->db->prepare($query);

         $stmt->execute([
            'title'            => $data['title'],
            'author'           => $data['author'],
            'isbn'             => $data['isbn'],
            'published_year'   => $data['published_year'] ?? null, 
            'genre'            => $data['genre'],
            'total_copies'     => $data['total_copies'] ?? 1, 
            'available_copies' => $data['total_copies'] ?? 1 
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Delete a book by its ID
     */
    public function deleteById($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
         $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Update a book by its ID
     */
   public function updateBook($id, $data) {
        $query = "UPDATE " . $this->table . " 
                SET title = :title, 
                    author = :author, 
                    genre = :genre, 
                    available_copies = :available_copies 
                WHERE id = :id";

        $stmt = $this->db->prepare($query);
        
        $stmt->execute([
            'title'            => $data['title'],
            'author'           => $data['author'],
            'genre'            => $data['genre'],
            'available_copies' => $data['available_copies'],
            'id'               => $id
        ]);
       return $stmt->rowCount() > 0;

    }   
}