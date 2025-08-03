<?php
session_start();

// Destroy all session data
$_SESSION = [];
session_unset();
session_destroy();

// Redirect to login or landing page
header("Location: ../landing/index.php");
exit();
?>
