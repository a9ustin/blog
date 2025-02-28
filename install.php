<?php
// Include database configuration
require_once 'config.php';

// Create connection
$conn = connectDB();

// SQL to create tables
$sql = "CREATE TABLE IF NOT EXISTS account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'author') NOT NULL DEFAULT 'author'
);

CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    author_id INT NOT NULL,
    FOREIGN KEY (author_id) REFERENCES account(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    author_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES account(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES account(id) ON DELETE CASCADE
);";

if ($conn->multi_query($sql)) {
    echo "Database tables created successfully.<br>";
} else {
    echo "Error creating tables: " . $conn->error;
}

// Insert default admin and author accounts
$users = [
    ['admin', password_hash('admin', PASSWORD_DEFAULT), 'Administrator', 'admin'],
    ['author', password_hash('author', PASSWORD_DEFAULT), 'Post Author', 'author']
];

foreach ($users as $user) {
    list($username, $password, $name, $role) = $user;
    $check_user = $conn->query("SELECT * FROM account WHERE username = '$username'");
    if ($check_user->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO account (username, password, name, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $name, $role);
        if ($stmt->execute()) {
            echo "$role account ($username) created successfully.<br>";
        } else {
            echo "Error creating $role account: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "$role account ($username) already exists.<br>";
    }
}

$conn->close();