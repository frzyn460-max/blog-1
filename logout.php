<?php
if (session_status() === PHP_SESSION_NONE) session_start();

unset($_SESSION['member_id']);
unset($_SESSION['member_name']);

header("Location: index.php");
exit();