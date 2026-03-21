<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$css = "account_db.css";
include 'includes/account_header.php';

$user_email = $_SESSION["user_email"];

function formatName($p) {
    if (empty($p['last_name']) && empty($p['first_name'])) return 'Not set';
    $name = htmlspecialchars($p['last_name'] ?? '') . ', ' . htmlspecialchars($p['first_name'] ?? '');
    if (!empty($p['middle_initial'])) $name .= ' ' . htmlspecialchars($p['middle_initial']) . '.';
    if (!empty($p['suffix']))         $name .= ', ' . htmlspecialchars($p['suffix']);
    return $name;
}

// Fetch Profile
$profile_stmt = $conn->prepare("SELECT last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth, profile_pic FROM user_profile WHERE user_email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

// Fetch vaccination history stats
$stats_stmt = $conn->prepare("SELECT status, COUNT(*) as cnt FROM vaccination_history WHERE user_email = ? GROUP BY status");
$stats_stmt->bind_param("s", $user_email);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$vax_done = 0;
while ($s = $stats_result->fetch_assoc()) {
    if ($s['status'] === 'Done') $vax_done = $s['cnt'];
}
$stats_stmt->close();

// Count total bookings
$book_stmt = $conn->prepare("SELECT COUNT(*) as total, SUM(status='Pending') as pending FROM bookings WHERE user_email = ?");
$book_stmt->bind_param("s", $user_email);
$book_stmt->execute();
$book_row = $book_stmt->get_result()->fetch_assoc();
$book_stmt->close();
$total_bookings   = $book_row['total'] ?? 0;
$pending_bookings = $book_row['pending'] ?? 0;

// Count vaccine categories completed
$cat_stmt = $conn->prepare("SELECT COUNT(DISTINCT vax_category) as cats FROM vaccination_history WHERE user_email = ? AND completed = 1");
$cat_stmt->bind_param("s", $user_email);
$cat_stmt->execute();
$cats_completed = $cat_stmt->get_result()->fetch_assoc()['cats'] ?? 0;
$cat_stmt->close();

// Next upcoming appointment
$next_stmt = $conn->prepare("SELECT vax_category, vaccine_type, booking_date, booking_time, clinic FROM bookings WHERE user_email = ? AND status = 'Pending' AND booking_date >= CURDATE() ORDER BY booking_date ASC, booking_time ASC LIMIT 1");
$next_stmt->bind_param("s", $user_email);
$next_stmt->execute();
$next_appt = $next_stmt->get_result()->fetch_assoc();
$next_stmt->close();

// Fetch combined Vaccination History + Bookings
$vax_stmt = $conn->prepare("
    SELECT vax_category, vaccine_type, dose_number, vax_date, status, 'history' AS source
    FROM vaccination_history WHERE user_email = ?
    UNION ALL
    SELECT vax_category, vaccine_type, dose_number, booking_date AS vax_date, status, 'booking' AS source
    FROM bookings WHERE user_email = ? AND status IN ('Pending', 'Missed', 'Cancelled')
    ORDER BY vax_date DESC, vax_category ASC
");
$vax_stmt->bind_param("ss", $user_email, $user_email);
$vax_stmt->execute();
$vax_history = $vax_stmt->get_result();
?>

<div class="dashboard-wrapper">

    <?php if (isset($_GET['success'])): ?>
        <p class="msg-success">Profile updated successfully!</p>
    <?php endif; ?>

    <!-- Header -->
    <header class="db-header">
        <div class="welcome-text">
            <h1>Welcome back, <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span></h1>
            <p>Managing records for: <?php echo htmlspecialchars($user_email); ?></p>
        </div>
        <a href="records.php" class="btn-primary">+ Add New Record</a>
    </header>

    <!-- Quick Stats -->
    <div class="db-stats-row">
        <div class="db-stat-card">
            <div class="db-stat-icon stat-icon-doses">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/>
                    <line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>
                </svg>
            </div>
            <div class="db-stat-info">
                <span class="db-stat-number"><?php echo $vax_done; ?></span>
                <span class="db-stat-label">Vaccination Recorded</span>
            </div>
        </div>
        <div class="db-stat-card">
            <div class="db-stat-icon stat-icon-cats">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            <div class="db-stat-info">
                <span class="db-stat-number"><?php echo $cats_completed; ?>/5</span>
                <span class="db-stat-label">E-Cards Unlocked</span>
            </div>
        </div>
        <div class="db-stat-card">
            <div class="db-stat-icon stat-icon-appt">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                </svg>
            </div>
            <div class="db-stat-info">
                <span class="db-stat-number"><?php echo $total_bookings; ?></span>
                <span class="db-stat-label">Total Appointments</span>
            </div>
        </div>
        <div class="db-stat-card <?php echo $pending_bookings > 0 ? 'stat-card-alert' : ''; ?>">
            <div class="db-stat-icon stat-icon-pending">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
            <div class="db-stat-info">
                <span class="db-stat-number"><?php echo $pending_bookings; ?></span>
                <span class="db-stat-label">Pending Appointments</span>
            </div>
        </div>
    </div>

    <!-- Next Appointment Banner -->
    <?php if ($next_appt): ?>
    <div class="db-next-appt-banner">
        <div class="next-appt-left">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>
                <path d="M8 14h.01M12 14h.01M16 14h.01"/>
            </svg>
            <div>
                <span class="next-appt-title">Next Appointment</span>
                <span class="next-appt-detail">
                    <strong><?php echo htmlspecialchars($next_appt['vax_category']); ?></strong>
                    (<?php echo htmlspecialchars($next_appt['vaccine_type']); ?>) —
                    <?php echo date("F j, Y", strtotime($next_appt['booking_date'])); ?> at
                    <?php echo date("g:i A", strtotime($next_appt['booking_time'])); ?> ·
                    <?php echo htmlspecialchars($next_appt['clinic']); ?>
                </span>
            </div>
        </div>
        <a href="appointments.php" class="next-appt-link">View →</a>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div class="db-content">

        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <div class="glass-card">
                <div class="profile-avatar">
                    <?php if (!empty($profile['profile_pic'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($profile['profile_pic']); ?>"
                             class="profile-pic-img" alt="Profile Photo">
                    <?php else: ?>
                        <div class="profile-pic-placeholder">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#00684A" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Name + email under avatar -->
                <div class="profile-name-block">
                    <p class="profile-display-name"><?php echo formatName($profile); ?></p>
                    <p class="profile-display-email"><?php echo htmlspecialchars($user_email); ?></p>
                </div>

                <div class="profile-divider"></div>

                <h3>Personal Profile</h3>
                <div class="info-group">
                    <label>Sex</label>
                    <p><?php echo htmlspecialchars($profile['sex'] ?? 'Not set'); ?></p>
                </div>
                <div class="info-group">
                    <label>Date of Birth</label>
                    <p><?php echo htmlspecialchars($profile['date_of_birth'] ?? 'Not set'); ?></p>
                </div>
                <div class="info-group">
                    <label>Contact Number</label>
                    <p><?php echo htmlspecialchars($profile['contact_number'] ?? 'Not set'); ?></p>
                </div>
                <div class="info-group">
                    <label>Address</label>
                    <p><?php
                        $addr = array_filter([
                            $profile['house_number'] ?? '',
                            $profile['barangay'] ?? '',
                            $profile['city'] ?? '',
                            $profile['province'] ?? ''
                        ]);
                        echo htmlspecialchars(!empty($addr) ? implode(', ', $addr) : 'Not set');
                    ?></p>
                </div>

                <div class="profile-divider"></div>

                <!-- Mini vax stats inside sidebar -->
                <div class="sidebar-mini-stats">
                    <div class="sidebar-mini-stat">
                        <span class="mini-stat-num"><?php echo $vax_done; ?></span>
                        <span class="mini-stat-lbl">Vaccines</span>
                    </div>
                    <div class="sidebar-mini-stat">
                        <span class="mini-stat-num"><?php echo $cats_completed; ?></span>
                        <span class="mini-stat-lbl">E-Cards</span>
                    </div>
                    <div class="sidebar-mini-stat">
                        <span class="mini-stat-num"><?php echo $pending_bookings; ?></span>
                        <span class="mini-stat-lbl">Pending</span>
                    </div>
                </div>

                <a href="edit_profile.php" class="btn-secondary">Edit Profile</a>
            </div>
        </aside>

        <!-- History Table -->
        <main class="history-main">
            <div class="glass-card">
                <div class="history-card-header">
                    <h3>Vaccination History</h3>
                    <a href="appointments.php" class="btn-appointments">My Appointments</a>
                </div>
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Brand Used</th>
                                <th>Dose</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($vax_history->num_rows > 0): ?>
                                <?php while ($row = $vax_history->fetch_assoc()): ?>
                                <tr>
                                    <td class="vax-name"><?php echo htmlspecialchars($row['vax_category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['vaccine_type']); ?></td>
                                    <td><span class="dose-tag">Dose <?php echo $row['dose_number']; ?></span></td>
                                    <td><?php echo date("F j, Y", strtotime($row['vax_date'])); ?></td>
                                    <td>
                                        <?php
                                        switch ($row['status']) {
                                            case 'Done':      echo '<span class="status-done">Done</span>'; break;
                                            case 'Pending':   echo '<span class="status-pending">Pending</span>'; break;
                                            case 'Missed':    echo '<span class="status-missed">Missed</span>'; break;
                                            case 'Cancelled': echo '<span class="status-cancelled">Cancelled</span>'; break;
                                            default:          echo '<span class="status-pending">' . htmlspecialchars($row['status']) . '</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        No vaccination records found.
                                        <a href="records.php" style="color:var(--deep-jade); font-weight:600;">Add one here →</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

    </div>
</div>

<?php include 'includes/account_footer.php'; ?>
