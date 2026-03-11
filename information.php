<?php
$css = "dashboard.css";
include 'includes/header.php';

// Get disease from URL param, default to covid
$disease = isset($_GET['disease']) ? strtolower($_GET['disease']) : 'covid';

// Disease data
$diseases = [
    'covid' => [
        'title'       => 'COVID-19',
        'subtitle'    => 'Coronavirus Disease 2019',
        'description' => 'COVID-19 is a highly contagious respiratory illness caused by the SARS-CoV-2 virus. It spreads primarily through respiratory droplets and can cause mild to severe illness.',
        'image'       => 'images/covid.png',
        'color'       => '#00684A',
        'stats' => [
            ['label' => 'Doses Required',   'value' => '3',    'icon' => '💉'],
            ['label' => 'Effectiveness',    'value' => '95%',  'icon' => '🛡️'],
            ['label' => 'Booster Interval', 'value' => '6 mo', 'icon' => '📅'],
            ['label' => 'Age Requirement',  'value' => '5+',   'icon' => '👤'],
        ],
        'quick_facts' => [
            'First identified in Wuhan, China in December 2019',
            'Declared a global pandemic by WHO in March 2020',
            'RNA virus that mutates frequently into new variants',
            'Can cause long-term symptoms known as Long COVID',
        ],
        'symptoms' => [
            ['icon' => '🤒', 'name' => 'Fever',           'desc' => 'High temperature, often above 38°C'],
            ['icon' => '😮‍💨', 'name' => 'Shortness of Breath', 'desc' => 'Difficulty breathing or chest tightness'],
            ['icon' => '👃', 'name' => 'Loss of Smell',   'desc' => 'Sudden anosmia or altered sense of taste'],
            ['icon' => '😫', 'name' => 'Fatigue',         'desc' => 'Extreme tiredness and body weakness'],
        ],
        'side_effects' => [
            ['icon' => '💪', 'name' => 'Sore Arm',        'desc' => 'Pain or swelling at injection site'],
            ['icon' => '🥵', 'name' => 'Mild Fever',      'desc' => 'Low-grade fever for 1–2 days post-vaccine'],
            ['icon' => '😴', 'name' => 'Fatigue',         'desc' => 'Tiredness lasting up to 48 hours'],
            ['icon' => '🤕', 'name' => 'Headache',        'desc' => 'Mild headache that resolves quickly'],
        ],
        'who_should_get' => [
            ['icon' => '👶', 'name' => 'Children 5+',     'desc' => 'Pediatric doses available for ages 5 and above'],
            ['icon' => '👴', 'name' => 'Elderly',         'desc' => 'High-risk group, strongly recommended'],
            ['icon' => '🤰', 'name' => 'Pregnant Women',  'desc' => 'Safe and recommended during pregnancy'],
            ['icon' => '🏥', 'name' => 'Immunocompromised', 'desc' => 'Extra doses recommended for immune conditions'],
        ],
        'brands' => ['Pfizer', 'Moderna', 'AstraZeneca', 'Sinovac'],
    ],
    'flu' => [
        'title'       => 'Influenza',
        'subtitle'    => 'Seasonal Flu Vaccine',
        'description' => 'Influenza is a contagious respiratory illness caused by influenza viruses. Annual vaccination is the best way to protect against flu and its potentially serious complications.',
        'image'       => 'images/influenza.webp',
        'color'       => '#0077B6',
        'stats' => [
            ['label' => 'Doses Required',   'value' => '1',      'icon' => '💉'],
            ['label' => 'Effectiveness',    'value' => '40–60%', 'icon' => '🛡️'],
            ['label' => 'Given Annually',   'value' => 'Yearly', 'icon' => '📅'],
            ['label' => 'Age Requirement',  'value' => '6 mo+',  'icon' => '👤'],
        ],
        'quick_facts' => [
            'Flu viruses change every year, requiring annual updates',
            'Best time to vaccinate is before flu season begins',
            'Can prevent millions of illnesses and hospitalizations',
            'Especially important for high-risk populations',
        ],
        'symptoms' => [
            ['icon' => '🤒', 'name' => 'High Fever',      'desc' => 'Sudden onset fever of 38–40°C'],
            ['icon' => '🤧', 'name' => 'Runny Nose',      'desc' => 'Congestion and nasal discharge'],
            ['icon' => '😣', 'name' => 'Body Aches',      'desc' => 'Severe muscle pain and body soreness'],
            ['icon' => '😮‍💨', 'name' => 'Dry Cough',    'desc' => 'Persistent cough with chest discomfort'],
        ],
        'side_effects' => [
            ['icon' => '💪', 'name' => 'Sore Arm',        'desc' => 'Redness or swelling at injection site'],
            ['icon' => '🥵', 'name' => 'Low Fever',       'desc' => 'Mild fever within first day or two'],
            ['icon' => '🤕', 'name' => 'Headache',        'desc' => 'Mild to moderate headache'],
            ['icon' => '😴', 'name' => 'Fatigue',         'desc' => 'Brief tiredness lasting 1–2 days'],
        ],
        'who_should_get' => [
            ['icon' => '👶', 'name' => 'Infants 6 mo+',   'desc' => 'Two doses needed in the first year'],
            ['icon' => '👴', 'name' => 'Elderly 65+',     'desc' => 'Higher-dose vaccine recommended'],
            ['icon' => '🤰', 'name' => 'Pregnant Women',  'desc' => 'Protects both mother and newborn'],
            ['icon' => '🏥', 'name' => 'Chronic Illness', 'desc' => 'Diabetes, asthma, heart disease patients'],
        ],
        'brands' => ['Fluad', 'Vaxigrip', 'Fluarix'],
    ],
    'chickenpox' => [
        'title'       => 'Chickenpox',
        'subtitle'    => 'Varicella Vaccine',
        'description' => 'Chickenpox is a highly contagious disease caused by the varicella-zoster virus, characterized by an itchy blister-like rash. The vaccine provides strong, long-lasting protection.',
        'image'       => 'images/chickenpox.webp',
        'color'       => '#9B59B6',
        'stats' => [
            ['label' => 'Doses Required',   'value' => '2',    'icon' => '💉'],
            ['label' => 'Effectiveness',    'value' => '98%',  'icon' => '🛡️'],
            ['label' => 'Dose Interval',    'value' => '4–8 wk', 'icon' => '📅'],
            ['label' => 'Age Requirement',  'value' => '12 mo+', 'icon' => '👤'],
        ],
        'quick_facts' => [
            'Caused by varicella-zoster virus (VZV)',
            'Virus can reactivate later in life as shingles',
            'Most contagious before the rash appears',
            'Can cause serious complications in adults',
        ],
        'symptoms' => [
            ['icon' => '🔴', 'name' => 'Itchy Rash',      'desc' => 'Red spots that develop into fluid-filled blisters'],
            ['icon' => '🤒', 'name' => 'Fever',           'desc' => 'Mild to moderate fever with chills'],
            ['icon' => '😫', 'name' => 'Fatigue',         'desc' => 'Tiredness and general malaise'],
            ['icon' => '🤕', 'name' => 'Headache',        'desc' => 'Headache before rash appears'],
        ],
        'side_effects' => [
            ['icon' => '💪', 'name' => 'Sore Arm',        'desc' => 'Tenderness at the injection site'],
            ['icon' => '🔴', 'name' => 'Mild Rash',       'desc' => 'Small rash may appear near injection site'],
            ['icon' => '🥵', 'name' => 'Low Fever',       'desc' => 'Mild fever lasting a day or two'],
            ['icon' => '😴', 'name' => 'Fatigue',         'desc' => 'Brief tiredness after vaccination'],
        ],
        'who_should_get' => [
            ['icon' => '👶', 'name' => 'Children 12 mo+', 'desc' => 'First dose at 12–15 months recommended'],
            ['icon' => '🧑', 'name' => 'Unvaccinated Adults', 'desc' => 'Adults with no prior infection or vaccine'],
            ['icon' => '🏥', 'name' => 'Healthcare Workers', 'desc' => 'Strongly recommended for medical staff'],
            ['icon' => '🤰', 'name' => 'Pre-Pregnancy',   'desc' => 'Vaccinate before getting pregnant'],
        ],
        'brands' => ['Varivax', 'Varilrix'],
    ],
    'dengue' => [
        'title'       => 'Dengue Fever',
        'subtitle'    => 'Dengue Vaccine',
        'description' => 'Dengue is a mosquito-borne viral infection causing severe flu-like illness. Transmitted by the Aedes mosquito, it is prevalent in tropical and subtropical regions including the Philippines.',
        'image'       => 'images/dengue.jpg',
        'color'       => '#E63946',
        'stats' => [
            ['label' => 'Doses Required',   'value' => '3',    'icon' => '💉'],
            ['label' => 'Effectiveness',    'value' => '82%',  'icon' => '🛡️'],
            ['label' => 'Dose Interval',    'value' => '6 mo', 'icon' => '📅'],
            ['label' => 'Age Requirement',  'value' => '9–45', 'icon' => '👤'],
        ],
        'quick_facts' => [
            'Caused by four serotypes of dengue virus (DENV 1–4)',
            'Philippines is a dengue-endemic country',
            'Only approved for those with prior dengue infection',
            'Second infection is often more severe than the first',
        ],
        'symptoms' => [
            ['icon' => '🤒', 'name' => 'High Fever',      'desc' => 'Sudden fever reaching up to 40°C'],
            ['icon' => '😣', 'name' => 'Severe Headache', 'desc' => 'Intense pain behind the eyes'],
            ['icon' => '🔴', 'name' => 'Skin Rash',       'desc' => 'Red rash appearing 3–4 days after fever'],
            ['icon' => '🩸', 'name' => 'Bleeding',        'desc' => 'Nosebleeds or bleeding gums in severe cases'],
        ],
        'side_effects' => [
            ['icon' => '💪', 'name' => 'Injection Site',  'desc' => 'Pain or bruising at injection site'],
            ['icon' => '🤕', 'name' => 'Headache',        'desc' => 'Mild headache post-vaccination'],
            ['icon' => '😣', 'name' => 'Myalgia',         'desc' => 'Mild muscle pain lasting 1–2 days'],
            ['icon' => '😴', 'name' => 'Fatigue',         'desc' => 'General tiredness after injection'],
        ],
        'who_should_get' => [
            ['icon' => '🧪', 'name' => 'Seropositive Only', 'desc' => 'Must have prior dengue infection confirmed'],
            ['icon' => '🧑', 'name' => 'Ages 9–45',       'desc' => 'Approved age range for Dengvaxia'],
            ['icon' => '🌏', 'name' => 'Endemic Areas',   'desc' => 'Residents of dengue-prone regions'],
            ['icon' => '🏥', 'name' => 'Doctor Consult',  'desc' => 'Requires medical evaluation before vaccination'],
        ],
        'brands' => ['Dengvaxia'],
    ],
    'rabies' => [
        'title'       => 'Rabies',
        'subtitle'    => 'Anti-Rabies Vaccine',
        'description' => 'Rabies is a fatal viral disease that affects the nervous system, transmitted through the bite or scratch of an infected animal. Post-exposure vaccination is highly effective if given promptly.',
        'image'       => 'images/rabies.webp',
        'color'       => '#E67E22',
        'stats' => [
            ['label' => 'Doses Required',   'value' => '5',      'icon' => '💉'],
            ['label' => 'Effectiveness',    'value' => '100%',   'icon' => '🛡️'],
            ['label' => 'PEP Window',       'value' => 'ASAP',   'icon' => '📅'],
            ['label' => 'Age Requirement',  'value' => 'All',    'icon' => '👤'],
        ],
        'quick_facts' => [
            'Nearly 100% fatal once symptoms appear',
            'Post-exposure prophylaxis (PEP) must begin immediately',
            'Dogs are responsible for 99% of rabies transmission',
            'Pre-exposure vaccine available for high-risk individuals',
        ],
        'symptoms' => [
            ['icon' => '🧠', 'name' => 'Confusion',       'desc' => 'Disorientation and aggressive behavior'],
            ['icon' => '💧', 'name' => 'Hydrophobia',     'desc' => 'Fear of water, difficulty swallowing'],
            ['icon' => '⚡', 'name' => 'Paralysis',       'desc' => 'Progressive muscle weakness and paralysis'],
            ['icon' => '🤒', 'name' => 'Fever',           'desc' => 'Early flu-like symptoms with headache'],
        ],
        'side_effects' => [
            ['icon' => '💪', 'name' => 'Sore Arm',        'desc' => 'Pain and redness at injection site'],
            ['icon' => '🤕', 'name' => 'Headache',        'desc' => 'Mild headache after injection'],
            ['icon' => '🤢', 'name' => 'Nausea',          'desc' => 'Mild stomach upset, usually brief'],
            ['icon' => '😴', 'name' => 'Dizziness',       'desc' => 'Light-headedness that passes quickly'],
        ],
        'who_should_get' => [
            ['icon' => '🐕', 'name' => 'Animal Bite Victims', 'desc' => 'Anyone bitten or scratched by an animal'],
            ['icon' => '🏕️', 'name' => 'Travelers',      'desc' => 'Those traveling to rabies-endemic regions'],
            ['icon' => '🐾', 'name' => 'Veterinarians',   'desc' => 'Pre-exposure vaccine strongly recommended'],
            ['icon' => '👶', 'name' => 'All Ages',        'desc' => 'No age restriction, given when needed'],
        ],
        'brands' => ['Verorab', 'Rabipur'],
    ],
];

// Fallback to covid if invalid disease
if (!array_key_exists($disease, $diseases)) {
    $disease = 'covid';
}

$d = $diseases[$disease];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $d['title']; ?> - ImmuniTrack</title>
    <link rel="stylesheet" href="css/dashboard.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/information.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="images/favicon.png">
</head>
<body>

<!-- Disease Nav Pills -->
<div class="disease-nav">
    <div class="disease-nav-inner">
        <?php foreach ($diseases as $key => $val): ?>
        <a href="information.php?disease=<?php echo $key; ?>"
           class="disease-pill <?php echo $disease === $key ? 'active' : ''; ?>">
            <?php echo $val['title']; ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<main class="info-page">

    <!-- ========== HERO CARD ========== -->
    <div class="info-hero-card">

        <!-- Left: Image -->
        <div class="info-hero-image">
            <img src="<?php echo $d['image']; ?>" alt="<?php echo $d['title']; ?>">
            <div class="info-hero-image-overlay">
                <span class="info-disease-tag"><?php echo $d['subtitle']; ?></span>
            </div>
        </div>

        <!-- Right: Info -->
        <div class="info-hero-content">

            <div class="info-hero-top">
                <h1 class="info-title"><?php echo $d['title']; ?></h1>
                <p class="info-description"><?php echo $d['description']; ?></p>
            </div>

            <!-- Stats Row -->
            <div class="info-stats-row">
                <?php foreach ($d['stats'] as $stat): ?>
                <div class="info-stat-box">
                    <span class="info-stat-icon"><?php echo $stat['icon']; ?></span>
                    <span class="info-stat-value"><?php echo $stat['value']; ?></span>
                    <span class="info-stat-label"><?php echo $stat['label']; ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Quick Facts -->
            <div class="info-quick-facts">
                <h3 class="info-section-mini-title">Quick Facts</h3>
                <ul class="info-facts-list">
                    <?php foreach ($d['quick_facts'] as $fact): ?>
                    <li><?php echo $fact; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Brands -->
            <div class="info-brands">
                <h3 class="info-section-mini-title">Available Vaccines</h3>
                <div class="info-brand-pills">
                    <?php foreach ($d['brands'] as $brand): ?>
                    <span class="info-brand-pill"><?php echo $brand; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- ========== BOTTOM CARDS ========== -->
    <div class="info-bottom-grid">

        <!-- Symptoms Card -->
        <div class="info-detail-card symptoms-card">
            <div class="info-detail-card-header">
                <span class="info-detail-icon">🤒</span>
                <div>
                    <h2>Symptoms</h2>
                    <p>Signs to watch out for</p>
                </div>
            </div>
            <div class="info-icon-list">
                <?php foreach ($d['symptoms'] as $item): ?>
                <div class="info-icon-row">
                    <span class="info-row-icon"><?php echo $item['icon']; ?></span>
                    <div>
                        <strong><?php echo $item['name']; ?></strong>
                        <p><?php echo $item['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Side Effects Card -->
        <div class="info-detail-card sideeffects-card">
            <div class="info-detail-card-header">
                <span class="info-detail-icon">⚠️</span>
                <div>
                    <h2>Side Effects</h2>
                    <p>After vaccination</p>
                </div>
            </div>
            <div class="info-icon-list">
                <?php foreach ($d['side_effects'] as $item): ?>
                <div class="info-icon-row">
                    <span class="info-row-icon"><?php echo $item['icon']; ?></span>
                    <div>
                        <strong><?php echo $item['name']; ?></strong>
                        <p><?php echo $item['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Who Should Get It Card -->
        <div class="info-detail-card whoshould-card">
            <div class="info-detail-card-header">
                <span class="info-detail-icon">👥</span>
                <div>
                    <h2>Who Should Get It</h2>
                    <p>Recommended groups</p>
                </div>
            </div>
            <div class="info-icon-list">
                <?php foreach ($d['who_should_get'] as $item): ?>
                <div class="info-icon-row">
                    <span class="info-row-icon"><?php echo $item['icon']; ?></span>
                    <div>
                        <strong><?php echo $item['name']; ?></strong>
                        <p><?php echo $item['desc']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

</main>

<?php include 'includes/footer.php'; ?>

</body>
</html>