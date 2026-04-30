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

        if (!isset($data['full_name']) || !isset($data['email'])) {
            Response::error("Full Name and Email are required", 400); 
        }

        $newMemberId = $this->memberModel->createMember($data);

        Response::success(['id' => $newMemberId], "Member created successfully", 201);
    }

    /**
     * Update an existing member's information
     * Route: PUT /members/{id}
     */
    public function update($id)
    {
        $data = Request::getBody();

        $success = $this->memberModel->updateMember($id, $data);

        if ($success) {
            Response::success(null, "Member updated successfully");
        } else {
            Response::error("Failed to update member or no changes made", 400);
        }
    }

    /**
     * Remove a member from the system
     * Route: DELETE /members/{id}
     */
    public function destroy($id)
    {
        $success = $this->memberModel->deleteById($id);

        if ($success) {
            Response::success(null, "Member deleted successfully");
        } else {
            Response::error("Member not found or could not be deleted", 404);
        }
    }
}