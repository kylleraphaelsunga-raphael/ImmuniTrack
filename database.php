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
    user_email VARCHAR(100) NOT NULL,
    username   VARCHAR(50)  NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (user_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. User Profile Table
CREATE TABLE user_profile (
    user_email     VARCHAR(100) NOT NULL,
    last_name      VARCHAR(50)  DEFAULT NULL,
    first_name     VARCHAR(50)  DEFAULT NULL,
    middle_initial VARCHAR(5)   DEFAULT NULL,
    suffix         VARCHAR(10)  DEFAULT NULL,
    sex            ENUM('Male', 'Female') DEFAULT NULL,
    contact_number VARCHAR(20)  DEFAULT NULL,
    date_of_birth  DATE         DEFAULT NULL,
    house_number   VARCHAR(20)  DEFAULT NULL,
    barangay       VARCHAR(100) DEFAULT NULL,
    city           VARCHAR(100) DEFAULT NULL,
    province       VARCHAR(100) DEFAULT NULL,
    PRIMARY KEY (user_email),
    CONSTRAINT fk_profile_email 
        FOREIGN KEY (user_email) REFERENCES users(user_email) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Vaccination History Table
CREATE TABLE vaccination_history (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    user_email    VARCHAR(100) NOT NULL,
    vax_category  VARCHAR(50)  DEFAULT NULL,
    vaccine_type  VARCHAR(100) DEFAULT NULL,
    dose_number   INT(11)      DEFAULT NULL,
    vax_date      DATE         DEFAULT NULL,
    status        ENUM('Done', 'Pending') DEFAULT 'Done',
    completed     TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    CONSTRAINT fk_vax_email 
        FOREIGN KEY (user_email) REFERENCES users(user_email) 
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- . CREATE TABLE bookings (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    user_email    VARCHAR(100) NOT NULL,
    vax_category  VARCHAR(50)  DEFAULT NULL,
    vaccine_type  VARCHAR(100) DEFAULT NULL,
    dose_number   INT(11)      DEFAULT NULL,
    booking_date  DATE         DEFAULT NULL,
    booking_time  TIME         DEFAULT NULL,
    clinic        VARCHAR(150) DEFAULT NULL,
    medical_condition TEXT     DEFAULT NULL,
    notes         TEXT         DEFAULT NULL,
    status        ENUM('Pending','Completed','Missed','Cancelled') DEFAULT 'Pending',
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    CONSTRAINT fk_booking_email
        FOREIGN KEY (user_email) REFERENCES users(user_email)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
 -->
