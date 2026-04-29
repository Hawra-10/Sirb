<?php
session_start();

$_SESSION = [];
session_unset();
session_destroy();

header("Location: splash.php?logout=success");
exit;
?>