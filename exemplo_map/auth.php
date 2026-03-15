<?php
/**
 * @file auth.php
 * @brief Utility script to handle authentication and session management.
 * @author Marco
 * @date 2026-03-15
 */

session_start();

/**
 * @brief Checks if the user is currently logged in.
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * @brief Gets the logged in user's ID.
 * @return int|null User ID or null if not logged in.
 */
function get_logged_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * @brief Gets the logged in user's role/type.
 * @return string|null 'admin', 'normal', or null if not logged in.
 */
function get_logged_user_type() {
    return $_SESSION['user_tipo'] ?? null;
}

/**
 * @brief Checks if the logged in user is an administrator.
 * @return bool True if the user is admin, false otherwise.
 */
function is_admin() {
    return get_logged_user_type() === 'admin';
}

/**
 * @brief Enforces authentication. Redirects to login page if the user is not logged in.
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}
