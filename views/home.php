<?php
// views/home.php - Home page
$posts = getAllPosts();
?>

<div class="row">
    <div class="col-md-12">
        <h2>Beranda</h2>
        <p>Selamat datang di Blog Sederhana. Lihat post terbaru di bawah ini.</p>

        <div class="row">
            <?php if ($posts->num_rows > 0): ?>
            <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">
                            Oleh: <?php echo htmlspecialchars($post['name']); ?> |
                            <?php echo date('d M Y H:i', strtotime($post['date'])); ?>
                        </h6>
                        <p class="card-text">
                            <?php echo substr(htmlspecialchars($post['content']), 0, 150); ?>...
                        </p>
                        <a href="index.php?page=post&action=view&id=<?php echo $post['idpost']; ?>"
                            class="btn btn-primary btn-sm">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <div class="col-12">
                <p>Belum ada post.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>