<?php
include "database.php";
$css = "account_db.css";
include 'includes/account_header.php';

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];

// Helper function to format name
function formatName($p)
{
    if (empty($p['last_name']) && empty($p['first_name'])) return 'N/A';
    $name = htmlspecialchars($p['last_name'] ?? '') . ', ' . htmlspecialchars($p['first_name'] ?? '');
    if (!empty($p['middle_initial'])) $name .= ' ' . htmlspecialchars($p['middle_initial']) . '.';
    if (!empty($p['suffix']))         $name .= ', ' . htmlspecialchars($p['suffix']);
    return $name;
}

// Fetch Profile
$profile_stmt = $conn->prepare("SELECT last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth FROM user_profile WHERE user_email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

// Required doses per category
$required_doses = [
    "Covid"      => 3,
    "Dengue"     => 3,
    "Flu"        => 1,
    "Chickenpox" => 2,
    "Rabies"     => 5
];

// Handle Mark as Complete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_complete"])) {
    $vax_category = $_POST["vax_category"];
    $stmt = $conn->prepare("UPDATE vaccination_history SET completed = 1 WHERE user_email = ? AND vax_category = ?");
    $stmt->bind_param("ss", $user_email, $vax_category);
    $stmt->execute();
    $stmt->close();
    header("Location: digitalcard.php");
    exit();
}

// Fetch all vaccination records grouped by category
$vax_stmt = $conn->prepare("SELECT vax_category, vaccine_type, dose_number, vax_date, completed FROM vaccination_history WHERE user_email = ? ORDER BY vax_category, dose_number ASC");
$vax_stmt->bind_param("s", $user_email);
$vax_stmt->execute();
$vax_result = $vax_stmt->get_result();

$vaccines = [];
while ($row = $vax_result->fetch_assoc()) {
    $vaccines[$row['vax_category']][] = $row;
}
$vax_stmt->close();
?>

<div class="ecard-page">
    <div class="ecard-page-header">
        <h2 class="title">My Vaccination E-Cards</h2>
        <p class="ecard-subtitle">Mark a vaccine as complete to unlock its e-card.</p>
    </div>

    <?php if (empty($vaccines)): ?>
        <div class="ecard-empty">
            <p>No vaccination records found. <a href="records.php">Add one here.</a></p>
        </div>
    <?php endif; ?>

    <?php foreach ($vaccines as $vax_category => $doses): ?>
        <?php
        $required     = $required_doses[$vax_category] ?? 1;
        $recorded     = count($doses);
        $is_complete  = $doses[0]['completed'] == 1;
        $all_recorded = $recorded >= $required;
        $last_date    = end($doses)['vax_date'];

        $addr = array_filter([
            $profile['house_number'] ?? '',
            $profile['barangay'] ?? '',
            $profile['city'] ?? '',
            $profile['province'] ?? ''
        ]);
        $full_address = !empty($addr) ? implode(', ', $addr) : 'N/A';
        ?>

        <div class="ecard-wrapper">

            <!-- Controls -->
            <div class="ecard-controls">
                <span class="ecard-status <?php echo $is_complete ? 'status-complete' : 'status-pending'; ?>">
                    <?php echo $is_complete ? '✔ Completed' : 'Pending Completion'; ?>
                </span>

                <?php if ($all_recorded && !$is_complete): ?>
                    <form method="POST">
                        <input type="hidden" name="vax_category" value="<?php echo htmlspecialchars($vax_category); ?>">
                        <button href="digitalcard.php" type="submit" name="mark_complete" class="btn-secondary">Mark as Complete</button>
                    </form>
                <?php elseif (!$all_recorded): ?>
                    <span class="ecard-doses-note"><?php echo $recorded; ?>/<?php echo $required; ?> doses recorded</span>
                <?php endif; ?>

                <?php if ($is_complete): ?>
                    <button class="btn-primary" onclick="printCard('<?php echo htmlspecialchars($vax_category); ?>')">
                        🖨 Print / Download
                    </button>
                <?php endif; ?>
            </div>

            <!-- E-Card -->
            <div class="ecard-id <?php echo !$is_complete ? 'ecard-locked' : ''; ?>"
                id="card-<?php echo htmlspecialchars($vax_category); ?>">

                <!-- Left Panel -->
                <div class="ecard-left">
                    <div class="ecard-left-inner">
                        <div class="ecard-logo">💉</div>
                        <div class="ecard-vaccine-name"><?php echo htmlspecialchars($vax_category); ?></div>
                        <div class="ecard-vaccine-label">Vaccination Certificate</div>
                        <div class="ecard-divider"></div>
                        <div class="ecard-doses">
                            <?php foreach ($doses as $dose): ?>
                                <div class="ecard-dose-row">
                                    <span>Dose <?php echo $dose['dose_number']; ?> — <?php echo htmlspecialchars($dose['vaccine_type']); ?></span>
                                    <span><?php echo date("M j, Y", strtotime($dose['vax_date'])); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($is_complete): ?>
                            <div class="ecard-completed-date">
                                Completed: <?php echo date("M j, Y", strtotime($last_date)); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Panel -->
                <div class="ecard-right">
                    <div class="ecard-fields-grid">
                        <div class="ecard-field full-width">
                            <span class="ecard-label">Full Name</span>
                            <span class="ecard-value"><?php echo formatName($profile); ?></span>
                        </div>
                        <div class="ecard-field">
                            <span class="ecard-label">Sex</span>
                            <span class="ecard-value"><?php echo htmlspecialchars($profile['sex'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="ecard-field">
                            <span class="ecard-label">Date of Birth</span>
                            <span class="ecard-value"><?php echo htmlspecialchars($profile['date_of_birth'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="ecard-field">
                            <span class="ecard-label">Contact</span>
                            <span class="ecard-value"><?php echo htmlspecialchars($profile['contact_number'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="ecard-field full-width">
                            <span class="ecard-label">Address</span>
                            <span class="ecard-value"><?php echo htmlspecialchars($full_address); ?></span>
                        </div>
                    </div>
                    <div class="ecard-immunitrack">ImmuniTrack</div>
                </div>

                <!-- Locked Overlay -->
                <?php if (!$is_complete): ?>
                    <div class="ecard-overlay">
                        <span>🔒 Mark as complete to unlock this e-card</span>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    <?php endforeach; ?>
</div>

<script>
    function printCard(vaxCategory) {
        const card = document.getElementById('card-' + vaxCategory);
        const win = window.open('', '', 'width=900,height=500');
        win.document.write(`
        <html>
        <head>
            <title>Vaccination E-Card - ${vaxCategory}</title>
            <link rel="stylesheet" href="css/ecard.css">
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
        </head>
        <body style="margin:0; padding:20px; background:#fff;">
            ${card.outerHTML}
            <script>window.onload = () => { window.print(); window.close(); }<\/script>
        </body>
        </html>
    `);
        win.document.close();
    }
</script>

<?php include 'includes/account_footer.php'; ?>