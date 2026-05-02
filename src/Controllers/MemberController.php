<?php

class MemberController
{
    private Member $memberModel;

    public function __construct()
    {
        $this->memberModel = new Member();
    }

    /**
     * Display a list of all members
     * Route: GET /members
     */
    public function index()
    {
        $members = $this->memberModel->getAll();

        Response::success($members, "Members retrieved successfully");
    }

    /**
     * Display a specific member by their ID
     * Route: GET /members/{id}
     */
    public function show($id)
    {
        if (!is_numeric($id)) {
            Response::error("Invalid member ID", 400);
        }
        $member = $this->memberModel->getById($id);

        if ($member) {
            Response::success($member, "Member retrieved successfully");
        } else {
            Response::error("Member not found", 404);
        }
    }

    /**
     * Create a new member in the system
     * Route: POST /members
     */
    public function store()
    {
        $data = Request::getBody();

       $requiredFields = ['full_name', 'email', 'phone'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                Response::error("$field is required", 400);
            }
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error("Invalid email format", 400);
        }
        $allowedStatuses = ['active', 'suspended', 'expired'];

        if (isset($data['membership_status']) && !in_array($data['membership_status'], $allowedStatuses)) {
            Response::error("Invalid membership status", 400);
        }

        try {
            $newMemberId = $this->memberModel->createMember($data);
            Response::success(['id' => $newMemberId], "Member created successfully", 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                Response::error("Email already exists", 409);
            }

            Response::error("Database error", 500);
        }

    }

    /**
     * Update an existing member's information
     * Route: PUT /members/{id}
     */
    public function update($id)
    {
        if (!is_numeric($id)) {
            Response::error("Invalid member ID", 400);
        }

        $existingMember = $this->memberModel->getById($id);

        if (!$existingMember) {
            Response::error("Member not found", 404);
        }

        $data = Request::getBody();

        $requiredFields = ['full_name', 'email', 'phone', 'membership_status'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                Response::error("$field is required", 400);
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error("Invalid email format", 400);
        }

        $allowedStatuses = ['active', 'suspended', 'expired'];

        if (!in_array($data['membership_status'], $allowedStatuses)) {
            Response::error("Invalid membership status", 400);
        }
        try {
            $this->memberModel->updateMember($id, $data);
            Response::success(null, "Member updated successfully");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                Response::error("Email already exists", 409);
            }

            Response::error("Database error", 500);
        }
    }

    /**
     * Remove a member from the system
     * Route: DELETE /members/{id}
     */
    public function destroy($id)
    {
        if (!is_numeric($id)) {
            Response::error("Invalid member ID", 400);
        }
        $success = $this->memberModel->deleteById($id);

        if ($success) {
            Response::success(null, "Member deleted successfully");
        } else {
            Response::error("Member not found or could not be deleted", 404);
        }
    }
}