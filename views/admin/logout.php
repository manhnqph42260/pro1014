<?php
session_start();
session_unset();
session_destroy();

// Quay về trang login ở gốc
header("Location: ../../login.php");
exit();
?>
