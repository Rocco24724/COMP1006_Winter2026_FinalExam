<?php
$pageTitle = $pageTitle ?? 'Image Gallery';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?> — Admin Gallery</title>
</head>
<body>

<nav>
    <a href="<?= isLoggedIn() ? '/gallery.php' : '/login.php' ?>">Gallery Admin</a>
    &nbsp;|&nbsp;
    <?php if (isLoggedIn()): ?>
        <a href="/gallery.php">Gallery</a> |
        <a href="/upload.php">Upload</a> |
        <a href="/logout.php">Log out</a>
    <?php else: ?>
        <a href="/login.php">Login</a> |
        <a href="/register.php">Register</a>
    <?php endif; ?>
</nav>

<hr>