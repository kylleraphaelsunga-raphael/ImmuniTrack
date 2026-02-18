<?php
include "database.php";
$css = "account_db.css"; 
include 'includes/account_header.php'; 

if (!isset($_SESSION["user_id"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];

// Fetch Profile Data
$profile_stmt = $conn->prepare("SELECT house_address, contact_number, birthday FROM user_profiles WHERE user_email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();

// Fetch Vaccination History
$vax_stmt = $conn->prepare("SELECT vaccine_type, dose_number, vax_date, status FROM vaccination_history WHERE user_email = ? ORDER BY vax_date DESC");
$vax_stmt->bind_param("s", $user_email);
$vax_stmt->execute();
$vax_history = $vax_stmt->get_result();
?>

<div class="dashboard-wrapper">
    <header class="db-header">
        <div class="welcome-text">
            <h1>Welcome back, <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span></h1>
            <p>Managing records for: <?php echo htmlspecialchars($user_email); ?></p>
        </div>
        <a href="records.php" class="btn-primary">+ Add New Record</a>
    </header>

    <div class="db-content">
        <aside class="profile-sidebar">
            <div class="glass-card">
                <h3>Personal Profile</h3>
                <div class="info-group">
                    <label>House Address</label>
                    <p><?php echo htmlspecialchars($profile['house_address'] ?? 'Not set'); ?></p>
                </div>
                <div class="info-group">
                    <label>Contact Number</label>
                    <p><?php echo htmlspecialchars($profile['contact_number'] ?? 'Not set'); ?></p>
                </div>
                <div class="info-group">
                    <label>Date of Birth:</label>
                    <p><?php echo htmlspecialchars($profile['birthday'] ?? 'Not set'); ?></p>
                </div>
                <a href="##" class="btn-secondary">Edit Profile</a>
            </div>
        </aside>

        <main class="history-main">
            <div class="glass-card">
                <h3>Vaccination History</h3>
                <div class="table-container">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Vaccine Brand</th>
                                <th>Dose</th>
                                <th>Date Administered</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($vax_history->num_rows > 0): ?>
                                <?php while($row = $vax_history->fetch_assoc()): ?>
                                    <tr>
                                        <td class="vax-name"><?php echo htmlspecialchars($row['vaccine_type']); ?></td>
                                        <td><span class="dose-tag">Dose <?php echo $row['dose_number']; ?></span></td>
                                        <td><?php echo date("F j, Y", strtotime($row['vax_date'])); ?></td>
                                        <td><span class="status-done">Done</span></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="empty-state">No vaccination records found. Start by adding one!</td>
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
