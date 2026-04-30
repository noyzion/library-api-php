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
    * Create a new loan record and decrement available copies 
    */
    public function borrowBook($book_id, $member_id) {
        try {
            $this->db->beginTransaction();
        $query = "INSERT INTO " . $this->table . " (book_id, member_id, loan_date, due_date) 
                  VALUES (:book_id, :member_id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY))";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'book_id'   => $book_id,
            'member_id' => $member_id
        ]);

        $updateBook = "UPDATE books SET available_copies = available_copies - 1 WHERE id = :book_id";
            $updateStmt = $this->db->prepare($updateBook);
            $updateStmt->execute(['book_id' => $book_id]);
        return $this->db->lastInsertId();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Mark a book as returned and increment available copies[cite: 1]
     */
    public function returnBook($loan_id) {
      try {
            $loanQuery = "SELECT book_id FROM " . $this->table . " WHERE id = :id AND status = 'active'";
            $loanStmt = $this->db->prepare($loanQuery);
            $loanStmt->execute(['id' => $loan_id]);
            $loan = $loanStmt->fetch();

            if (!$loan) return false;

            $this->db->beginTransaction();

            $query = "UPDATE " . $this->table . " 
                      SET return_date = CURDATE(), 
                          status = 'returned' 
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $loan_id]);

            $updateBook = "UPDATE books SET available_copies = available_copies + 1 WHERE id = :book_id";
            $updateStmt = $this->db->prepare($updateBook);
            $updateStmt->execute(['book_id' => $loan['book_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
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

    public function isAlreadyBorrowed($memberId, $bookId)
    {
        $query = "SELECT id FROM " . $this->table . " 
                WHERE book_id = :book_id 
                AND member_id = :member_id 
                AND status = 'active' 
                LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':book_id' => $bookId,
            ':member_id' => $memberId
        ]);

        return $stmt->fetch() ? true : false;
    }
}