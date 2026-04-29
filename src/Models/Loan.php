<?php

// Include the Database Singleton class
require_once __DIR__ . '/../../config/Database.php';

class Loan {
    private $db;
    private $table = "loans";

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Get all loans with book and member details
     */
    public function getAll() {
        $query = "SELECT l.*, b.title as book_title, m.full_name as member_name 
                  FROM " . $this->table . " l
                  JOIN books b ON l.book_id = b.id
                  JOIN members m ON l.member_id = m.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a new loan record
     */
    public function borrowBook($book_id, $member_id) {
        $query = "INSERT INTO " . $this->table . " (book_id, member_id, loan_date, due_date) 
                  VALUES (:book_id, :member_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'book_id'   => $book_id,
            'member_id' => $member_id
        ]);
    }

    /**
     * Mark a book as returned
     */
    public function returnBook($loan_id) {
        $query = "UPDATE " . $this->table . " 
                  SET return_date = CURDATE() 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $loan_id]);
    }

    /**
     * Fetch all loans past their due date
     */
    public function getOverdueLoans() {
        $query = "SELECT l.*, b.title as book_title, m.full_name as member_name 
                  FROM " . $this->table . " l
                  JOIN books b ON l.book_id = b.id
                  JOIN members m ON l.member_id = m.id
                  WHERE l.return_date IS NULL 
                  AND l.due_date < CURDATE()";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}