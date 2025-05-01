<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user']) && $_SESSION['user']['logged_in'] === true;
}

function getUserRole() {
    return isLoggedIn() ? $_SESSION['user']['role'] : null;
}

function getUserEmail() {
    return isLoggedIn() ? $_SESSION['user']['email'] : null;
}
?>