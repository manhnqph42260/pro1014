<?php
// test_action.php
echo "<h1>🧪 Test Action Parameter</h1>";

echo "<p><strong>GET parameters:</strong></p>";
echo "<pre>";
print_r($_GET);
echo "</pre>";

echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'none') . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "</p>";

// Test links với các format khác nhau
echo "<h2>🔗 Test các format link:</h2>";
echo "<ol>";
echo "<li><a href='index.php?act=admin_guides'>index.php?act=admin_guides</a></li>";
echo "<li><a href='./index.php?act=admin_guides'>./index.php?act=admin_guides</a></li>";
echo "<li><a href='/index.php?act=admin_guides'>/index.php?act=admin_guides</a></li>";
echo "<li><a href='http://" . $_SERVER['HTTP_HOST'] . "/index.php?act=admin_guides'>Full URL</a></li>";
echo "<li><a href='?act=admin_guides'>?act=admin_guides (relative)</a></li>";
echo "</ol>";

// Test form submit
echo "<h2>📝 Test Form Submit:</h2>";
echo "<form method='GET' action='index.php'>
    <input type='hidden' name='act' value='admin_guides'>
    <button type='submit'>Submit to index.php?act=admin_guides</button>
</form>";
?>