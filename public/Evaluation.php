<?php 
require_once '../backend/Session.php';
?>
<html>
<head>
    <title>Expression Evaluation</title>
    <link rel="stylesheet" href="assets/css/Expression.css">
    <script src="assets/js/ExpressionValidation.js" defer></script>
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
    <h1 class="heading">Expression Evaluation</h1>
    <div id="d1">
        <label>Choose Evaluation type:</label>
        <select style="display:inline-block" name="conversion" id="select">
            <option value="#">-----select-----</option>
            <option value="prefix">Prefix Expression</option>
            <option value="infix">Infix Expression</option>
            <option value="postfix">Postfix Expression</option>
        </select><br />

        <input type="text" id="evaluate" placeholder="Enter your Expression (e.g., a-d*c)">
        <button onclick="extractVariables()">Next</button>
        <button onclick="resetForm('evaluate')">Reset</button><br />
        <p id="EvalError" style="color: red;"></p>

        <!-- Container for variable inputs -->
        <div id="variableInputs" style="display: none;">
            <h3>Enter values for variables:</h3>
            <div id="inputs"></div>
            <button onclick="evaluateExpression()">Evaluate</button>
        </div>

        <!-- Result display -->
        <p id="result"></p>
    </div>

    <div id="evaluationHistory">
        <h3>Evaluation History</h3>
        <div id="evalHistoryList"></div>
    </div>
    <script>
        async function evaluateExpression() {
            const errorElement = document.getElementById('EvalError');
            const resultElement = document.getElementById('result');
            errorElement.textContent = '';
            resultElement.textContent = '';

            try {
                // Validate variables
                const variables = {};
                const inputs = document.querySelectorAll('#inputs input');
                let isValid = true;

                inputs.forEach(input => {
                    if (!input.value) {
                        isValid = false;
                        input.classList.add('error-border');
                    } else {
                        input.classList.remove('error-border');
                    }
                    variables[input.previousElementSibling.textContent.replace(':', '')] = input.value;
                });

                if (!isValid) {
                    throw new Error('Please fill all variable values');
                }

                const expression = document.getElementById('evaluate').value.trim();
                const type = document.getElementById('select').value;

                if (!validateExpression(expression, type, 'evaluate')) {
                    return;
                }

                const response = await fetch('../backend/ExpressionEvaluation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'evaluate',
                        expression: expression,
                        type: type,
                        variables: variables
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Evaluation failed');
                }

                resultElement.textContent = `Result: ${data.result}`;
                resultElement.style.color = '#27ae60';
                await loadEvaluationHistory();

            } catch (error) {
                errorElement.textContent = error.message;
                errorElement.style.color = '#e74c3c';
                console.error('Evaluation error:', error);
            }
        }

        async function loadEvaluationHistory() {
            try {
                const response = await fetch('../backend/GetEvaluationHistory.php');
                const history = await response.json();
                
                const historyList = document.getElementById('evalHistoryList');
                historyList.innerHTML = history.map(entry => `
                    <div class="history-item">
                        <strong>${entry.expression_type}</strong><br>
                        Expression: ${entry.expression}<br>
                        Variables: ${JSON.stringify(JSON.parse(entry.variables))}<br>
                        Result: ${entry.result}<br>
                        <small>${new Date(entry.evaluation_timestamp).toLocaleString()}</small>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // Call on page load
        window.addEventListener('DOMContentLoaded', loadEvaluationHistory);
    </script>
</body>

</html>
