<?php

// Include the Database Singleton class
require_once __DIR__ . '/../../config/database.php';

class Member {
    private $db;
    private $table = "members";

    /**
     * Initialize connection using the Database Singleton
     */
    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Fetch all records from the members table
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Retrieve a single member by their unique ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Insert a new member into the database
     */
    public function createMember($data) {
    // SQL query with named placeholders for security
    $query = "INSERT INTO " . $this->table . " (full_name, email, phone, membership_status)
              VALUES (:full_name, :email, :phone, :membership_status)";

    $stmt = $this->db->prepare($query);

    $stmt->execute([
        'full_name'         => $data['full_name'],
        'email'             => $data['email'],
        'phone'             => $data['phone'],
        'membership_status' => $data['membership_status'] ?? 'active'
    ]);
    return $this->db->lastInsertId();

}

    /**
     * Permanently remove a member record by ID
     */
    public function deleteById($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;

    }

    /**
     * Update existing member information
     */
    public function updateMember($id, $data) {
        $query = "UPDATE " . $this->table . " 
                SET full_name = :full_name, 
                    email = :email, 
                    phone = :phone, 
                    membership_status = :membership_status
                WHERE id = :id";

        $stmt = $this->db->prepare($query);
        
        $stmt->execute([
            'full_name'         => $data['full_name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'membership_status' => $data['membership_status'],
            'id'                => $id
        ]);

        return $stmt->rowCount() > 0;
    }
}