<?php

$host = "mysql";
$user = "root";
$pass = "root";
$db   = "toy_universe";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("DB connect failed: " . $conn->connect_error);
}

echo "Connected!";
?>