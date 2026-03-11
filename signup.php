<?php
include "database.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - ImmuniTrack</title>
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
</head>

<body class="forms-bg">

    <?php
    $message = "";
    $msg_class = "msg-error";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        // Check if username or email already exists
        $check = $conn->prepare("SELECT user_email FROM users WHERE username = ? OR user_email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Username or Email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, user_email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $message = "Registration successful! <a href='signin.php'>Login here</a>";
                $msg_class = "msg-success";
            } else {
                $message = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }
        $check->close();
    }
    ?>

    <div class="forms-container">
        <div class="forms-card-wrapper">
            <section class="forms-card">
                <a href="dashboard.php" class="link-back">← Back to Home</a>

                <h2 class="title">Create Account</h2>

                <?php if ($message): ?>
                    <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Choose a username" required>
                    </div>

                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Create a password" required>
                    </div>

                    <button type="submit" class="btn-submit">Sign Up</button>
                </form>

                <p class="switch-text">
                    Already have an account? <a href="signin.php">Login</a>
                </p>
            </section>
        </div>
    </div>

</body>

</html>
