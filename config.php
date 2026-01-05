<?php
// Sending to Main
// Database configuration (Hostinger)
$host = "localhost";
$user = "u506280443_crijuldbUser";
$password = "B;fNW7FS59";
$dbname = "u506280443_crijulDB";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    die("Database connection error.");
}

// Optional but recommended
$conn->set_charset("utf8mb4");