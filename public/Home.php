<?php
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expression Evaluation and Conversion</title>
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
                <button class="signup" onclick="redirectToSignUp()">Sign-up</button>
            <?php endif; ?>
        </div>
    </nav>
    <header>
        <div class="header-content">
            <h1><span class="highlight">ExEC</span> - Your Expression Evaluation & Conversion Tool</h1>
            <p>Evaluate and convert mathematical expressions quickly and efficiently.</p>
            <div class="cta-container">
                <a href="Conversion.php" class="cta-button">Convert</a>
                <a href="Evaluation.php" class="cta-button">Evaluate</a>
            </div>
        </div>
        <img src="assets/images/illustration.jpg" alt="Illustration">
    </header>

    <div class="content-section">
        <h2>Understanding Expression Notations</h2>
        
        <div class="notation-cards">
            <!-- Infix Card -->
            <div class="notation-card">
                <h3>Infix Notation</h3>
                <div class="notation-example">
                    <code>a + b * c</code>
                </div>
                <ul class="notation-rules">
                    <li>Operators between operands</li>
                    <li>Requires parentheses for precedence</li>
                    <li>Most human-readable format</li>
                    <li>Valid example: <code>(a + b) * c</code></li>
                    <li>Invalid example: <code>a + b *</code></li>
                </ul>
            </div>

            <!-- Prefix Card -->
            <div class="notation-card">
                <h3>Prefix (Polish) Notation</h3>
                <div class="notation-example">
                    <code>* + a b c</code>
                </div>
                <ul class="notation-rules">
                    <li>Operator precedes operands</li>
                    <li>No parentheses needed</li>
                    <li>Used in Lisp-like languages</li>
                    <li>Valid example: <code>+ * a b c</code></li>
                    <li>Invalid example: <code>* a + b</code></li>
                </ul>
            </div>

            <!-- Postfix Card -->
            <div class="notation-card">
                <h3>Postfix (Reverse Polish) Notation</h3>
                <div class="notation-example">
                    <code>a b + c *</code>
                </div>
                <ul class="notation-rules">
                    <li>Operator follows operands</li>
                    <li>No parentheses needed</li>
                    <li>Used in stack-based calculators</li>
                    <li>Valid example: <code>a b c + *</code></li>
                    <li>Invalid example: <code>a + b c</code></li>
                </ul>
            </div>
        </div>

        <div class="validation-rules">
            <h3>General Validation Rules</h3>
            <ul>
                <li>Operands must be alphanumeric (e.g., a1, 25, var)</li>
                <li>Valid operators: +, -, *, /, ^</li>
                <li>No consecutive operators</li>
                <li>Proper operand-operator ratio</li>
                <li>Balanced parentheses in infix</li>
            </ul>
        </div>
    </div>

    <footer>
        &copy; 2025 ExEC. All rights reserved.
    </footer>

    <script>
        function redirectToLogin() {
            window.location.href = '../auth/Login.php';
        }

        function redirectToSignUp() {
            window.location.href = '../auth/SignUp.php';
        }
    </script>
</body>
</html>
