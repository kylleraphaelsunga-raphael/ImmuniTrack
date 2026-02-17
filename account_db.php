<?php
include "database.php";
$css = "dashboard.css"; 
include 'includes/header.php'; 

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
?>

<div class="dashboard-wrapper">
    <header class="db-header">
        <div class="welcome-text">
            <h1>Welcome back, <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span></h1>
            <p>Managing records for: <?php echo htmlspecialchars($user_email); ?></p>
        </div>
        <a href="#" class="btn-primary">+ Add New Record</a>
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
                            <tr>
                                <td class="vax-name">COVID-19</td>
                                <td><span class="dose-tag">Dose 1</span></td>
                                <td>September 1, 2020</td>
                                <td><span class="status-done">Done</span></td>
                            </tr>
                            <tr>
                                <td class="vax-name">Influenza</td>
                                <td><span class="dose-tag">Annual</span></td>
                                <td>September 8, 2024</td>
                                <td><span class="status-done">Done</span></td>
                            </tr>
                            <tr>
                                <td class="vax-name">Dengue</td>
                                <td><span class="dose-tag">Dose 1</span></td>
                                <td>December 25, 2025</td>
                                <td><span class="status-done">Done</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <a href="logout.php" class="btn-secondary">LogOut</a>

    </div>
</div>

<?php include 'includes/footer.php'; ?>