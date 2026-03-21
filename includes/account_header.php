<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmuniTrack</title>
    <link rel="stylesheet" href="css/<?php echo $css; ?>?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <link rel="icon" type="img/png" href="images/favicon.png">
</head>
<body>
    <header class="main-header">

        <!-- Mobile Combined Menu -->
        <div class="mobile-menu" id="mobileMenu">
            <a href="account_db.php">Account</a>
            <a href="digitalcard.php">Digital Card</a>
            <a href="booking.php">Book a Dose</a>
            <a href="appointments.php">Appointments</a>
            <hr>
            <a href="account_contact.php">Contact</a>
            <a href="account_about.php">About Us</a>
            <a href="account_privacy.php">Privacy</a>
            <a href="logout.php" class="nav-acc">Logout</a>

        </div>

        <div class="header-left">
            <a href="index.php" class="logo-link">
                <img src="images/favicon.png" class="nav-logo" alt="ImmuniTrack Logo">
            </a>
            <span class="brand-name">Immuni<span class="highlight">Track</span></span>
        </div>

        <!-- Hamburger Button -->
        <div class="hamburger" id="hamburger">
            ☰
        </div>

        <!-- Navigation -->
        <nav class="header-right" id="navMenu">
            <a href="account_db.php">Account</a>
            <a href="digitalcard.php">Digital Card</a>
            <a href="booking.php">Book a Dose</a>
            <a href="appointments.php">Appointments</a>
            <a href="logout.php" class="nav-acc">Logout</a>
        </nav>
    </header>


