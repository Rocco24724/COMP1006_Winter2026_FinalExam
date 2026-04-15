<?php
require_once __DIR__ . '/config/helpers.php';
require_once __DIR__ . '/config/db.php';

requireLogin();

/* Only accept POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/gallery.php');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    setFlash('error', 'Invalid image ID.');
    redirect('/gallery.php');
}

$pdo = getDB();

/* Fetch the record (verify it exists and belongs to this admin) */
$stmt = $pdo->prepare('SELECT id, file_path FROM images WHERE id = ?');
$stmt->execute([$id]);
$image = $stmt->fetch();

if (!$image) {
    setFlash('error', 'Image not found.');
    redirect('/gallery.php');
}

/* Delete the physical file */
$fullPath = __DIR__ . '/' . $image['file_path'];
if (file_exists($fullPath)) {
    unlink($fullPath);
}

/* Delete the database record */
$stmt = $pdo->prepare('DELETE FROM images WHERE id = ?');
$stmt->execute([$id]);

setFlash('success', 'Image deleted successfully.');
redirect('/gallery.php');