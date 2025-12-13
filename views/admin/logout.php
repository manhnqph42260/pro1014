<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xóa tất cả session
session_unset();
session_destroy();

// Quay về trang login chính (không phải admin login)
header("Location: index.php?act=login");
exit();
?>