<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message    = "";
$msg_class  = "msg-error";
$booking_confirmed = false;
$booking_details   = [];

// Pre-fill name and contact from profile
$profile_name    = "";
$profile_contact = "";
$pstmt = $conn->prepare("SELECT first_name, last_name, contact_number FROM user_profile WHERE user_email = ?");
$pstmt->bind_param("s", $user_email);
$pstmt->execute();
$prow = $pstmt->get_result()->fetch_assoc();
$pstmt->close();
if ($prow) {
    $profile_name    = trim(($prow['first_name'] ?? '') . ' ' . ($prow['last_name'] ?? ''));
    $profile_contact = $prow['contact_number'] ?? '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name         = trim($_POST['full_name']);
    $contact_number    = trim($_POST['contact_number']);
    $vax_category      = $_POST['vax_category'];
    $vax_brand         = $_POST['vax_brand'];
    $booking_date      = $_POST['booking_date'];
    $booking_time      = $_POST['booking_time'];
    $clinic            = $_POST['clinic'];
    $medical_condition = trim($_POST['medical_condition'] ?? '');
    $notes             = trim($_POST['notes'] ?? '');

    // Count existing doses for dose number reference
    $check_doses = $conn->prepare("SELECT COUNT(*) as total FROM vaccination_history WHERE user_email = ? AND vax_category = ?");
    $check_doses->bind_param("ss", $user_email, $vax_category);
    $check_doses->execute();
    $existing = $check_doses->get_result()->fetch_assoc()['total'];
    $check_doses->close();
    $dose_number = $existing + 1;

    if (!empty($booking_date) && !empty($booking_time) && !empty($full_name)) {
        $stmt = $conn->prepare("INSERT INTO bookings (user_email, vax_category, vaccine_type, dose_number, booking_date, booking_time, clinic, medical_condition, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("sssisssss", $user_email, $vax_category, $vax_brand, $dose_number, $booking_date, $booking_time, $clinic, $medical_condition, $notes);
        if ($stmt->execute()) {
            $booking_confirmed = true;
            $booking_details = [
                'name'      => $full_name,
                'contact'   => $contact_number,
                'category'  => $vax_category,
                'brand'     => $vax_brand,
                'dose'      => $dose_number,
                'date'      => date("F j, Y", strtotime($booking_date)),
                'time'      => date("g:i A", strtotime($booking_time)),
                'clinic'    => $clinic,
                'condition' => $medical_condition ?: 'None',
                'notes'     => $notes ?: 'None',
            ];
        } else {
            $message = "Something went wrong. Please try again.";
        }
        $stmt->close();
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Time slots 8AM - 4:30PM every 30 min
$time_slots = [];
$start = strtotime("08:00");
$end   = strtotime("16:30");
while ($start <= $end) {
    $time_slots[] = date("H:i", $start);
    $start = strtotime("+30 minutes", $start);
}

$clinics = [
    "Sto. Rosario Vaccination Clinic",
    "Mining Health Center",
    "Pampang Medical Center",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Dose - ImmuniTrack</title>
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/booking.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body class="forms-bg">

<div class="booking-container">

<?php if ($booking_confirmed): ?>

    <!-- CONFIRMATION CARD -->
    <div class="forms-card-wrapper">
        <section class="forms-card confirmation-card">
            <div class="confirm-icon">✅</div>
            <h2 class="title" style="text-align:center;">Appointment Booked!</h2>
            <p class="confirm-subtitle">Your vaccination appointment has been scheduled. Please arrive <strong>10 minutes early</strong> and bring a valid ID.</p>

            <div class="confirm-details">
                <div class="confirm-row"><span class="confirm-label">Patient Name</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['name']); ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Contact</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['contact']); ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Clinic</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['clinic']); ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Vaccine</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['category']); ?> — <?php echo htmlspecialchars($booking_details['brand']); ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Dose</span><span class="confirm-value">Dose <?php echo $booking_details['dose']; ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Date & Time</span><span class="confirm-value"><?php echo $booking_details['date']; ?> at <?php echo $booking_details['time']; ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Medical Condition</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['condition']); ?></span></div>
                <div class="confirm-row"><span class="confirm-label">Notes</span><span class="confirm-value"><?php echo htmlspecialchars($booking_details['notes']); ?></span></div>
            </div>

            <div class="confirm-reminder">
                <p class="reminder-title">📌 Reminders</p>
                <ul>
                    <li>Bring a valid government-issued ID</li>
                    <li>Arrive 10 minutes before your scheduled time</li>
                    <li>Wear a short-sleeved shirt for easy injection access</li>
                    <li>Stay hydrated — drink water before and after</li>
                    <li>Eat a light meal before your appointment</li>
                    <li>Inform the staff of any allergies or medical conditions</li>
                </ul>
            </div>

            <div class="confirm-status-pill">
                <span class="dot"></span> Pending Confirmation
            </div>

            <div class="confirm-actions">
                <a href="appointments.php" class="btn-submit">View My Appointments</a>
                <a href="account_db.php" class="btn-back-link">← Back to Dashboard</a>
            </div>
        </section>
    </div>

<?php else: ?>

    <!-- TWO PANEL LAYOUT -->
    <div class="booking-wide">
        <div class="booking-layout">

            <!-- LEFT: Form -->
            <div class="booking-form-panel">
                <div class="forms-card-wrapper">
                    <section class="forms-card">
                        <a href="account_db.php" class="link-back">← Back to Dashboard</a>

                        <div class="booking-header">
                            <h2 class="title">Book a Dose</h2>
                            <span class="booking-badge">💉 Vaccination Appointment</span>
                        </div>
                        <p class="form-hint" style="margin-bottom:20px;">Fill in your details to schedule a vaccination appointment.</p>

                        <?php if ($message): ?>
                            <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
                        <?php endif; ?>

                        <form method="POST" id="bookingForm">

                            <h3 class="form-section-title" style="border-top:none; padding-top:0;">Patient Information</h3>
                            <div class="booking-two-col">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($profile_name); ?>" placeholder="e.g. Juan Dela Cruz" required>
                                </div>
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_number" value="<?php echo htmlspecialchars($profile_contact); ?>" placeholder="e.g. +63 917 123 4567" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Medical Condition <span class="label-optional">(if any)</span></label>
                                <input type="text" name="medical_condition" placeholder="e.g. Asthma, Diabetes, None">
                            </div>

                            <h3 class="form-section-title">Vaccine Details</h3>
                            <div class="booking-two-col">
                                <div class="form-group">
                                    <label>Vaccine Type / Category</label>
                                    <select id="vax_category" name="vax_category" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Covid">Covid-19</option>
                                        <option value="Dengue">Dengue</option>
                                        <option value="Flu">Flu Shots</option>
                                        <option value="Chickenpox">Chickenpox</option>
                                        <option value="Rabies">Anti-Rabies</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Vaccine Brand</label>
                                    <select id="vax_brand" name="vax_brand" required>
                                        <option value="">-- Select Brand --</option>
                                    </select>
                                </div>
                            </div>

                            <div class="dose-guideline" id="doseGuideline" style="display:none;">
                                <p class="guideline-title">💉 Dose Guidelines</p>
                                <div id="guidelineContent"></div>
                            </div>

                            <h3 class="form-section-title">Appointment Schedule</h3>
                            <div class="form-group">
                                <label>Preferred Clinic</label>
                                <select name="clinic" id="clinic" required>
                                    <option value="">-- Select Clinic --</option>
                                    <?php foreach ($clinics as $c): ?>
                                    <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="booking-two-col">
                                <div class="form-group">
                                    <label>Preferred Date</label>
                                    <input type="date" name="booking_date" id="booking_date" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Preferred Time Slot</label>
                                    <select name="booking_time" id="booking_time" required>
                                        <option value="">-- Select Time --</option>
                                        <?php foreach ($time_slots as $slot): ?>
                                        <option value="<?php echo $slot; ?>"><?php echo date("g:i A", strtotime($slot)); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <h3 class="form-section-title">Additional Notes</h3>
                            <div class="form-group">
                                <label>Notes <span class="label-optional">(optional)</span></label>
                                <textarea name="notes" rows="3" placeholder="e.g. previous allergic reactions, special requests..."></textarea>
                            </div>

                            <div class="booking-summary" id="bookingSummary" style="display:none;">
                                <p class="summary-title">📋 Appointment Summary</p>
                                <div class="summary-grid">
                                    <div class="sum-row"><span>Name</span><strong id="sum_name">—</strong></div>
                                    <div class="sum-row"><span>Clinic</span><strong id="sum_clinic">—</strong></div>
                                    <div class="sum-row"><span>Vaccine</span><strong id="sum_vax">—</strong></div>
                                    <div class="sum-row"><span>Brand</span><strong id="sum_brand">—</strong></div>
                                    <div class="sum-row"><span>Date</span><strong id="sum_date">—</strong></div>
                                    <div class="sum-row"><span>Time</span><strong id="sum_time">—</strong></div>
                                </div>
                            </div>

                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="consent" name="consent" required>
                                <label for="consent">I confirm that the information provided is accurate and I agree to the <a href="privacy.php">Privacy Policy</a> of ImmuniTrack.</label>
                            </div>

                            <button type="submit" class="btn-submit">Confirm Appointment →</button>
                        </form>
                    </section>
                </div>
            </div>

            <!-- RIGHT: Sidebar -->
            <div class="booking-sidebar">
                <div class="sidebar-card appt-reminders">
                    <p class="reminders-title">📌 Before Your Appointment</p>
                    <div class="reminders-list">
                        <div class="reminder-item"><span class="reminder-icon">🪪</span><div><strong>Bring Valid ID</strong><p>Government-issued ID required</p></div></div>
                        <div class="reminder-item"><span class="reminder-icon">⏰</span><div><strong>Arrive 10 Min Early</strong><p>Allow time for registration and screening</p></div></div>
                        <div class="reminder-item"><span class="reminder-icon">👕</span><div><strong>Wear Short Sleeves</strong><p>For easy injection access</p></div></div>
                        <div class="reminder-item"><span class="reminder-icon">💧</span><div><strong>Stay Hydrated</strong><p>Drink water before and after</p></div></div>
                        <div class="reminder-item"><span class="reminder-icon">🍽️</span><div><strong>Eat Before Coming</strong><p>Don't vaccinate on empty stomach</p></div></div>
                        <div class="reminder-item"><span class="reminder-icon">🩺</span><div><strong>Disclose Conditions</strong><p>Inform staff of allergies</p></div></div>
                    </div>
                </div>
                <div class="sidebar-card clinic-hours">
                    <p class="sidebar-card-title">🕐 Clinic Hours</p>
                    <div class="hours-list">
                        <div class="hours-row"><span>Monday – Friday</span><strong>8:00 AM – 5:00 PM</strong></div>
                        <div class="hours-row"><span>Saturday</span><strong>8:00 AM – 12:00 PM</strong></div>
                        <div class="hours-row closed"><span>Sunday</span><strong>Closed</strong></div>
                    </div>
                </div>
                <div class="sidebar-card what-to-expect">
                    <p class="sidebar-card-title">✅ What to Expect</p>
                    <ol class="expect-list">
                        <li>Present your ID and booking confirmation</li>
                        <li>Brief health screening by clinic staff</li>
                        <li>Vaccination administered by a nurse</li>
                        <li>15–30 minute observation period</li>
                        <li>Receive your updated vaccination record</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>

<?php endif; ?>

</div>
<script src="js/booking.js"></script>
</body>
</html>