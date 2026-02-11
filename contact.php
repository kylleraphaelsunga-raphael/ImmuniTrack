<?php 
    $css = "footernav.css"; 
    include 'includes/header.php'; 
?>

<main class="contact-body">
    <div class="contact-header-text">
        <h1>Connect with <span class="highlight">ImmuniTrack</span></h1>
        <p>Our medical support team is available to assist you with your health records.</p>
    </div>

    <div class="contact-container">
        <section class="contact-form-section card">
            <h3>Send us a Message</h3>
            <form action="process_contact.php" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="John Doe" required>
                </div>
                
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="john@example.com" required>
                </div>

                <div class="form-group">
                    <label>Inquiry Type</label>
                    <select name="subject">
                        <option value="general">General Inquiry</option>
                        <option value="technical">Technical Support</option>
                        <option value="billing">Data Correction</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" placeholder="How can we help?"></textarea>
                </div>

                <button type="submit" class="btn-send">Send Message</button>
            </form>
        </section>

        <aside class="contact-info-column">
            <div class="info-card card">
                <h3>Contact Details</h3>
                <p>Email: <a href="mailto:support@immunitrack.com">support@immunitrack.com</a></p>
                <p>Phone: <strong>+63 (045) 123 4567</strong></p>
                <p>Mobile: <strong>+63 917 123 4567</strong></p>
            </div>

            <div class="info-card card">
                <h3>Follow Our Updates</h3>
                <p>Stay updated on our social media platforms:</p>
                <div class="social-links">
                    <a href="#" class="social-tag">Facebook</a>
                    <a href="#" class="social-tag">Instagram</a>
                    <a href="#" class="social-tag">Twitter</a>
                    <a href="#" class="social-tag">Tiktok</a>
                </div>
            </div>

            <div class="info-card card">
                <h3>ADD PA ISA DITO</h3>
                <p>1</p>
                <p>1</p>
            </div>
        </aside>
    </div>
</main>

<?php include 'includes/footer.php'; ?>