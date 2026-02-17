<?php
include "database.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ImmuniTrack</title>
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
    
    // Additional Profile Data
    $address = $_POST["address"];
    $contact = "+63" . $_POST["contact"];
    $birthday = $_POST["birthday"];

    // 🔎 CHECK IF USERNAME OR EMAIL EXISTS
    $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $message = "Username or Email already exists.";
    } else {
        // Start Transaction
        $conn->begin_transaction();

        try {
            // 1. Insert into 'users' table
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);
            $stmt->execute();

            // 2. Insert into 'user_profiles' table
            $stmt_profile = $conn->prepare("INSERT INTO user_profiles (user_email, house_address, contact_number, birthday) VALUES (?, ?, ?, ?)");
            $stmt_profile->bind_param("ssss", $email, $address, $contact, $birthday);
            $stmt_profile->execute();

            // If both succeed, commit to database
            $conn->commit();
            
            $message = "Registration successful! <a href='signin.php'>Login here</a>";
            $msg_class = "msg-success";
        } catch (Exception $e) {
            // If any error occurs, undo everything
            $conn->rollback();
            $message = "Something went wrong. Please try again.";
        }
        
        if(isset($stmt)) $stmt->close();
        if(isset($stmt_profile)) $stmt_profile->close();
    }
    $check->close();
}
?>

<div class="forms-container">
    <section class="forms-card">
        <a href="dashboard.php" class="link-back">← Back to Home</a>
        
        <h2 class="title">Create Account</h2>

        <?php if($message): ?>
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

            <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">
            <h3 style="color: var(--deep-jade); margin-bottom: 15px; font-size: 1.1rem;">Personal Details</h3>

            <div class="form-group">
                <label>House Address</label>
                <input type="text" name="address" placeholder="Bldg/Street/City" required>
            </div>

            <div class="form-group">
                <label>Contact Number</label>
                <div class="contact-group">
                    <span class="prefix">+63</span>
                    <input type="text" name="contact" placeholder="9123456789" maxlength="10" required>
                </div>
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="date" name="birthday" required>
            </div>
            
            <button type="submit" class="btn-submit">Sign Up</button>
        </form>

        <p class="switch-text">
            Already have an account? <a href="signin.php">Login</a>
        </p>
    </section>
</div>

</body>
</html>