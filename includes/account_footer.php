<footer class="main-footer">
    <div class="footer-left">
        <p class="quote">"Stay Informed. Stay Healthy. Stay Ahead."</p>
    </div>
    <div class="footer-right">
        <nav class="footer-nav">
            <a href="contact.php">Contact</a>
            <a href="about.php">About Us</a>
            <a href="#">Privacy</a>
        </nav>
        <p class="copyright">&copy; <?php echo date("Y"); ?> ImmuniTrack - WD203</p>
    </div>
</footer>

<script>
const hamburger = document.getElementById("hamburger");
const mobileMenu = document.getElementById("mobileMenu");

hamburger.addEventListener("click", () => {
    mobileMenu.classList.toggle("show");
});
</script>


</body>
</html>
