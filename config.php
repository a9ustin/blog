<?php
// config.php - Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'blog_db');

// Connect to database
function connectDB()
{
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Start session on every page
session_start();

// Functions for authentication
function login($username, $password)
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT username, password, name, role, gender FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Harusnya pakai password_verify() kalau hash
            $_SESSION['username'] = $user['username'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['gender'] = $user['gender'];
            return true;
        }
    }
    return false;
}

function logout()
{
    session_unset();
    session_destroy();
}

function isLoggedIn()
{
    return isset($_SESSION['username']);
}

function isAdmin()
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isAuthor()
{
    return isset($_SESSION['role']) && ($_SESSION['role'] === 'author' || $_SESSION['role'] === 'admin');
}

// CRUD functions for account
function getAllAccounts()
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT username, name, role, gender FROM account");
    $stmt->execute();
    return $stmt->get_result();
}

function getAccount($username)
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT username, password, name, role, gender FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createAccount($username, $password, $name, $role, $gender)
{
    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO account (username, password, name, role, gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $name, $role, $gender);
    return $stmt->execute();
}

function updateAccount($username, $password, $name, $role, $gender)
{
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE account SET password = ?, name = ?, role = ?, gender = ? WHERE username = ?");
    $stmt->bind_param("ssss", $password, $name, $role, $username, $gender);
    return $stmt->execute();
}

function deleteAccount($username)
{
    $conn = connectDB();
    $stmt = $conn->prepare("DELETE FROM account WHERE username = ?");
    $stmt->bind_param("s", $username);
    return $stmt->execute();
}

// CRUD functions for posts
function getAllPosts()
{
    $conn = connectDB();
    // Modified query to include username from account table
    $stmt = $conn->prepare("SELECT p.id, p.title, p.content, p.created_at, p.author_id, a.name, a.username 
                            FROM posts p 
                            JOIN account a ON p.author_id = a.id 
                            ORDER BY p.created_at DESC");

    if (!$stmt) {
        die("Query error: " . $conn->error);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        die("Execution error: " . $stmt->error);
    }

    return $result;
}


function getUserPosts($author_id)
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, title, content, created_at FROM posts WHERE author_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $author_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getPost($id)
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT p.id, p.title, p.content, p.created_at, p.author_id, a.name, a.username 
                            FROM posts p 
                            JOIN account a ON p.author_id = a.id 
                            WHERE p.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createPost($title, $content, $username)
{
    $conn = connectDB();

    // First get the author's ID from the username
    $userStmt = $conn->prepare("SELECT id FROM account WHERE username = ?");
    $userStmt->bind_param("s", $username);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows !== 1) {
        return false; // User not found
    }

    $user = $userResult->fetch_assoc();
    $author_id = $user['id'];

    // Now create the post with the correct author_id
    $stmt = $conn->prepare("INSERT INTO posts (title, content, author_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $content, $author_id);

    if (!$stmt->execute()) {
        die("Error: " . $stmt->error);
    }

    return true;
}

function updatePost($id, $title, $content)
{
    $conn = connectDB();
    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    return $stmt->execute();
}

function deletePost($id)
{
    $conn = connectDB();
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Helper functions
function redirect($url)
{
    header("Location: $url");
    exit();
}

function showError($message)
{
    return "<div class='alert alert-danger'>$message</div>";
}

function showSuccess($message)
{
    return "<div class='alert alert-success'>$message</div>";
}