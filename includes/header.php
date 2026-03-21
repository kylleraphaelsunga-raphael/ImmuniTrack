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
            <a href="dashboard.php">Dashboard</a>
            <a href="information.php">Information</a>
            <a href="signin.php">SignIn</a>
            <a href="signup.php">SignUp</a>
            <hr>
            <a href="contact.php">Contact</a>
            <a href="about.php">About Us</a>
            <a href="privacy.php">Privacy</a>
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
            <a href="dashboard.php">Dashboard</a>
            <a href="information.php">Information</a>
            <a href="signin.php" class="nav-acc">SignIn</a>
            <a href="signup.php" class="nav-acc">SignUp</a>
        </nav>
    </header>


