<?php
/**
 * @file logout.php
 * @brief Logs the user out by destroying the session and redirecting to the login page.
 */
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit;
