<?php
include "database.php";

// Redirect to signin if not logged in
if (!isset($_SESSION["user_email"])) {
    header("Location: signin.php");
    exit();
}

$user_email = $_SESSION["user_email"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Only collect Vaccination Info
    $vax_type = $_POST['vax_brand']; 
    $num_doses = $_POST['dose_count'];
    $status = "Done"; 

    // Loop through the dynamic date inputs based on number of doses
    for ($i = 1; $i <= $num_doses; $i++) {
        $date_val = $_POST["vax_date_$i"];
        
        if(!empty($date_val)) {
            $stmt = $conn->prepare("INSERT INTO vaccination_history (user_email, vaccine_type, dose_number, vax_date, status) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $user_email, $vax_type, $i, $date_val, $status);
            $stmt->execute();
        }
    }
    
    $message = "Vaccination records successfully added!";
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
    <section class="forms-card">
        <a href="account_db.php" class="link-back">← Back to Account</a>
        <h2 class="title">Add Vaccination Record</h2>

        <?php if($message): ?>
            <p class="msg-success"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <h3>Vaccination Information</h3>
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 20px;">
                Log your vaccine history below. Personal details are now managed via your account profile.
            </p>
            
            <div class="form-group">
                <label>Vaccine Category</label>
                <select id="vax_category" onchange="updateBrands()" required>
                    <option value="">-- Select Category --</option>
                    <option value="Covid">Covid</option>
                    <option value="Dengue">Dengue</option>
                    <option value="Flu">Flu Shots</option>
                    <option value="Chickenpox">Chickenpox</option>
                    <option value="Rabies">Anti-Rabies</option>
                </select>
            </div>

            <div class="form-group">
                <label>Vaccine Brand/Type</label>
                <select id="vax_brand" name="vax_brand" required>
                    <option value="">-- Select Brand --</option>
                </select>
            </div>

            <div class="form-group">
                <label>Number of Doses</label>
                <select id="dose_count" name="dose_count" onchange="generateDateInputs()">
                    <option value="1">1 Dose</option>
                    <option value="2">2 Doses</option>
                    <option value="3">3 Doses</option>
                </select>
            </div>

            <div id="date_inputs_container">
                <div class="form-group">
                    <label>Date of Dose 1</label>
                    <input type="date" name="vax_date_1" required>
                </div>
            </div>
            
            <button type="submit" class="btn-submit">Save Vaccination History</button>
        </form>
    </section>
</div>

<script>
// Data for Brands
const vaxData = {
    "Covid": ["Pfizer", "Moderna", "AstraZeneca", "Sinovac"],
    "Dengue": ["Dengvaxia"],
    "Flu": ["Fluad", "Vaxigrip", "Fluarix"],
    "Chickenpox": ["Varivax", "Varilrix"],
    "Rabies": ["Verorab", "Rabipur"]
};

// Update brand dropdown based on category selection
function updateBrands() {
    const category = document.getElementById('vax_category').value;
    const brandSelect = document.getElementById('vax_brand');
    brandSelect.innerHTML = '<option value="">-- Select Brand --</option>';
    
    if(vaxData[category]) {
        vaxData[category].forEach(brand => {
            let option = new Option(brand, brand);
            brandSelect.add(option);
        });
    }
}

// Generate number of date inputs based on dose count selection
function generateDateInputs() {
    const count = document.getElementById('dose_count').value;
    const container = document.getElementById('date_inputs_container');
    container.innerHTML = '';

    for (let i = 1; i <= count; i++) {
        const div = document.createElement('div');
        div.className = 'form-group';
        div.innerHTML = `<label>Date of Dose ${i}</label><input type="date" name="vax_date_${i}" required>`;
        container.appendChild(div);
    }
}
</script>

</body>

</html>
