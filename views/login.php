<?php
// views/login.php - Login page
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (login($username, $password)) {
        redirect('index.php');
    } else {
        $error = showError('Username atau password salah!');
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3>Login</h3>
            </div>
            <div class="card-body">
                <?php echo $error; ?>

                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>

                <div class="mt-3">
                    <h5>User dummy untuk login:</h5>
                    <ul>
                        <li>Admin: username <strong>admin</strong>, password <strong>admin</strong></li>
                        <li>Author: username <strong>author</strong>, password <strong>author</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>