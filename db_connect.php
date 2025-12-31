<?php
// db_connect.php
// Update credentials if needed
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = 'Vinith@1234';
$DB_NAME = 'society_security';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}
session_start();
?>
