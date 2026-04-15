<?php
require_once __DIR__ . '/config/helpers.php';

if (isLoggedIn()) {
    redirect('/gallery.php');
} else {
    redirect('/login.php');
}