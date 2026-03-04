<?php
session_start();
?>
<html lang="en">
<head>
    <title>Contact Us - ExEC</title>
    <link rel="stylesheet" href="assets/css/Expression.css">
    <script src="assets/js/DisplayCondition.js" defer></script>
</head>
<body>
    <nav>
        <a href="Home.php" style="text-decoration: none; color: white">
            <div class="logo">
                <img src="assets/images/ExEC_Logo.png" alt="Logo">
                <span>ExEC</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="Home.php">Home</a></li>
            <li><a href="Evaluation.php">Evaluator</a></li>
            <li><a href="Conversion.php">Converter</a></li>
            <li><a href="About.php">About</a></li>
            <li><a href="Contact.php">Contact</a></li>
        </ul>
        <div class="auth-buttons">
            <?php if(isset($_SESSION['username'])): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="../auth/LogOut.php" class="logout">Logout</a>
            <?php else: ?>
                <button class="login" onclick="redirectToLogin()">Login</button>
                <button class="signup"onclick="redirectToSignUp()">Sign-up</button>
            <?php endif; ?>
        </div>
    </nav>

    <div class="contact-container">
        <h1>Contact Us</h1>
        <form id="contactForm">
            <div class="form-group">
                <label>Name:</label>
                <input type="text" id="name" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label>Subject:</label>
                <input type="text" id="subject" required>
            </div>
            <div class="form-group">
                <label>Message:</label>
                <textarea id="message" rows="5" required></textarea>
            </div>
            <button type="submit">Send Message</button>
            <p id="contactError" style="color: red;"></p>
        </form>
    </div>

    <script>
        document.getElementById('contactForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const errorElement = document.getElementById('contactError');
            
            // Get form values
            const formData = {
                name: document.getElementById('name').value.trim(),
                email: document.getElementById('email').value.trim(),
                subject: document.getElementById('subject').value.trim(),
                message: document.getElementById('message').value.trim()
            };

            // Simple validation
            if (!formData.name || !formData.email || !formData.subject || !formData.message) {
                errorElement.textContent = 'Please fill in all fields';
                return;
            }

            // Basic email validation
            if (!/^\S+@\S+\.\S+$/.test(formData.email)) {
                errorElement.textContent = 'Please enter a valid email address';
                return;
            }

            // Simulate form submission
            errorElement.textContent = 'Message sent successfully!';
            document.getElementById('contactForm').reset();
            setTimeout(() => errorElement.textContent = '', 3000);
        });
    </script>

    <footer>
        &copy; 2025 ExEC. All rights reserved.
    </footer>
</body>
</html>
