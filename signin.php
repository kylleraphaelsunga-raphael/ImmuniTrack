<?php
include "database.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ImmuniTrack</title>
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
</head>

<body class="forms-bg">

    <?php
    $message = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!isset($_POST['privacy_consent'])) {
            $message = "Please agree to the Privacy Policy and Terms to continue.";
        } else {
            $email = $_POST["email"];
            $password = $_POST["password"];

            $stmt = $conn->prepare("SELECT username, password FROM users WHERE user_email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($username, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION["user_email"] = $email;
                    $_SESSION["username"]   = $username;
                    header("Location: account_db.php");
                    exit();
                } else {
                    $message = "Invalid password.";
                }
            } else {
                $message = "No account found with that email.";
            }
            $stmt->close();
        }
    }
    ?>

    <div class="forms-container">
        <div class="forms-card-wrapper">
            <section class="forms-card">
                <a href="dashboard.php" class="link-back">← Back to Home</a>

                <h2 class="title">Login</h2>

                <?php if ($message): ?>
                    <p class="msg-error"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your registered email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" name="privacy_consent" id="privacy_consent" required>
                        <label for="privacy_consent">
                            I have read and understand the <a href="privacy.php">Privacy Policy</a> of ImmuniTrack.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit">Login to ImmuniTrack</button>
                </form>

                <p class="switch-text">
                    New here? <a href="signup.php">Create Account</a>
                </p>
            </section>
        </div>
    </div>

</body>

</html>
