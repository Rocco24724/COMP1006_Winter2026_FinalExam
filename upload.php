<?php
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

$errors  = [];
$oldTitle = '';
$flashSuccess = getFlash('success');

/* Allowed MIME types and extensions */
/* Max size 5 MBs */
const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
const ALLOWED_EXT  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
const MAX_SIZE     = 5 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $oldTitle = $title;

    /* Title validation */
    if ($title === '') {
        $errors[] = 'Image title is required.';
    } elseif (strlen($title) > 150) {
        $errors[] = 'Title must be 150 characters or fewer.';
    }

    /* File validation */
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Please choose an image file to upload.';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error (code ' . $_FILES['image']['error'] . '). Please try again.';
    } else {
        $file     = $_FILES['image'];
        $tmpPath  = $file['tmp_name'];
        $origName = $file['name'];
        $fileSize = $file['size'];

        /* Size check */
        if ($fileSize > MAX_SIZE) {
            $errors[] = 'File is too large. Maximum allowed size is 5 MB.';
        }

        /* Extension check */
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXT, true)) {
            $errors[] = 'Only JPG, PNG, GIF, and WebP files are allowed.';
        }

        /* MIME check */
        $imageInfo = @getimagesize($tmpPath);
        if ($imageInfo === false) {
            $errors[] = 'Uploaded file is not a valid image.';
        } elseif (!in_array($imageInfo['mime'], ALLOWED_MIME, true)) {
            $errors[] = 'Image type not permitted. Allowed: JPG, PNG, GIF, WebP.';
        }
    }

    /* Move and save */
    if (empty($errors)) {
        $safeName = bin2hex(random_bytes(12)) . '.' . $ext;   // unguessable filename
        $destDir  = __DIR__ . '/uploads/';
        $destPath = $destDir . $safeName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            $errors[] = 'Failed to save the image. Please try again.';
        } else {
            $pdo  = getDB();
            $stmt = $pdo->prepare(
                'INSERT INTO images (user_id, title, file_path) VALUES (?, ?, ?)'
            );
            $stmt->execute([$_SESSION['user_id'], $title, 'uploads/' . $safeName]);

            setFlash('success', 'Image "' . $title . '" uploaded successfully.');
            redirect('/gallery.php');
        }
    }
}

$pageTitle = 'Upload Image';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width:540px">
    <div class="card">
        <h1>Upload Image</h1>

        <?php if ($flashSuccess): ?>
            <div class="alert alert-success"><?= h($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?>
                    <div><?= h($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/upload.php" enctype="multipart/form-data" novalidate>
            <div class="form-group">
                <label for="title">Image Title</label>
                <input type="text" id="title" name="title"
                       value="<?= h($oldTitle) ?>" placeholder="e.g. Sunset at the lake" required>
            </div>

            <div class="form-group">
                <label for="image">Choose Image <small>(JPG, PNG, GIF, WebP · max 5 MB)</small></label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>

            <div style="display:flex;gap:1rem;align-items:center;margin-top:.5rem">
                <button type="submit" class="btn btn-primary">Upload</button>
                <a href="/gallery.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>