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
            return Response::error("Book ID and Member ID are required", 400);
        }

        if (!is_numeric($data['book_id']) || !is_numeric($data['member_id'])) {
            return Response::error("Book ID and Member ID must be valid numbers", 400);
        }

        $bookId = (int)$data['book_id'];
        $memberId = (int)$data['member_id'];

        $member = $this->memberModel->getById($memberId);

        if (!$member || $member['membership_status'] !== 'active') {
            return Response::error("Only active members can borrow books", 403);
        }

        $book = $this->bookModel->getById($bookId);

        if (!$book) {
            return Response::error("Book not found", 404);
        }

        if ($this->loanModel->isAlreadyBorrowed($memberId, $bookId)) {
            return Response::error("This member already has an active loan for this book", 400);
        }

        $loanId = $this->loanModel->borrowBook($bookId, $memberId);

        if ($loanId) {
            return Response::success(['id' => $loanId], "Book borrowed successfully", 201);
        }

        return Response::error("Book is not available for loan", 400);
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