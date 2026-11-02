# Employee Management System – PHP CRUD

A simple Employee Management System built using PHP, PDO, MySQL, and Bootstrap. This project demonstrates all four CRUD operations: Create, Read, Update, and Delete.

## Features

- Add new employee records.
- View all employees in a dashboard.
- Update existing employee details.
- Delete employee records with a confirmation prompt.
- Validate form inputs.
- Prevent duplicate email addresses.
- Use PDO prepared statements for database operations.
- Use CSRF tokens to protect delete requests.
- Responsive interface using Bootstrap.

## Technologies Used

- **Backend:** PHP
- **Database:** MySQL
- **Database Connectivity:** PDO
- **Frontend:** HTML, CSS, Bootstrap
- **Development Environment:** XAMPP
- **Version Control:** Git and GitHub

## Project Structure

```text
php-crud/
│
├── config/
│   └── database.php
│
├── models/
│   └── Employee.php
│
├── assets/
│   └── css/
│
├── index.php
├── create.php
├── edit.php
├── delete.php
├── .gitignore
└── README.md
```

## Requirements

- PHP 8.0 or later
- MySQL
- XAMPP
- Web browser

## Installation and Setup

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/php-crud.git
```

### 2. Move the project into XAMPP

Place the project folder inside:

```text
C:\xampp\htdocs\
```

### 3. Start XAMPP

Start Apache and MySQL from the XAMPP Control Panel.

### 4. Create the database

Open phpMyAdmin:

```text
http://localhost/phpmyadmin/
```

Create a database named:

```sql
php_crud
```

Select the database and execute:

```sql
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    department VARCHAR(100) NOT NULL,
    salary DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Configure the database connection

Open:

```text
config/database.php
```

Set the database host, port, name, username, and password to match your local MySQL configuration.

Example local XAMPP settings:

- Host: `127.0.0.1`
- Port: `3307` (change if your MySQL uses a different port)
- Database: `php_crud`
- Username: `root`
- Password: your local MySQL password

Do not use development credentials in a public production deployment.

### 6. Run the project

Open your browser and visit:

```text
http://localhost/php-crud/
```

## CRUD Operations

| Operation | Description |
|---|---|
| Create | Add a new employee |
| Read | Display employee records |
| Update | Modify employee information |
| Delete | Remove an employee after confirmation |

## Application Screenshots

### 1. Employee Dashboard
![Employee Dashboard](screenshots/dashboard.png)

### 2. Add Employee
![Add Employee](screenshots/add-employee.png)

### 3. Edit Employee
![Edit Employee](screenshots/edit-employee.png)

### 4. Delete Confirmation
![Delete Confirmation](screenshots/delete-confirmation.png)



## Learning Outcomes

- Understanding PHP fundamentals.
- Connecting PHP applications to MySQL using PDO.
- Writing SQL queries for CRUD operations.
- Using prepared statements.
- Implementing form validation.
- Organizing code using a simple model-based structure.
- Using Bootstrap to build responsive interfaces.
- Using Git and GitHub for version control.


## Author

**Varsha Bombe**

GitHub: https://github.com/varshabombe10