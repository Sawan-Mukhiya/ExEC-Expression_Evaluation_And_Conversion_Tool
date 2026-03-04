<?php 
require_once '../backend/Session.php';
?>
<html>
<head>
    <title>Expression Conversion</title>
    <link rel="stylesheet" href="assets/css/Expression.css">
    <script src="assets/js/DisplayCondition.js" defer></script>
    <script src="assets/js/ExpressionValidation.js" defer></script>
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
    <h1 class="heading">Expression Conversion</h1>
    <form id="conversionForm">
        <div id="d2">
            <label>Choose Conversion type:</label>
            <select name="source_type" id="select" onchange="fromTo()">
                <option value="">-----select-----</option>
                <option value="prefix">From Prefix</option>
                <option value="infix">From Infix</option>
                <option value="postfix">From Postfix</option>
            </select><br>

            <input type="text" id="convert" name="expression" placeholder="Enter your Expression"><br>

            <p id="ConvError"></p>
            <p id="ConvResult"></p>

            <div id="containButton">
                <div id="unhide1" class="hidden">
                    <button type="button" data-target="infix">To Infix</button>
                    <button type="button" data-target="postfix">To Postfix</button>
                </div>
                <div id="unhide2" class="hidden">
                    <button type="button" data-target="prefix">To Prefix</button>
                    <button type="button" data-target="postfix">To Postfix</button>
                </div>
                <div id="unhide3" class="hidden">
                    <button type="button" data-target="infix">To Infix</button>
                    <button type="button" data-target="prefix">To Prefix</button>
                </div>
                <button onclick="resetForm('convert')">Reset</button><br />
            </div>
        </div>
    </form>

    <div id="conversionHistory">
        <h3>Conversion History</h3>
        <div id="historyList"></div>
    </div>

    <script>
        document.querySelectorAll('#containButton button').forEach(button => {
            button.addEventListener('click', async function () {
                const errorElement = document.getElementById('ConvError');
                const resultElement = document.getElementById('ConvResult');

                // Reset UI states
                errorElement.textContent = '';
                resultElement.textContent = '';
                document.getElementById('convert').classList.remove('error-border');

                // Get input values
                const expression = document.getElementById('convert').value.trim();
                const sourceType = document.getElementById('select').value;
                const targetType = this.dataset.target;

                // Validate input
                if (!sourceType || !targetType) {
                    showValidationError('Please select valid conversion types');
                    return;
                }

                if (!validateExpression(expression, sourceType, 'convert')) {
                    return;
                }

                try {
                    const response = await fetch('../backend/ExpressionConversion.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            expression: expression.trim(),
                            source_type: sourceType,
                            target_type: targetType
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.error || 'Conversion failed');
                    }

                    resultElement.innerHTML = `<span class="result-label">${targetType.toUpperCase()} Result</span><div class="result-container">${data.result}</div>`;
                    resultElement.classList.remove('error');
                    await loadConversionHistory();
                } catch (error) {
                    showValidationError(error.message);
                    resultElement.textContent = '';
                    console.error('Conversion error:', error);
                }
            });
        });

        async function loadConversionHistory() {
            try {
                const response = await fetch('../backend/GetConversionHistory.php');
                const history = await response.json();
                
                const historyList = document.getElementById('historyList');
                historyList.innerHTML = history.map(entry => `
                    <div class="history-item">
                        <strong>${entry.source_type} → ${entry.target_type}</strong><br>
                        Original: ${entry.original_expression}<br>
                        Converted: ${entry.converted_expression}<br>
                        <small>${new Date(entry.conversion_timestamp).toLocaleString()}</small>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Call on page load
        window.addEventListener('DOMContentLoaded', loadConversionHistory);
    </script>
</body>

</html>
