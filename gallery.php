<?php
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

/* Fetch all images ordered by upload date */
$pdo  = getDB();
$stmt = $pdo->prepare(
    'SELECT i.id, i.title, i.file_path, i.uploaded_at, u.username
     FROM images i
     JOIN users u ON u.id = i.user_id
     ORDER BY i.uploaded_at DESC'
);
$stmt->execute();
$images = $stmt->fetchAll();

$flashSuccess = getFlash('success');
$flashError   = getFlash('error');

$pageTitle = 'Gallery';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

    <div class="page-actions">
        <h1>Image Gallery</h1>
        <a href="/upload.php" class="btn btn-primary">+ Upload Image</a>
    </div>

    <?php if ($flashSuccess): ?>
        <div class="alert alert-success"><?= h($flashSuccess) ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="alert alert-error"><?= h($flashError) ?></div>
    <?php endif; ?>

    <?php if (empty($images)): ?>
        <div class="card" style="text-align:center;padding:3rem">
            <p style="color:#777;font-size:1rem">No images yet. <a href="/upload.php" style="color:#e94560">Upload one!</a></p>
        </div>
    <?php else: ?>
        <div class="gallery-grid">
            <?php foreach ($images as $img): ?>
                <div class="gallery-item">
                    <img src="<?= h($img['file_path']) ?>"
                         alt="<?= h($img['title']) ?>">
                    <div class="gallery-item-info">
                        <span class="title" title="<?= h($img['title']) ?>"><?= h($img['title']) ?></span>
                        <form method="POST" action="/delete.php"
                              onsubmit="return confirm('Delete this image?')">
                            <input type="hidden" name="id" value="<?= (int)$img['id'] ?>">
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>