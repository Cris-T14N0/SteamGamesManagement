<?php
// Sending to Main
// Database configuration (Hostinger)
$host = "localhost";
$user = "root";
$password = "B;fNW7FS59";
$dbname = "sgm_db";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection error.");
}

// Optional but recommended
$conn->set_charset("utf8mb4");
