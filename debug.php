<?php
session_start();
require_once './commons/env.php';
require_once './commons/function.php';

echo "<h2>DEBUG THÔNG TIN HIỆN TẠI</h2>";
echo "URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "ACT: " . ($_GET['act'] ?? 'không có') . "<br><br>";

echo "<h3>Session:</h3><pre>";
var_dump($_SESSION);
echo "</pre>";

echo "<h3>Routes đang có:</h3><pre>";
require_once './index.php'; // để load $routes
var_dump(array_keys($routes));
echo "</pre>";
?>