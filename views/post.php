<?php
// views/post.php - Posts CRUD
require_once 'config.php';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? $_GET['id'] : null;
$message = '';

// Require authentication for all actions except view
if ($action !== 'view' && $action !== 'list' && !isAuthor()) {
    redirect('index.php?page=login');
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'create' && isAuthor()) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $username = $_SESSION['username'];

        if (createPost($title, $content, $username)) {
            $message = showSuccess('Post berhasil dibuat!');
            $action = 'list';
        } else {
            $message = showError('Gagal membuat post!');
        }
    } else if ($action === 'edit' && isAuthor()) {
        $title = $_POST['title'];
        $content = $_POST['content'];

        $post = getPost($id);
        if (!$post || ($post['username'] !== $_SESSION['username'] && !isAdmin())) {
            $message = showError('Anda tidak memiliki akses untuk mengedit post ini!');
        } else if (updatePost($id, $title, $content)) {
            $message = showSuccess('Post berhasil diupdate!');
            $action = 'list';
        } else {
            $message = showError('Gagal mengupdate post!');
        }
    }
}

// Handle delete action
if ($action === 'delete' && isAuthor() && $id) {
    $post = getPost($id);
    if (!$post || ($post['username'] !== $_SESSION['username'] && !isAdmin())) {
        $message = showError('Anda tidak memiliki akses untuk menghapus post ini!');
    } else if (deletePost($id)) {
        $message = showSuccess('Post berhasil dihapus!');
    } else {
        $message = showError('Gagal menghapus post!');
    }
    $action = 'list';
}
?>

<div class="row">
    <div class="col-md-12">
        <h2>Post</h2>
        <?php echo $message; ?>

        <?php if (isAuthor() && $action === 'list'): ?>
        <div class="mb-3">
            <a href="index.php?page=post&action=create" class="btn btn-primary">Buat Post Baru</a>
        </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
        <?php
            $posts = getAllPosts();
            ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($posts && $posts->num_rows > 0): ?>
                <?php while ($post = $posts->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $post['title']; ?></td>
                    <td><?php echo $post['name']; ?></td>
                    <td><?php echo date('d M Y H:i', strtotime($post['created_at'])); ?></td>
                    <td>
                        <a href="index.php?page=post&action=view&id=<?php echo $post['id']; ?>"
                            class="btn btn-sm btn-info">Lihat</a>
                        <?php if (isAuthor() && ($post['username'] === $_SESSION['username'] || isAdmin())): ?>
                        <a href="index.php?page=post&action=edit&id=<?php echo $post['id']; ?>"
                            class="btn btn-sm btn-warning">Edit</a>
                        <a href="index.php?page=post&action=delete&id=<?php echo $post['id']; ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus post ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="4">Tidak ada post.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php elseif ($action === 'view' && $id): ?>
        <?php
            $post = getPost($id);
            if (!$post): ?>
        <div class="alert alert-danger">Post tidak ditemukan!</div>
        <?php else: ?>
        <div class="card mb-4">
            <div class="card-header">
                <h3><?php echo $post['title']; ?></h3>
                <p class="text-muted">
                    Oleh: <?php echo $post['name']; ?> |
                    <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                </p>
            </div>
            <div class="card-body">
                <div class="post-content">
                    <?php echo nl2br($post['content']); ?>
                </div>
            </div>
            <div class="card-footer">
                <?php if (isAuthor() && ($post['username'] === $_SESSION['username'] || isAdmin())): ?>
                <a href="index.php?page=post&action=edit&id=<?php echo $post['id']; ?>" class="btn btn-warning">Edit</a>
                <a href="index.php?page=post&action=delete&id=<?php echo $post['id']; ?>" class="btn btn-danger"
                    onclick="return confirm('Apakah Anda yakin ingin menghapus post ini?')">Hapus</a>
                <?php endif; ?>
                <a href="index.php?page=post" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
        <?php endif; ?>
        <?php elseif ($action === 'create' && isAuthor()): ?>
        <div class="card">
            <div class="card-header">
                <h3>Buat Post Baru</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Konten</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="index.php?page=post" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
        <?php elseif ($action === 'edit' && isAuthor() && $id): ?>
        <?php
            $post = getPost($id);
            if (!$post || ($post['username'] !== $_SESSION['username'] && !isAdmin())): ?>
        <div class="alert alert-danger">Anda tidak memiliki akses untuk mengedit post ini!</div>
        <?php else: ?>
        <div class="card">
            <div class="card-header">
                <h3>Edit Post</h3>
            </div>
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo $post['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Konten</label>
                        <textarea class="form-control" id="content" name="content" rows="10"
                            required><?php echo $post['content']; ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="index.php?page=post" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>