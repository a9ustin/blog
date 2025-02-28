<?php
// index.php - Main entry point
require_once 'config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// HTML header and navigation
include 'header.php';

// Main content
switch ($page) {
    case 'home':
        include 'views/home.php';
        break;
    case 'post':
        include 'views/post.php';
        break;
    case 'account':
        include 'views/account.php';
        break;
    case 'login':
        include 'views/login.php';
        break;
    case 'logout':
        logout();
        redirect('index.php');
        break;
    default:
        include 'views/home.php';
}

// HTML footer
include 'footer.php';
?>