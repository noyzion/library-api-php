# Library Management System - PHP API

A clean and structured REST API for managing a library system, built with pure PHP and MySQL. 
This project implements core library business logic, including book management, member tracking, and loan processing.

## 🚀 Features
- **Books Management:** CRUD operations with genre filtering.
- **Member Management:** Full CRUD for library members.
- **Loan System:**
    - Borrow books with automatic `due_date` calculation (14 days).
    - Automatic inventory update (`available_copies`) on borrow/return.
    - Validation for member status and book availability.
    - Overdue loans tracking.

## 🛠 Tech Stack
- **Language:** PHP (Pure/Vanilla).
- **Database:** MySQL via PDO.
- **Architecture:** MVC-like structure with Singleton Pattern and custom Router.

## 📋 Installation

1. **Database Setup:**
   - Create a database named `library_db`.
   - Import the `sql/schema.sql` file into your MySQL server.

2. **Configuration:**
   - Update `config/database.php` with your local database credentials (host, username, password).
   - *Note: In production, these should be handled via environment variables.*

3. **Web Server Configuration:**
   - Ensure you are using a server like Apache (XAMPP/WAMP).
   - The project requires an `.htaccess` file in the `/public` folder to route all requests to `index.php`.

## 🛣 API Endpoints

### Books
- `GET /books` - List all books (Filter by `?genre=fiction`).
- `GET /books/{id}` - Get single book.
- `POST /books` - Add new book.
- `PUT /books/{id}` - Update book.
- `DELETE /books/{id}` - Remove book.

### Members
- `GET /members` - List all members.
- `POST /members` - Register new member.
- `PUT /members/{id}` - Update member details.

### Loans
- `GET /loans` - View all loan history.
- `POST /loans` - Borrow a book (Required: `book_id`, `member_id`).
- `PUT /loans/{id}/return` - Return a borrowed book.
- `GET /loans/overdue` - List all overdue loans.

## 🧪 Business Logic Implemented
- **Active Members Only:** Only members with `status = 'active'` can borrow.
- **Availability Check:** Cannot borrow a book if `available_copies` is 0.
- **No Duplicates:** A member cannot borrow the same book twice simultaneously.
- **Data Integrity:** Used SQL Transactions for borrow/return operations to ensure stock and loan records stay in sync.