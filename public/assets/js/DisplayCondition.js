function redirectToLogin() { window.location.href = '../auth/Login.php'; }
function redirectToSignUp() { window.location.href = '../auth/SignUp.php'; }

function fromTo() {
    const buttons = document.getElementById('containButton');
    if (buttons != null) buttons.style.display = "flex";
    buttons.style.justifyContent = "space-evenly";

    document.getElementById('ConvError').innerText = '';
    document.getElementById('ConvResult').innerText = '';

    document.getElementById("unhide1").style.display = "none";
    document.getElementById("unhide2").style.display = "none";
    document.getElementById("unhide3").style.display = "none";

    const conversionType = document.getElementById("select").value;

    if (conversionType === "prefix") {
        document.getElementById("unhide1").style.display = "flex";
    } else if (conversionType === "infix") {
        document.getElementById("unhide2").style.display = "flex";
    } else if (conversionType === "postfix") {
        document.getElementById("unhide3").style.display = "flex";
    }
}

// Extract variables from the expression
function extractVariables() {
    const expression = document.getElementById('evaluate').value.trim();
    const type = document.getElementById('select').value;

    
    const variables = [...new Set(expression.match(/[a-zA-Z]+/g))] || [];

    if (variables.length === 0) {
        document.getElementById('EvalError').textContent = "No variables found";
        return;
    }
    
    const inputsDiv = document.getElementById('inputs');
    inputsDiv.innerHTML = variables.map(variable => `
        <div class="input-group">
            <label for="${variable}">${variable}:</label>
            <input type="number" step="any" id="${variable}" required>
        </div>
    `).join('');

    document.getElementById('variableInputs').style.display = 'block';
}

function resetForm(type) {
    if(type == 'evaluate'){
        // Clear the expression input
        document.getElementById('evaluate').value = '';

        // Clear the variable inputs
        document.getElementById('inputs').innerHTML = '';

        // Hide the variable input section
        document.getElementById('variableInputs').style.display = 'none';

        // Clear the result and error messages
        document.getElementById('result').textContent = '';
        document.getElementById('EvalError').textContent = '';

        // Reset the dropdown to default
        document.getElementById('select').selectedIndex = 0;
    }else{
        // Clear the expression input
        document.getElementById('convert').value = '';

        // Clear the result and error messages
        document.getElementById('ConvResult').textContent = '';
        document.getElementById('ConvError').textContent = '';

        // Reset the dropdown to default
        document.getElementById('select').selectedIndex = 0;
    }
}
