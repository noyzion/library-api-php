# Library Management System - PHP REST API

Pure PHP + MySQL REST API for managing a library system.  
The API supports books, members, and book loans, including the main business logic required for borrowing and returning books.

---

## Tech Stack

- PHP (Vanilla PHP, no framework)
- MySQL
- PDO
- Apache / XAMPP / WAMP
- Custom Router
- MVC-like structure

---

## Project Structure

```text
library-api-php/
├── config/
│   └── database.php
├── src/
│   ├── Controllers/
│   │   ├── BookController.php
│   │   ├── MemberController.php
│   │   └── LoanController.php
│   ├── Models/
│   │   ├── Book.php
│   │   ├── Member.php
│   │   └── Loan.php
│   └── Core/
│       ├── Router.php
│       ├── Request.php
│       └── Response.php
├── sql/
│   └── schema.sql
├── index.php
├── .htaccess
└── README.md
```

---

## Database Setup

1. Create a MySQL database:

```sql
CREATE DATABASE library_db;
```

2. Import the schema file:

```text
sql/schema.sql
```

3. Update the database credentials in:

```text
config/database.php
```

Default local configuration:

```php
private const HOST = "localhost";
private const DB_NAME = "library_db";
private const USERNAME = "root";
private const PASSWORD = "";
```

For production, database credentials should be stored in environment variables and not committed to Git.

---

## Running the Project

1. Place the project inside your Apache server directory, for example:

```text
htdocs/library-api-php
```

2. Make sure Apache and MySQL are running.

3. Send requests to:

```text
http://localhost/library-api-php
```

Example:

```text
GET http://localhost/library-api-php/books
```

The `.htaccess` file routes all requests to `index.php`.

---

## Response Format

### Success Response

```json
{
  "success": true,
  "data": {},
  "message": "Operation completed successfully"
}
```

### Error Response

```json
{
  "success": false,
  "error": "Error message",
  "code": 400
}
```

---

## API Endpoints

## Books

### Get all books

```http
GET /books
```

Optional genre filter:

```http
GET /books?genre=fiction
```

Allowed genres:

```text
fiction, non-fiction, science, history, other
```

### Get book by ID

```http
GET /books/{id}
```

### Create book

```http
POST /books
```

Example body:

```json
{
  "title": "The Hobbit",
  "author": "J.R.R. Tolkien",
  "isbn": "9780547928227",
  "published_year": 1937,
  "genre": "fiction",
  "total_copies": 3
}
```

### Update book

```http
PUT /books/{id}
```

Example body:

```json
{
  "title": "The Hobbit - Updated",
  "author": "J.R.R. Tolkien",
  "isbn": "9780547928227",
  "published_year": 1937,
  "genre": "fiction",
  "total_copies": 5,
  "available_copies": 4
}
```

### Delete book

```http
DELETE /books/{id}
```

If the book has related loans, the API returns `409 Conflict`.

---

## Members

### Get all members

```http
GET /members
```

### Get member by ID

```http
GET /members/{id}
```

### Create member

```http
POST /members
```

Example body:

```json
{
  "full_name": "Noy Zion",
  "email": "noy@example.com",
  "phone": "0501234567",
  "membership_status": "active"
}
```

Allowed membership statuses:

```text
active, suspended, expired
```

If `membership_status` is not provided, the default value is `active`.

### Update member

```http
PUT /members/{id}
```

Example body:

```json
{
  "full_name": "Noy Zion",
  "email": "noy.updated@example.com",
  "phone": "0507654321",
  "membership_status": "active"
}
```

### Delete member

```http
DELETE /members/{id}
```

If the member has related loans, the API returns `409 Conflict`.

---

## Loans

### Get all loans

```http
GET /loans
```

### Borrow a book

```http
POST /loans
```

Example body:

```json
{
  "book_id": 1,
  "member_id": 1
}
```

Business rules:

- The member must exist.
- The member must have `membership_status = active`.
- The book must exist.
- The book must have at least one available copy.
- The same member cannot borrow the same book twice at the same time.
- On successful borrow, `available_copies` is decreased by 1.
- `due_date` is calculated automatically as `loan_date + 14 days`.

### Return a book

```http
PUT /loans/{id}/return
```

Business rules:

- The loan must exist.
- The loan must still be active.
- On successful return, `return_date` is set to the current date.
- The loan status changes to `returned`.
- The related book's `available_copies` is increased by 1.

### Get overdue loans

```http
GET /overdue/loans
```

Returns active loans where:

```text
return_date IS NULL
and due_date < current date
```

---

## Validation and Error Handling

The API validates:

- Required fields
- Numeric IDs
- Valid email format
- Valid book genre
- Valid membership status
- Positive `total_copies`
- Non-negative `available_copies`
- `available_copies <= total_copies`
- Duplicate ISBN conflicts
- Duplicate email conflicts
- Delete conflicts caused by related loan records

Common HTTP status codes:

| Code | Meaning |
|---|---|
| 200 | Successful request |
| 201 | Resource created |
| 400 | Invalid request data |
| 403 | Member is not allowed to borrow |
| 404 | Resource not found |
| 409 | Duplicate data or related records conflict |
| 500 | Server/database error |

---

## Business Logic Implemented

- Only active members can borrow books.
- A book cannot be borrowed when `available_copies = 0`.
- A member cannot borrow the same book twice while the first loan is still active.
- Borrowing a book automatically decreases `available_copies`.
- Returning a book automatically increases `available_copies`.
- `due_date` is calculated automatically as 14 days after `loan_date`.
- Borrow and return operations use SQL transactions to keep loan and inventory data consistent.

---

## Example Flow for Testing

1. Create a book:

```http
POST /books
```

2. Create an active member:

```http
POST /members
```

3. Borrow the book:

```http
POST /loans
```

4. Check that `available_copies` decreased:

```http
GET /books/{id}
```

5. Return the book:

```http
PUT /loans/{id}/return
```

6. Check that `available_copies` increased:

```http
GET /books/{id}
```

---

## Notes

- This project is an API only. There is no frontend UI.
- Requests and responses are JSON based.
- The project uses PDO prepared statements for database queries.
- Local database credentials are configured in `config/database.php`.
- Do not commit real passwords or production secrets to Git.
