<?php
session_start();

/* Redirect to a URL and exit */
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

/* Returns true if an admin session is active */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/* Require the user to be logged in; redirect to login if not */
function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

/* Sanitise a string for safe HTML output */
function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/* Flash message helpers — store one message per key per request cycle */
function setFlash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): string {
    $msg = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $msg;
}