<?php
require_once '../../includes/functions.php';
start_secure_session();
session_destroy();
header("Location: login.php");
exit;
?>
