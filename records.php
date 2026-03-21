<?php
include "database.php";

if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message = "";
$msg_class = "msg-success";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vax_category = $_POST['vax_category'];
    $vax_brand    = $_POST['vax_brand'];
    $date_val     = $_POST['vax_date'];
    $status       = "Done";

    // Check how many doses already exist for this category
    $check_doses = $conn->prepare("SELECT COUNT(*) as total FROM vaccination_history WHERE user_email = ? AND vax_category = ?");
    $check_doses->bind_param("ss", $user_email, $vax_category);
    $check_doses->execute();
    $existing = $check_doses->get_result()->fetch_assoc()['total'];
    $check_doses->close();

    $dose_number = $existing + 1;

    if (!empty($date_val)) {
        $stmt = $conn->prepare("INSERT INTO vaccination_history (user_email, vax_category, vaccine_type, dose_number, vax_date, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $user_email, $vax_category, $vax_brand, $dose_number, $date_val, $status);
        $stmt->execute();
        $stmt->close();
        $message = "Dose $dose_number ($vax_brand) for $vax_category successfully recorded!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vaccination Record - ImmuniTrack</title>
    <link rel="stylesheet" href="css/index.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
</head>
<body class="forms-bg">

<div class="forms-container">
    <div class="forms-card-wrapper">
        <section class="forms-card">
            <a href="account_db.php" class="link-back">← Back to Account</a>
            <h2 class="title">Add Vaccination Record</h2>

            <?php if ($message): ?>
                <p class="<?php echo $msg_class; ?>"><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST">
                <h3 class="form-section-title" style="border-top: none; padding-top: 0;">Vaccination Information</h3>
                <p class="form-hint">Add one dose at a time. You can use different brands per dose.</p><br>

                <div class="form-group">
                    <label>Vaccine Category</label>
                    <select id="vax_category" name="vax_category" onchange="updateBrands()" required>
                        <option value="">-- Select Category --</option>
                        <option value="Covid">Covid</option>
                        <option value="Dengue">Dengue</option>
                        <option value="Flu">Flu Shots</option>
                        <option value="Chickenpox">Chickenpox</option>
                        <option value="Rabies">Anti-Rabies</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Vaccine Brand Used for This Dose</label>
                    <select id="vax_brand" name="vax_brand" required>
                        <option value="">-- Select Brand --</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date Administered</label>
                    <input type="date" name="vax_date" required>
                </div>

                <button type="submit" class="btn-submit">Save Vaccination Record</button>
            </form>
        </section>
    </div>
</div>

<script>
const vaxData = {
    "Covid":      ["Pfizer", "Moderna", "AstraZeneca", "Sinovac"],
    "Dengue":     ["Dengvaxia"],
    "Flu":        ["Fluad", "Vaxigrip", "Fluarix"],
    "Chickenpox": ["Varivax", "Varilrix"],
    "Rabies":     ["Verorab", "Rabipur"]
};

function updateBrands() {
    const category = document.getElementById('vax_category').value;
    const brandSelect = document.getElementById('vax_brand');
    brandSelect.innerHTML = '<option value="">-- Select Brand --</option>';

    if (vaxData[category]) {
        vaxData[category].forEach(brand => {
            let option = new Option(brand, brand);
            brandSelect.add(option);
        });
    }
}
</script>
</body>
</html>
