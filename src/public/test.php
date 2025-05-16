<?php
// test.php

// エラーを強制的に表示する設定 (開発環境でのテスト用)
ini_set('display_errors', '1');
error_reporting(E_ALL);

echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working! Hello from test.php</p>";
echo "<p>Current PHP version: " . phpversion() . "</p>";
echo "<hr>";

echo "<h2>phpinfo() Output:</h2>";
phpinfo(); // PHPの詳細情報を出力

?>