<?php
$css = "dashboard.css";
include 'includes/header.php';
?>

<main class="dashboard-body">
    <div class="parent">
       <div class="div1 card">
            <a href="information.php?disease=covid">
                <img src="images/covid.png" alt="covid" class="card-img">
                <div class="card-text">
                    <h3>COVID-19 VACCINES</h3>
                </div>
            </a>
        </div>

        <div class="div2 card">
            <a href="information.php?disease=flu">
                <img src="images/influenza.webp" alt="flu shots" class="card-img">
                <div class="card-text">
                    <h3>INFLUENZA VACCINES</h3>
                </div>
            </a>
        </div>

        <div class="div3 card">
            <a href="information.php?disease=chickenpox">
                <img src="images/chickenpox.webp" alt="chickenpox" class="card-img">
                <div class="card-text">
                    <h3>CHICKENPOX VACCINES</h3>
                </div>
            </a>
        </div>

        <div class="div4 card">
            <a href="information.php?disease=dengue">
                <img src="images/dengue.jpg" alt="dengue" class="card-img">
                <div class="card-text">
                    <h3>DENGUE VACCINES</h3>
                </div>
            </a>
        </div>

        <div class="div5 card">
            <a href="information.php?disease=rabies">
                <img src="images/rabies.webp" alt="rabies" class="card-img">
                <div class="card-text">
                    <h3>ANTI-RABIES VACCINES</h3>
                </div>
            </a>
        </div>

        <div class="div6 card news-card">
            <div class="news-header">
                <span class="badge">Health Education</span>
                <h2>Public Health & Immunization</h2>
            </div>

            <div class="news-content">
                <section>
                    <h3>🛡 Why Vaccination Matters</h3>
                    <p>Vaccination is one of the most effective ways to prevent the spread of infectious diseases such as COVID-19, Influenza, Dengue, and Rabies. Vaccines train your immune system to recognize harmful pathogens without causing the illness itself.</p>
                </section>

                <section>
                    <h3>👶 Protecting the Vulnerable</h3>
                    <p>When a community reaches high vaccination levels, it creates <strong>Herd Immunity</strong>. This shields those who cannot be vaccinated, such as newborns, the elderly, and individuals with specific medical conditions.</p>
                </section>

                <section>
                    <h3>🌍 Global Safety</h3>
                    <p>Consistent vaccination coverage reduces the frequency of outbreaks, keeping schools, workplaces, and public spaces safer for everyone while reducing the burden on healthcare systems.</p>
                </section>

                <section>
                    <h3>📅 Staying Up-to-Date</h3>
                    <p>Some vaccines require boosters or follow-up doses. Staying current with your immunization schedule ensures your body maintains a high level of defense over time.</p>
                </section>
            </div>
        </div>

        <div class="div7 card side-info">
            <div class="side-content">
                <h3>🌿 Healthy Living</h3>
                <p>Maintaining good health starts with simple daily habits: proper hygiene, balanced nutrition, and regular exercise. Small consistent actions improve overall well-being.</p>
            </div>
        </div>

        <div class="div8 card side-info">
            <div class="side-content">
                <h3>💉 Quick Health Fact</h3>
                <p>Most vaccines require multiple doses to provide full protection. Always follow the recommended immunization schedule from your local health center for best results.</p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

