<?php
session_start();
?>
<html lang="en">
<head>
    <title>ExEC-Login</title>
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
        <h1>Login</h1>
        <form id="loginForm">
            <div class="form-group">
                <label>Username or Email:</label>
                <input type="text" id="loginIdentifier" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" id="loginPassword" required>
            </div>
            <button type="submit">Login</button>
            <p id="loginError" style="color: red;"></p>
            <p>Don't have an account? <a href="SignUp.php">Sign Up</a></p>
        </form>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const identifier = document.getElementById('loginIdentifier').value.trim();
            const password = document.getElementById('loginPassword').value.trim();
            const errorElement = document.getElementById('loginError');

            // Frontend validation
            if (!identifier || !password) {
                errorElement.textContent = 'Please fill in all fields';
                return;
            }

            try {
                const response = await fetch('LoginBackend.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ identifier, password })
                });

                const data = await response.json();
                
                if (!response.ok) throw new Error(data.error || 'Login failed');
                
                window.location.href = '../public/Home.php';
            } catch (error) {
                errorElement.textContent = error.message;
            }
        });
    </script>
</body>
</html>
