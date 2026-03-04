<?php
session_start();
?>
<html lang="en">
<head>
    <title>About Us - ExEC</title>
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

    <div class="about-content">
        <h1>About ExEC</h1>
        <div class="about-section">
            <h2>Our Mission</h2>
            <p>ExEC (Expression Evaluation and Conversion) aims to simplify complex mathematical expressions 
               by providing instant conversion between different notations and accurate evaluations with 
               variable substitution.</p>
        </div>

        <div class="about-section">
            <h2>Features</h2>
            <ul class="features-list">
                <li>Support for Prefix, Infix, and Postfix notations</li>
                <li>Real-time expression validation</li>
                <li>User history tracking</li>
                <li>Secure authentication system</li>
            </ul>
        </div>

        <div class="about-section">
            <h2>Development Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <h3>Sawan Mukhiya</h3>
                    <p>Lead Developer</p>
                </div>
                <div class="team-member">
                    <h3>Sawan Mukhiya</h3>
                    <p>UI/UX Designer</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        &copy; 2025 ExEC. All rights reserved.
    </footer>
</body>
</html>
