<?php
session_start();
?>
<html>
<head>
    <title>ExEC-Sign Up</title>
    <link rel="stylesheet" href="../public/assets/css/Expression.css">
    <script src="../public/assets/js/DisplayCondition.js" defer></script>
</head>
<body>
    <nav>
        <a href="../public/Home.php" style="text-decoration: none; color: white">
            <div class="logo">
                <img src="../public/assets/images/ExEC_Logo.png" alt="Logo">
                <span>ExEC</span>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="../public/Home.php">Home</a></li>
            <li><a href="../public/Evaluation.php">Evaluator</a></li>
            <li><a href="../public/Conversion.php">Converter</a></li>
            <li><a href="../public/About.php">About</a></li>
            <li><a href="../public/Contact.php">Contact</a></li>
        </ul>
        <div class="auth-buttons">
            <?php if(isset($_SESSION['username'])): ?>
                <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></span>
                <a href="LogOut.php" class="logout">Logout</a>
            <?php else: ?>
                <button class="login" onclick="redirectToLogin()">Login</button>
                <button class="signup"onclick="redirectToSignUp()">Sign-up</button>
            <?php endif; ?>
        </div>
    </nav>
    <div class="auth-container">
        <h1>Sign Up</h1>
        <form id="signupForm">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" id="username" required minlength="3">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" id="email" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" id="password" required minlength="8">
            </div>
            <div class="form-group">
                <label>Confirm Password:</label>
                <input type="password" id="confirmPassword" required>
            </div>
            <button type="submit">Sign Up</button>
            <p id="signupError" style="color: red;"></p>
            <p>Already have an account? <a href="Login.php">Login</a></p>
        </form>
    </div>

    <script>
        document.getElementById('signupForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('confirmPassword').value.trim();
            const errorElement = document.getElementById('signupError');

            // Frontend validation
            if (!username || !email || !password || !confirmPassword) {
                errorElement.textContent = 'Please fill in all fields';
                return;
            }

            if (password !== confirmPassword) {
                errorElement.textContent = 'Passwords do not match';
                return;
            }

            if (password.length < 8) {
                errorElement.textContent = 'Password must be at least 8 characters';
                return;
            }

            try {
                const response = await fetch('SignUpBackend.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ username, email, password })
                });

                const data = await response.json();
                
                if (!response.ok) throw new Error(data.error || 'Signup failed');
                
                window.location.href = 'Login.php';
            } catch (error) {
                errorElement.textContent = error.message;
            }
        });
    </script>
</body>
</html>
