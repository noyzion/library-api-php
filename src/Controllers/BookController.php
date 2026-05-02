<?php

class BookController
{
    private Book $bookModel;

    public function __construct()
    {
        $this->bookModel = new Book();
    }

    /**
     * Get all books (with optional genre filter)
     * Route: GET /books
     */
    public function index()
    {
        $params = Request::getQueryParams();
        $genre = $params['genre'] ?? null;

        $books = $this->bookModel->getAll($genre);
        Response::success($books, "Books retrieved successfully");
    }

    /**
     * Get a single book by ID
     * Route: GET /books/{id}
    */
    public function show($id)
    {
        if (!is_numeric($id)) {
            Response::error("Invalid book ID", 400);
        }
        $book = $this->bookModel->getById($id);
        if(!$book) {
            Response::error("Book not found", 404);
        }
   
        Response::success($book, "Book found");
    }

    /**
     * Create a new book
     * Route: POST /books
     */
    public function store() {
        $data = Request::getBody();
        $requiredFields = ['title', 'author', 'isbn', 'genre'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                Response::error("$field is required", 400);
            }
        }
        $allowedGenres = ['fiction', 'non-fiction', 'science', 'history', 'other'];

        if (!in_array($data['genre'], $allowedGenres)) {
            Response::error("Invalid genre", 400);
        }
        if (isset($data['published_year']) && !is_numeric($data['published_year'])) {
            Response::error("Published year must be a number", 400);
        }
        if (isset($data['total_copies']) && (!is_numeric($data['total_copies']) || $data['total_copies'] < 1)) {
            Response::error("Total copies must be a positive number", 400);
        }

        try {
            $newBookId = $this->bookModel->createBook($data);
            Response::success(['id' => $newBookId], "Book created successfully", 201);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                Response::error("ISBN already exists", 409);
            }

            Response::error("Database error", 500);
}
    }

    /**
     * Update an existing book
     * Route: PUT /books/{id}
     */
    public function update($id) {
        if (!is_numeric($id)) {
            Response::error("Invalid book ID", 400);
        }

        $existingBook = $this->bookModel->getById($id);

        if (!$existingBook) {
            Response::error("Book not found", 404);
        }

        $data = Request::getBody();

        $requiredFields = ['title', 'author', 'isbn', 'genre', 'total_copies', 'available_copies'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                Response::error("$field is required", 400);
            }
        }

        $allowedGenres = ['fiction', 'non-fiction', 'science', 'history', 'other'];

        if (!in_array($data['genre'], $allowedGenres)) {
            Response::error("Invalid genre", 400);
        }

        if (!is_numeric($data['total_copies']) || $data['total_copies'] < 1) {
            Response::error("Total copies must be a positive number", 400);
        }

        if (!is_numeric($data['available_copies']) || $data['available_copies'] < 0) {
            Response::error("Available copies must be zero or a positive number", 400);
        }

        if ($data['available_copies'] > $data['total_copies']) {
            Response::error("Available copies cannot be greater than total copies", 400);
        }

        try {
            $this->bookModel->updateBook($id, $data);
            Response::success(null, "Book updated successfully");
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                Response::error("ISBN already exists", 409);
            }

            Response::error("Database error", 500);
}
    }

    /**
     * Delete a book
     * Route: DELETE /books/{id}
     */
    public function destroy($id) {
        if (!is_numeric($id)) {
            Response::error("Invalid book ID", 400);
        }
        $success = $this->bookModel->deleteById($id);

        if ($success) {
            Response::success(null, "Book deleted successfully");
        } else {
            Response::error("Book not found or could not be deleted", 404);
        }
    }


}