<?php
global $conn;
$host = "localhost";
$name = "mydb";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$name;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}