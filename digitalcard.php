<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];

$required_doses = [
    "Covid"      => 3,
    "Dengue"     => 3,
    "Flu"        => 1,
    "Chickenpox" => 2,
    "Rabies"     => 5
];

// Handle Mark as Complete — BEFORE any output
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["mark_complete"])) {
    $vax_category = $_POST["vax_category"];
    $stmt = $conn->prepare("UPDATE vaccination_history SET completed = 1 WHERE user_email = ? AND vax_category = ?");
    $stmt->bind_param("ss", $user_email, $vax_category);
    $stmt->execute();
    $stmt->close();
    header("Location: digitalcard.php");
    exit();
}

$css = "account_db.css";
include 'includes/account_header.php';

function formatName($p) {
    if (empty($p['last_name']) && empty($p['first_name'])) return 'N/A';
    $name = htmlspecialchars($p['last_name'] ?? '') . ', ' . htmlspecialchars($p['first_name'] ?? '');
    if (!empty($p['middle_initial'])) $name .= ' ' . htmlspecialchars($p['middle_initial']) . '.';
    if (!empty($p['suffix']))         $name .= ', ' . htmlspecialchars($p['suffix']);
    return $name;
}

$profile_stmt = $conn->prepare("SELECT last_name, first_name, middle_initial, suffix, sex, house_number, barangay, city, province, contact_number, date_of_birth FROM user_profile WHERE user_email = ?");
$profile_stmt->bind_param("s", $user_email);
$profile_stmt->execute();
$profile = $profile_stmt->get_result()->fetch_assoc();
$profile_stmt->close();

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
        <p class="ecard-subtitle">Mark a vaccine as complete to unlock its official certificate.</p>
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

        // Generate a document number
        $doc_number = strtoupper(substr($vax_category, 0, 3)) . '-' . strtoupper(substr(md5($user_email . $vax_category), 0, 8));
    ?>

    <div class="ecard-wrapper">

        <!-- Controls -->
        <div class="ecard-controls">
            <span class="ecard-status <?php echo $is_complete ? 'status-complete' : 'status-pending'; ?>">
                <?php echo $is_complete ? 'Completed' : 'Pending Completion'; ?>
            </span>

            <?php if ($all_recorded && !$is_complete): ?>
                <form method="POST">
                    <input type="hidden" name="vax_category" value="<?php echo htmlspecialchars($vax_category); ?>">
                    <button type="submit" name="mark_complete" class="btn-secondary">Mark as Complete</button>
                </form>
            <?php elseif (!$all_recorded): ?>
                <span class="ecard-doses-note"><?php echo $recorded; ?>/<?php echo $required; ?> doses recorded</span>
            <?php endif; ?>

            <?php if ($is_complete): ?>
                <button
                    class="btn-primary"
                    id="btn-<?php echo htmlspecialchars($vax_category); ?>"
                    onclick="downloadCard('<?php echo htmlspecialchars($vax_category); ?>', this)">
                    Download Certificate
                </button>
            <?php endif; ?>
        </div>

        <!-- CERTIFICATE -->
        <div class="ecard-id <?php echo !$is_complete ? 'ecard-locked' : ''; ?>"
             id="card-<?php echo htmlspecialchars($vax_category); ?>">

            <!-- Header Band -->
            <div class="ecard-cert-header">
                <div class="ecard-cert-logo-area">
                    <div class="ecard-cert-logo-box">
                        <img src="images/favicon.png" alt="ImmuniTrack">
                    </div>
                    <div class="ecard-cert-org">
                        <span class="ecard-cert-org-name">ImmuniTrack</span>
                        <span class="ecard-cert-org-sub">Dynamic Web Applications and Development Tools</span>
                    </div>
                </div>
                <div class="ecard-cert-doc-number">
                    Document No. <?php echo $doc_number; ?><br>
                    Issued: <?php echo date("d M Y"); ?>
                </div>
            </div>

            <!-- Title Section -->
            <div class="ecard-cert-title-section">
                <p class="ecard-cert-title">Official Vaccination Certificate</p>
                <div class="ecard-vaccine-name"><?php echo htmlspecialchars($vax_category); ?> Vaccine</div>
                <div class="ecard-vaccine-label">Immunization Record</div>
            </div>

            <!-- Body: Patient Info -->
            <div class="ecard-cert-body">
                <div class="ecard-cert-col">
                    <div class="ecard-field">
                        <span class="ecard-label">Full Name</span>
                        <span class="ecard-value"><?php echo formatName($profile); ?></span>
                    </div>
                    <div class="ecard-field">
                        <span class="ecard-label">Date of Birth</span>
                        <span class="ecard-value"><?php echo htmlspecialchars($profile['date_of_birth'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="ecard-field">
                        <span class="ecard-label">Sex</span>
                        <span class="ecard-value"><?php echo htmlspecialchars($profile['sex'] ?? 'N/A'); ?></span>
                    </div>
                </div>
                <div class="ecard-cert-col">
                    <div class="ecard-field">
                        <span class="ecard-label">Contact Number</span>
                        <span class="ecard-value"><?php echo htmlspecialchars($profile['contact_number'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="ecard-field">
                        <span class="ecard-label">Address</span>
                        <span class="ecard-value"><?php echo htmlspecialchars($full_address); ?></span>
                    </div>
                    <div class="ecard-field">
                        <span class="ecard-label">Email</span>
                        <span class="ecard-value"><?php echo htmlspecialchars($user_email); ?></span>
                    </div>
                </div>
            </div>

            <!-- Dose Records -->
            <div class="ecard-cert-doses-section">
                <p class="ecard-doses-heading">Vaccination Record</p>
                <div class="ecard-doses">
                    <?php foreach ($doses as $dose): ?>
                    <div class="ecard-dose-row">
                        <span>Dose <?php echo $dose['dose_number']; ?></span>
                        <span><?php echo htmlspecialchars($dose['vaccine_type']); ?></span>
                        <span><?php echo date("d M Y", strtotime($dose['vax_date'])); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Footer: Seal + Date + Signature -->
            <div class="ecard-cert-footer">

                <!-- Official Seal (SVG) -->
                <svg class="ecard-seal" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <!-- Outer ring -->
                    <circle cx="50" cy="50" r="46" fill="none" stroke="#B8962E" stroke-width="2"/>
                    <circle cx="50" cy="50" r="40" fill="none" stroke="#B8962E" stroke-width="0.8"/>
                    <!-- Inner fill -->
                    <circle cx="50" cy="50" r="38" fill="#00684A" opacity="0.08"/>
                    <!-- Star points around ring -->
                    <?php
                    $angles = range(0, 330, 30);
                    foreach ($angles as $angle):
                        $rad = deg2rad($angle);
                        $x = 50 + 43 * cos($rad);
                        $y = 50 + 43 * sin($rad);
                    ?>
                    <circle cx="<?php echo round($x,1); ?>" cy="<?php echo round($y,1); ?>" r="1.5" fill="#B8962E"/>
                    <?php endforeach; ?>
                    <!-- Cross / Shield center -->
                    <path d="M50 22 L50 78" stroke="#00684A" stroke-width="1.5" opacity="0.4"/>
                    <path d="M22 50 L78 50" stroke="#00684A" stroke-width="1.5" opacity="0.4"/>
                    <!-- Center shield -->
                    <path d="M50 30 L62 38 L62 52 Q62 62 50 68 Q38 62 38 52 L38 38 Z"
                          fill="none" stroke="#B8962E" stroke-width="1.5"/>
                    <!-- IT monogram -->
                    <text x="50" y="55" text-anchor="middle"
                          font-family="Outfit, sans-serif"
                          font-size="11" font-weight="700"
                          fill="#00684A">IT</text>
                    <!-- OFFICIAL text arc -->
                    <path id="arc-top" d="M 16 50 A 34 34 0 0 1 84 50" fill="none"/>
                    <text font-size="6" fill="#B8962E" font-family="Outfit, sans-serif" letter-spacing="2" font-weight="700">
                        <textPath href="#arc-top" startOffset="12%">OFFICIAL SEAL</textPath>
                    </text>
                    <!-- Bottom text arc -->
                    <path id="arc-bot" d="M 18 54 A 34 34 0 0 0 82 54" fill="none"/>
                    <text font-size="5.5" fill="#B8962E" font-family="Outfit, sans-serif" letter-spacing="1.5">
                        <textPath href="#arc-bot" startOffset="10%">IMMUNITRACK · PHILIPPINES</textPath>
                    </text>
                </svg>

                <!-- Completion Date -->
                <div class="ecard-completed-date">
                    <?php if ($is_complete): ?>
                        <span class="cert-date-label">Date Completed</span>
                        <span class="cert-date-value"><?php echo date("F j, Y", strtotime($last_date)); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Signature -->
                <div class="ecard-signature">
                    <div class="ecard-sig-line"></div>
                    <span class="ecard-sig-name">Health Records Officer</span>
                    <span class="ecard-sig-label">Authorized Signatory</span>
                </div>

            </div>

            <!-- Watermark -->
            <div class="ecard-immunitrack">ImmuniTrack · <?php echo date("Y"); ?></div>

            <!-- Locked Overlay -->
            <?php if (!$is_complete): ?>
            <div class="ecard-overlay">
                <span>Complete all doses to unlock this certificate</span>
            </div>
            <?php endif; ?>

        </div><!-- end .ecard-id -->
    </div><!-- end .ecard-wrapper -->

    <?php endforeach; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
function downloadCard(vaxCategory, btn) {
    const original = btn.innerHTML;
    btn.innerHTML  = 'Generating...';
    btn.disabled   = true;

    const card = document.getElementById('card-' + vaxCategory);
    const wasLocked = card.classList.contains('ecard-locked');
    card.classList.remove('ecard-locked');

    html2canvas(card, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#FEFCF7',
        logging: false,
        allowTaint: true
    }).then(function(canvas) {
        if (wasLocked) card.classList.add('ecard-locked');

        const link    = document.createElement('a');
        link.download = 'ImmuniTrack_' + vaxCategory + '_Certificate.png';
        link.href     = canvas.toDataURL('image/png');
        link.click();

        btn.innerHTML = original;
        btn.disabled  = false;
    }).catch(function() {
        if (wasLocked) card.classList.add('ecard-locked');
        btn.innerHTML = original;
        btn.disabled  = false;
        alert('Download failed. Please try again.');
    });
}
</script>

<?php include 'includes/account_footer.php'; ?>
