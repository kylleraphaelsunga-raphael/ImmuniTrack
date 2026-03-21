<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message    = "";
$msg_class  = "msg-error";

// Fetch existing profile including profile_pic
$profile_stmt = $conn->prepare("SELECT last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth, profile_pic FROM user_profile WHERE user_email = ?");
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

    // Handle profile picture upload
    $profile_pic = $profile['profile_pic'] ?? null;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $file_type     = $_FILES['profile_pic']['type'];
        $file_size     = $_FILES['profile_pic']['size'];

        if (in_array($file_type, $allowed_types) && $file_size <= 2097152) {
            $ext      = pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION);
            $filename = 'profile_' . md5($user_email) . '.' . $ext;
            $dest     = 'uploads/' . $filename;

            // Create uploads folder if it doesn't exist
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }

            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], $dest)) {
                $profile_pic = $filename;
            } else {
                $message   = "Failed to upload image. Please try again.";
                $msg_class = "msg-error";
            }
        } else {
            $message   = "Image must be JPG, PNG, or WebP and under 2MB.";
            $msg_class = "msg-error";
        }
    }

    if (empty($message)) {
        $check = $conn->prepare("SELECT user_email FROM user_profile WHERE user_email = ?");
        $check->bind_param("s", $user_email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE user_profile SET last_name=?, first_name=?, middle_initial=?, suffix=?, sex=?, house_number=?, barangay=?, city=?, province=?, contact_number=?, date_of_birth=?, profile_pic=? WHERE user_email=?");
            $stmt->bind_param("sssssssssssss", $last_name, $first_name, $middle_initial, $suffix, $sex, $house_number, $barangay, $city, $province, $contact, $dob, $profile_pic, $user_email);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_profile (user_email, last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth, profile_pic) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssssssss", $user_email, $last_name, $first_name, $middle_initial, $suffix, $sex, $house_number, $barangay, $city, $province, $contact, $dob, $profile_pic);
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

            <?php if ($message): ?>
                <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <!-- PROFILE PICTURE UPLOAD -->
                <h3 class="form-section-title" style="border-top:none; padding-top:0;">Profile Photo</h3>
                <div class="profile-pic-upload">
                    <div class="current-pic" id="previewCircle">
                        <?php if (!empty($profile['profile_pic'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($profile['profile_pic']); ?>"
                                 alt="Profile Photo" id="previewImg" class="pic-preview-img">
                        <?php else: ?>
                            <span id="previewEmoji" class="pic-emoji">👤</span>
                            <img id="previewImg" src="" alt="" class="pic-preview-img" style="display:none;">
                        <?php endif; ?>
                    </div>
                    <div class="pic-upload-info">
                        <strong class="pic-upload-name">
                            <?php echo !empty($profile['first_name']) ? htmlspecialchars($profile['first_name']) : 'Your Photo'; ?>
                        </strong>
                        <label class="btn-upload-pic" for="profile_pic">📷 Choose Photo</label>
                        <input type="file" id="profile_pic" name="profile_pic"
                               accept="image/*" style="display:none;"
                               onchange="previewPhoto(this)">
                        <p class="pic-hint">JPG, PNG or WebP — max 2MB</p>
                    </div>
                </div>

                <!-- PERSONAL INFORMATION -->
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

                <!-- ADDRESS -->
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

                <!-- CONTACT -->
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

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const emoji  = document.getElementById('previewEmoji');
            const img    = document.getElementById('previewImg');

            if (emoji) emoji.style.display = 'none';

            img.src          = e.target.result;
            img.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>
