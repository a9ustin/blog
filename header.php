<?php
// header.php - Header template
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Sederhana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .blog-header {
        padding: 2rem 0;
        background-color: #f8f9fa;
        margin-bottom: 2rem;
    }

    .post-content {
        white-space: pre-line;
    }
    </style>
</head>

<body>
    <div class="container">
        <header class="blog-header py-3">
            <div class="row flex-nowrap justify-content-between align-items-center">
                <div class="col-4 pt-1">
                    <a class="link-secondary" href="index.php">Blog Sederhana</a>
                </div>
                <div class="col-4 d-flex justify-content-end align-items-center">
                    <?php if (isLoggedIn()): ?>
                    <span class="me-2">Halo, <?php echo $_SESSION['name']; ?> (<?php echo $_SESSION['role']; ?>)</span>
                    <a class="btn btn-sm btn-outline-secondary me-2" href="index.php?page=logout">Logout</a>
                    <?php else: ?>
                    <a class="btn btn-sm btn-outline-secondary" href="index.php?page=login">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </header>

        <div class="nav-scroller py-1 mb-4">
            <nav class="nav d-flex justify-content-between">
                <a class="p-2 link-secondary" href="index.php?page=home">Beranda</a>
                <a class="p-2 link-secondary" href="index.php?page=post">Post</a>
                <?php if (isAdmin()): ?>
                <a class="p-2 link-secondary" href="index.php?page=account">Akun</a>
                <?php endif; ?>
            </nav>
        </div>

        <?