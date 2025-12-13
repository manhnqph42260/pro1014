<?php
// check_base_url.php
require_once './commons/env.php';

echo "<h1>🔧 Check BASE_URL</h1>";
echo "<p><strong>BASE_URL:</strong> " . BASE_URL . "</p>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Test Links:</h2>";
echo "<ol>";
echo "<li><a href='" . BASE_URL . "index.php?act=admin_guides'>" . BASE_URL . "index.php?act=admin_guides</a></li>";
echo "<li><a href='/index.php?act=admin_guides'>/index.php?act=admin_guides</a></li>";
echo "<li><a href='index.php?act=admin_guides'>index.php?act=admin_guides</a></li>";
echo "<li><a href='./index.php?act=admin_guides'>./index.php?act=admin_guides</a></li>";
echo "</ol>";
?>