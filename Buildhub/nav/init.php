<?php
// /includes/init.php — PHP initializer (not Bootstrap CSS)
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}