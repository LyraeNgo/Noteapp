# NoteApp

A simple and elegant note-taking web application built with PHP, MySQL, and Bootstrap.

## Features

- User authentication (login/register)
- Create, read, update, and delete notes
- Dark/Light theme support
- Customizable font size
- Grid/List view options
- Responsive design
- Search functionality

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP (recommended for local development)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/NoteApp.git
```

2. Set up XAMPP:
   - Start Apache and MySQL services
   - Place the project in the `htdocs` directory

3. Import the database:
   - Open phpMyAdmin
   - Create a new database named `noteapp`
   - Import the SQL file from `admin/database.sql`

4. Configure the database connection:
   - Open `admin/db-con.php`
   - Update the database credentials if needed

5. Access the application:
   - Open your browser
   - Navigate to `http://localhost/NoteApp`

## Usage

1. Register a new account or login with existing credentials
2. Create new notes using the "Create Note" button
3. View your notes in either grid or list view
4. Use the search bar to find specific notes
5. Customize your experience with theme and font size options

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details. 