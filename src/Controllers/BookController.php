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
        if (!isset($data['title']) || !isset($data['author'])) {
            Response::error("Title and Author are required", 400);
        }

        $newBookId = $this->bookModel->createBook($data);
        Response::success(['id' => $newBookId], "Book created successfully", 201);
    }

    /**
     * Update an existing book
     * Route: PUT /books/{id}
     */
    public function update($id) {
        $data = Request::getBody();
        $success = $this->bookModel->updateBook($id, $data);

        if ($success) {
            Response::success(null, "Book updated successfully");
        } else {
            Response::error("Failed to update book or no changes made", 400);
        }
    }

    /**
     * Delete a book
     * Route: DELETE /books/{id}
     */
    public function destroy($id) {
        $success = $this->bookModel->deleteById($id);

        if ($success) {
            Response::success(null, "Book deleted successfully");
        } else {
            Response::error("Book not found or could not be deleted", 404);
        }
    }


}