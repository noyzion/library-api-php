<?php

// Include the Database Singleton class
require_once __DIR__ . '/../../config/database.php';

class Member {
    private $db;
    private $table = "members";

    public function __construct() {
        // Initialize the database connection
        $this->db = Database::getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createMember($data) {
        $query = "INSERT INTO " . $this->table . " (full_name, email, phone, membership_status
                  VALUES (:full_name, :email, :phone, :membership_status)";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'full_name'            => $data['full_name'],
            'email'           => $data['email'],
            'phone'             => $data['phone'],
            'membership_status'   => $data['membership_status']
        ]);
    }

   
    public function deleteById($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Update a book by its ID
     */
   public function updateMember($id, $data) {
        $query = "UPDATE " . $this->table . " 
                SET full_name = :full_name, 
                    email = :email, 
                    phone = :phone, 
                    membership_status = :membership_status
                WHERE id = :id";

        $stmt = $this->db->prepare($query);
        
        return $stmt->execute([
            'full_name'            => $data['full_name'],
            'email'           => $data['email'],
            'phone'            => $data['phone'],
            'membership_status' => $data['membership_status'],
            'id'               => $id
        ]);
    }   
}