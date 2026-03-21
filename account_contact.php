<?php 
    $css = "footernav.css"; 
    include 'includes/account_header.php'; 
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
                    <label>How can we help you?</label>
                    <select name="subject" required>
                        <option value="">Select a topic...</option>
                        <option value="record_issue">How to Sign In</option>
                        <option value="data_update">How can we register for the vaccination</option>
                        <option value="schedule">What are the requirements for an account</option>
                        <option value="other">Other concerns</option>
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
                    <a href="https://www.facebook.com/christructure" target="_blank" class="social-tag">Facebook</a>
                    <a href="https://www.instagram.com/christructure"  target="_blank" class="social-tag">Instagram</a>
                    <a href="https://github.com/kylleraphaelsunga-raphael/ImmuniTrack"  target="_blank" class="social-tag">Github</a>
                </div>
            </div>
        </aside>
    </div>
</main>

<?php include 'includes/account_footer.php'; ?>