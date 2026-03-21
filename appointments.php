<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message    = "";
$msg_class  = "msg-success";

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['booking_id'], $_POST['new_status'])) {
    $booking_id = (int) $_POST['booking_id'];
    $new_status = $_POST['new_status'];

    $allowed = ['Completed', 'Missed', 'Cancelled'];
    if (in_array($new_status, $allowed)) {
        $upd = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND user_email = ?");
        $upd->bind_param("sis", $new_status, $booking_id, $user_email);

        if ($upd->execute()) {
            if ($new_status === 'Completed') {
                $get = $conn->prepare("SELECT vax_category, vaccine_type, dose_number, booking_date FROM bookings WHERE id = ?");
                $get->bind_param("i", $booking_id);
                $get->execute();
                $brow = $get->get_result()->fetch_assoc();
                $get->close();

                if ($brow) {
                    $ins = $conn->prepare("INSERT INTO vaccination_history (user_email, vax_category, vaccine_type, dose_number, vax_date, status) VALUES (?, ?, ?, ?, ?, 'Done')");
                    $ins->bind_param("sssis", $user_email, $brow['vax_category'], $brow['vaccine_type'], $brow['dose_number'], $brow['booking_date']);
                    $ins->execute();
                    $ins->close();
                }
            }
            $message = "Appointment status updated successfully.";
        } else {
            $msg_class = "msg-error";
            $message   = "Failed to update status.";
        }
        $upd->close();
    }
}

// Fetch all bookings
$bstmt = $conn->prepare("SELECT id, vax_category, vaccine_type, dose_number, booking_date, booking_time, clinic, medical_condition, notes, status, created_at FROM bookings WHERE user_email = ? ORDER BY booking_date DESC");
$bstmt->bind_param("s", $user_email);
$bstmt->execute();
$bookings_result = $bstmt->get_result();
$bstmt->close();

// Separate into arrays for summary stats
$all_bookings = [];
while ($b = $bookings_result->fetch_assoc()) {
    $all_bookings[] = $b;
}

$total     = count($all_bookings);
$pending   = count(array_filter($all_bookings, fn($b) => $b['status'] === 'Pending'));
$completed = count(array_filter($all_bookings, fn($b) => $b['status'] === 'Completed'));
$missed    = count(array_filter($all_bookings, fn($b) => $b['status'] === 'Missed'));
$cancelled = count(array_filter($all_bookings, fn($b) => $b['status'] === 'Cancelled'));

// Required doses reference
$required_doses = [
    "Covid" => 3, "Dengue" => 3, "Flu" => 1, "Chickenpox" => 2, "Rabies" => 5
];

$css = "account_db.css";
include 'includes/account_header.php';
?>

<div class="dashboard-wrapper">

    <header class="db-header">
        <div class="welcome-text">
            <h1>My <span>Appointments</span></h1>
            <p>Track and manage your vaccination bookings</p>
        </div>
        <a href="booking.php" class="btn-primary">+ Book New Appointment</a>
    </header>

    <?php if ($message): ?>
        <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Summary Stats -->
    <div class="appt-stats-row">
        <div class="appt-stat-card">
            <span class="appt-stat-number"><?php echo $total; ?></span>
            <span class="appt-stat-label">Total Bookings</span>
        </div>
        <div class="appt-stat-card stat-pending">
            <span class="appt-stat-number"><?php echo $pending; ?></span>
            <span class="appt-stat-label">Pending</span>
        </div>
        <div class="appt-stat-card stat-completed">
            <span class="appt-stat-number"><?php echo $completed; ?></span>
            <span class="appt-stat-label">Completed</span>
        </div>
        <div class="appt-stat-card stat-missed">
            <span class="appt-stat-number"><?php echo $missed + $cancelled; ?></span>
            <span class="appt-stat-label">Missed / Cancelled</span>
        </div>
    </div>

    <div class="bookings-page">

        <?php if ($total === 0): ?>
            <div class="bookings-empty">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                        <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>
                    </svg>
                </div>
                <p>You have no appointments yet.</p>
                <p class="empty-sub">Book your first vaccination appointment to get started.</p>
                <a href="booking.php" class="btn-primary">Book Your First Appointment</a>
            </div>

        <?php else: ?>

            <!-- Reminder banner if any pending today or soon -->
            <?php
            $upcoming = array_filter($all_bookings, function($b) {
                $days = (strtotime($b['booking_date']) - time()) / 86400;
                return $b['status'] === 'Pending' && $days >= 0 && $days <= 3;
            });
            if (!empty($upcoming)):
                $next = reset($upcoming);
            ?>
            <div class="appt-reminder-banner">
                <div class="reminder-banner-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                </div>
                <div>
                    <strong>Upcoming Appointment</strong> —
                    <?php echo htmlspecialchars($next['vax_category']); ?> (<?php echo htmlspecialchars($next['vaccine_type']); ?>)
                    on <strong><?php echo date("F j, Y", strtotime($next['booking_date'])); ?></strong>
                    at <strong><?php echo date("g:i A", strtotime($next['booking_time'])); ?></strong>
                    — <?php echo htmlspecialchars($next['clinic']); ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="bookings-grid">
            <?php foreach ($all_bookings as $b):
                // Calculate days until/since appointment
                $days_diff = (strtotime($b['booking_date']) - strtotime(date('Y-m-d'))) / 86400;

                if ($b['status'] === 'Pending') {
                    if ($days_diff == 0)       $time_tag = ['Today', 'tag-today'];
                    elseif ($days_diff == 1)   $time_tag = ['Tomorrow', 'tag-soon'];
                    elseif ($days_diff <= 3)   $time_tag = ['In ' . (int)$days_diff . ' days', 'tag-soon'];
                    elseif ($days_diff < 0)    $time_tag = ['Overdue', 'tag-overdue'];
                    else                       $time_tag = [date("M j", strtotime($b['booking_date'])), 'tag-future'];
                } else {
                    $time_tag = null;
                }

                // Dose progress
                $req = $required_doses[$b['vax_category']] ?? 1;
                $progress_pct = min(100, round(($b['dose_number'] / $req) * 100));
            ?>

                <div class="booking-card status-<?php echo strtolower($b['status']); ?>">

                    <!-- Card Top -->
                    <div class="booking-card-top">
                        <div class="booking-card-vax">
                            <span class="bk-category"><?php echo htmlspecialchars($b['vax_category']); ?></span>
                            <span class="bk-brand"><?php echo htmlspecialchars($b['vaccine_type']); ?></span>
                        </div>
                        <div class="bk-top-right">
                            <span class="bk-status-pill bk-<?php echo strtolower($b['status']); ?>">
                                <?php echo $b['status']; ?>
                            </span>
                            <?php if ($time_tag): ?>
                                <span class="bk-time-tag <?php echo $time_tag[1]; ?>"><?php echo $time_tag[0]; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Dose Progress Bar -->
                    <div class="bk-progress-area">
                        <div class="bk-progress-label">
                            <span>Dose <?php echo $b['dose_number']; ?> of <?php echo $req; ?></span>
                            <span><?php echo $progress_pct; ?>%</span>
                        </div>
                        <div class="bk-progress-bar">
                            <div class="bk-progress-fill <?php echo $b['status'] === 'Completed' ? 'fill-done' : ($b['status'] === 'Missed' || $b['status'] === 'Cancelled' ? 'fill-missed' : ''); ?>"
                                 style="width: <?php echo $progress_pct; ?>%"></div>
                        </div>
                    </div>

                    <!-- Card Details -->
                    <div class="booking-card-details">
                        <div class="bk-detail-row">
                            <span>Date</span>
                            <strong><?php echo date("F j, Y", strtotime($b['booking_date'])); ?></strong>
                        </div>
                        <div class="bk-detail-row">
                            <span>Time</span>
                            <strong><?php echo date("g:i A", strtotime($b['booking_time'])); ?></strong>
                        </div>
                        <div class="bk-detail-row">
                            <span>Clinic</span>
                            <strong><?php echo htmlspecialchars($b['clinic']); ?></strong>
                        </div>
                        <div class="bk-detail-row">
                            <span>Booked On</span>
                            <strong><?php echo date("M j, Y", strtotime($b['created_at'])); ?></strong>
                        </div>
                        <?php if (!empty($b['medical_condition'])): ?>
                        <div class="bk-detail-row">
                            <span>Condition</span>
                            <strong><?php echo htmlspecialchars($b['medical_condition']); ?></strong>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($b['notes'])): ?>
                        <div class="bk-detail-row">
                            <span>Notes</span>
                            <strong><?php echo htmlspecialchars($b['notes']); ?></strong>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Next Steps hint for pending -->
                    <?php if ($b['status'] === 'Pending'): ?>
                    <div class="bk-next-steps">
                        <p class="bk-next-title">What to bring:</p>
                        <p class="bk-next-body">Valid ID &nbsp;·&nbsp; This booking confirmation &nbsp;·&nbsp; Short-sleeved clothing</p>
                    </div>
                    <?php endif; ?>

                    <!-- Status Actions -->
                    <?php if ($b['status'] === 'Pending'): ?>
                    <div class="booking-card-actions">
                        <p class="actions-label">Update Status:</p>
                        <div class="actions-row">
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                <input type="hidden" name="new_status" value="Completed">
                                <button type="submit" class="bk-btn bk-btn-complete"
                                        onclick="return confirm('Mark as Completed? This will add it to your vaccination history.')">
                                    Completed
                                </button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                <input type="hidden" name="new_status" value="Missed">
                                <button type="submit" class="bk-btn bk-btn-missed"
                                        onclick="return confirm('Mark as Missed?')">
                                    Missed
                                </button>
                            </form>
                            <form method="POST">
                                <input type="hidden" name="booking_id" value="<?php echo $b['id']; ?>">
                                <input type="hidden" name="new_status" value="Cancelled">
                                <button type="submit" class="bk-btn bk-btn-cancel"
                                        onclick="return confirm('Cancel this appointment?')">
                                    Cancel
                                </button>
                            </form>
                        </div>
                    </div>

                    <?php elseif ($b['status'] === 'Completed'): ?>
                    <div class="booking-card-done">
                        Dose recorded in your vaccination history
                    </div>

                    <?php elseif ($b['status'] === 'Missed'): ?>
                    <div class="booking-card-missed-msg">
                        You missed this appointment. <a href="booking.php">Rebook</a>
                    </div>

                    <?php elseif ($b['status'] === 'Cancelled'): ?>
                    <div class="booking-card-missed-msg">
                        This appointment was cancelled. <a href="booking.php">Book again</a>
                    </div>
                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/account_footer.php'; ?>