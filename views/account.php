<?php
// views/account.php - Account CRUD
// Only accessible by admin
if (!isAdmin()) {
    redirect('index.php');
}

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$username = isset($_GET['username']) ? $_GET['username'] : null;
$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create') {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        $gender = $_POST['gender'];

        if (createAccount($username, $password, $name, $role, $gender)) {
            $message = showSuccess('Akun berhasil dibuat!');
            $action = 'list';
        } else {
            $message = showError('Gagal membuat akun! Username mungkin sudah digunakan.');
        }
    } else if ($action === 'edit') {
        $password = $_POST['password'];
        $name = $_POST['name'];
        $role = $_POST['role'];
        $gender = $_POST['gender'];

        if (updateAccount($username, $password, $name, $role, $gender)) {
            $message = showSuccess('Akun berhasil diupdate!');
            $action = 'list';
        } else {
            $message = showError('Gagal mengupdate akun!');
        }
    }
}

// Handle delete action
if ($action === 'delete' && $username) {
    // Can't delete own account
    if ($username === $_SESSION['username']) {
        $message = showError('Anda tidak dapat menghapus akun Anda sendiri!');
    } else if (deleteAccount($username)) {
        $message = showSuccess('Akun berhasil dihapus!');
    } else {
        $message = showError('Gagal menghapus akun! Pastikan tidak ada post yang terkait dengan akun ini.');
    }
    $action = 'list';
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Akun</h2>
        <?php echo $message; ?>

        <?php if ($action === 'list'): ?>
        <div class="mb-3">
            <a href="index.php?page=account&action=create" class="btn btn-primary">Buat Akun Baru</a>
        </div>

        <?php
            $accounts = getAllAccounts();
            ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama</th>
                    <th>Jenis Kelamin</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($accounts->num_rows > 0): ?>
                <?php while ($account = $accounts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($account['username']); ?></td>
                    <td><?php echo htmlspecialchars($account['name']); ?></td>
                    <td><?php echo htmlspecialchars($account['gender']); ?></td>
                    <td><?php echo htmlspecialchars($account['role']); ?></td>
                    <td>
                        <a href="index.php?page=account&action=edit&username=<?php echo $account['username']; ?>"
                            class="btn btn-sm btn-warning">Edit</a>
                        <?php if ($account['username'] !== $_SESSION['username']): ?>
                        <a href="index.php?page=account&action=delete&username=<?php echo $account['username']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada akun.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php elseif ($action === 'create'): ?>
        <div class="card">
            <div class="card-header">
                <h3>Buat Akun Baru</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="Laki-laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Admin</option>
                            <option value="author">Author</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=account" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
        <?php elseif ($action === 'edit' && $username): ?>
        <?php
            $account = getAccount($username);
            if (!$account): ?>
        <div class="alert alert-danger">Akun tidak ditemukan!</div>
        <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h3>Edit Akun</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username"
                            value="<?php echo htmlspecialchars($account['username']); ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            value="<?php echo htmlspecialchars($account['password']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="name" name="name"
                            value="<?php echo htmlspecialchars($account['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="Laki-laki"
                                <?php echo $account['gender'] === 'Laki-laki' ? 'selected' : ''; ?>>
                                Laki-Laki
                            </option>
                            <option value="Perempuan"
                                <?php echo $account['gender'] === 'Perempuan' ? 'selected' : ''; ?>>
                                Perempuan
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin" <?php echo $account['role'] === 'admin' ? 'selected' : ''; ?>>Admin
                            </option>
                            <option value="author" <?php echo $account['role'] === 'author' ? 'selected' : ''; ?>>Author
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php?page=account" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>