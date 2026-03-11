<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message = "";
$msg_class = "msg-error";

// Fetch existing profile
$profile_stmt = $conn->prepare("SELECT last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth FROM user_profile WHERE user_email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $last_name      = $_POST["last_name"];
    $first_name     = $_POST["first_name"];
    $middle_initial = $_POST["middle_initial"];
    $suffix         = $_POST["suffix"];
    $sex            = $_POST["sex"];
    $house_number   = $_POST["house_number"];
    $barangay       = $_POST["barangay"];
    $city           = $_POST["city"];
    $province       = $_POST["province"];
    $contact        = "+63" . $_POST["contact"];
    $dob            = $_POST["date_of_birth"];

    $check = $conn->prepare("SELECT user_email FROM user_profile WHERE user_email = ?");
    $check->bind_param("s", $user_email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE user_profile SET last_name=?, first_name=?, middle_initial=?, suffix=?, sex=?, house_number=?, barangay=?, city=?, province=?, contact_number=?, date_of_birth=? WHERE user_email=?");
        $stmt->bind_param("ssssssssssss", $last_name, $first_name, $middle_initial, $suffix, $sex, $house_number, $barangay, $city, $province, $contact, $dob, $user_email);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_profile (user_email, last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $user_email, $last_name, $first_name, $middle_initial, $suffix, $sex, $house_number, $barangay, $city, $province, $contact, $dob);
    }

    if ($stmt->execute()) {
        header("Location: account_db.php?success=1");
        exit();
    } else {
        $message = "Something went wrong. Please try again.";
    }

    $check->close();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - ImmuniTrack</title>
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
</head>
<body class="forms-bg">

<div class="forms-container">
    <div class="forms-card-wrapper">
        <section class="forms-card">
            <a href="account_db.php" class="link-back">← Back to Dashboard</a>

            <h2 class="title">Edit Profile</h2>

            <?php if($message): ?>
                <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST">

                <h3 class="form-section-title">Personal Information</h3>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="e.g. Dela Cruz"
                        value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="e.g. Juan"
                        value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Middle Initial</label>
                    <input type="text" name="middle_initial" placeholder="e.g. S" maxlength="5"
                        value="<?php echo htmlspecialchars($profile['middle_initial'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Suffix <span class="label-optional">(optional)</span></label>
                    <select name="suffix">
                        <option value="">-- None --</option>
                        <option value="Jr." <?php echo ($profile['suffix'] ?? '') == 'Jr.' ? 'selected' : ''; ?>>Jr.</option>
                        <option value="Sr." <?php echo ($profile['suffix'] ?? '') == 'Sr.' ? 'selected' : ''; ?>>Sr.</option>
                        <option value="II"  <?php echo ($profile['suffix'] ?? '') == 'II'  ? 'selected' : ''; ?>>II</option>
                        <option value="III" <?php echo ($profile['suffix'] ?? '') == 'III' ? 'selected' : ''; ?>>III</option>
                        <option value="IV"  <?php echo ($profile['suffix'] ?? '') == 'IV'  ? 'selected' : ''; ?>>IV</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Sex</label>
                    <select name="sex" required>
                        <option value="">-- Select --</option>
                        <option value="Male"   <?php echo ($profile['sex'] ?? '') == 'Male'   ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($profile['sex'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <h3 class="form-section-title">Address</h3>

                <div class="form-group">
                    <label>House / Unit Number</label>
                    <input type="text" name="house_number" placeholder="e.g. 123 or Unit 4B"
                        value="<?php echo htmlspecialchars($profile['house_number'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Barangay</label>
                    <input type="text" name="barangay" placeholder="e.g. Barangay San Jose"
                        value="<?php echo htmlspecialchars($profile['barangay'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>City / Municipality</label>
                    <input type="text" name="city" placeholder="e.g. Quezon City"
                        value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Province</label>
                    <input type="text" name="province" placeholder="e.g. Metro Manila"
                        value="<?php echo htmlspecialchars($profile['province'] ?? ''); ?>" required>
                </div>

                <h3 class="form-section-title">Contact Details</h3>

                <div class="form-group">
                    <label>Contact Number</label>
                    <div class="contact-group">
                        <span class="prefix">+63</span>
                        <input type="text" name="contact" placeholder="9123456789" maxlength="10"
                            value="<?php echo htmlspecialchars(ltrim($profile['contact_number'] ?? '', '+63')); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="date_of_birth"
                        value="<?php echo htmlspecialchars($profile['date_of_birth'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn-submit">Save Changes</button>
            </form>
        </section>
    </div>
</div>

</body>

</html>
