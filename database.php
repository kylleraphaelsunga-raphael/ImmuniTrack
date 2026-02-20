<?php
session_start();
$servername = "localhost";
$username = "root"; // default XAMPP username
$password = "";     // default XAMPP password
$dbname = "immunitrack_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!-- 
CREATE DATABASE IF NOT EXISTS immunitrack_db;
USE immunitrack_db;

-- 1. Users Table
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    UNIQUE KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. User Profile Table
CREATE TABLE user_profile (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_email VARCHAR(100) NOT NULL,
    house_address TEXT DEFAULT NULL,
    contact_number VARCHAR(20) DEFAULT NULL,
    birthday DATE DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_profile_email FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Vaccination History Table
CREATE TABLE vaccination_history (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_email VARCHAR(100) NOT NULL,
    vaccine_brand VARCHAR(100) NOT NULL,
    dose VARCHAR(50) NOT NULL,
    date_administered DATE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Done',
    PRIMARY KEY (id),
    CONSTRAINT fk_vax_email FOREIGN KEY (user_email) REFERENCES users(email) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 -->
