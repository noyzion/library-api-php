<?php

class LoanController {
    private $loanModel;
    private $bookModel;
    private $memberModel;

    public function __construct() {
        $this->loanModel = new Loan();
        $this->bookModel = new Book();
        $this->memberModel = new Member();
    }

    /**
     * Get all loans
     * Route: GET /loans
     */
    public function index() {
        $loans = $this->loanModel->getAll();
        Response::success($loans, "Loans retrieved successfully");
    }

    /**
     * Create a new loan (Borrow a book)
     * Route: POST /loans
     */
    public function store()
    {
        $data = Request::getBody();
        if (!isset($data['book_id']) || !isset($data['member_id'])) {
            Response::error("Book ID and Member ID are required", 400);
        }
        // 2. Business Logic: Check if member is active
        $member = $this->memberModel->getById($data['member_id']);
        if (!$member || $member['membership_status'] !== 'active') {
            Response::error("Only active members can borrow books", 403);
        }
        // 3. Business Logic: Check book availability
        $book = $this->bookModel->getById($data['book_id']);
        if (!$book || $book['available_copies'] <= 0) {
            Response::error("Book is not available for loan", 400);
        }

        // 4. Business Logic: Check if already borrowed by same member[cite: 1]
        if ($this->loanModel->isAlreadyBorrowed($data['member_id'], $data['book_id'])) {
            Response::error("This member already has an active loan for this book", 400);
        }
        // 5. Create Loan (The model will handle due_date and copies update)[cite: 1]
        $loanId = $this->loanModel->borrowBook($data['book_id'], $data['member_id']);

        if ($loanId) {
            Response::success(['id' => $loanId], "Book borrowed successfully", 201);
        } else {
            Response::error("Failed to process loan", 500);
        }
    }

    /**
     * Return a book
     * Route: PUT /loans/{id}/return
     */
    public function returnBook($id)
    {
        $success = $this->loanModel->returnBook($id);
        if ($success) {
            Response::success(null, "Book returned successfully");
        } else {
            Response::error("Loan record not found or already returned", 404);
        }
    }

    /**
     * Get overdue loans
     * Route: GET /loans/overdue
     */
    public function overdue() {
        $overdueLoans = $this->loanModel->getOverdueLoans();
        Response::success($overdueLoans, "Overdue loans retrieved");
    }

}