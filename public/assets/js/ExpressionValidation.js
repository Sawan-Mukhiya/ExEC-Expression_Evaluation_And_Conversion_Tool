// Utility function to check if a token is an operand (letters or digits)
function isOperand(token) {
    return /^[A-Za-z0-9]+$/.test(token);
}

// Utility function to check if a character is an operator
function isOperator(char) {
    return /^[+\-*/^$]$/.test(char);
}

function isWhiteSpace(char){
    return /\s/.test(char);
}

// Function to validate infix expressions
function isValidInfix(expression) {
    const stack = [];
    let lastChar = null;
    let operandAfterOpen = false; // Tracks operands after '('

    for (let i = 0; i < expression.length; i++) {
        const char = expression[i];

        if (isWhiteSpace(char)){
            continue;
        }else if (char === '(') {
            if (lastChar !== null && (isOperand(lastChar) || lastChar === ')')) {
                // Detect missing operator before '('
                return false;
            }
            stack.push(char);
            operandAfterOpen = false; // Reset for the new '('
        } else if (char === ')') {
            if (stack.length === 0 || stack.pop() !== '(') {
                // Unmatched parenthesis
                return false;
            }
            if (!operandAfterOpen) {
                // No valid sub-expression inside parentheses
                return false;
            }
        } else if (isOperator(char)) {
            // Operators should not be the first character, follow another operator, or follow '('
            if (!lastChar || isOperator(lastChar) || lastChar === '(') {
                return false;
            }
        } else if (isOperand(char)) {
            // Operand found
            operandAfterOpen = true;

            // Check if an operand follows a closing parenthesis without an operator
            if (lastChar === ')') {
                return false; // Operand directly after ')'
            }
        } else {
            // Invalid character detected
            return false;
        }

        lastChar = char;
    }

    // Expression must not end with an operator, and parentheses must balance
    if (isOperator(lastChar) || stack.length > 0) {
        return false;
    }

    return true;
}

// Function to validate prefix expressions
function isValidPrefix(expression) {
    const tokens = expression.match(/[a-zA-Z]|[\+\-\*\/\^]/g) || [];
    let operandCount = 0;

    // Process tokens in reverse order
    for (let i = tokens.length - 1; i >= 0; i--) {
        const token = tokens[i];
        
        if (isWhiteSpace(token)) continue;
        else if (isOperator(token)) {
            if (operandCount < 2) {
                return false;
            }
            operandCount--;
        } else if (isOperand(token)) {
            operandCount++;
        } else {
            return false; // Invalid token
        }
    }

    return operandCount === 1;
}


// Function to validate postfix expressions
function isValidPostfix(expression) {
    const tokens = expression.match(/[a-zA-Z0-9]+|[\+\-\*\/\^]/g) || [];
    let operandCount = 0;

    for (const token of tokens) {
        if (isWhiteSpace(token)) continue;
        else if (isOperator(token)) {
            if (operandCount < 2) {
                return false;
            }
            operandCount--;
        } else if (isOperand(token)) {
            operandCount++;
        } else {
            return false; // Invalid token
        }
    }

    return operandCount === 1;
}

// From ExpressionValidation.js
function validateExpression(expression, type, elementId) {
    // Remove existing error highlights
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.remove('error-border');
    } else {
        console.warn(`Element with id '${elementId}' not found.`);
    }
    
    // Trim and clean expression
    const cleanedExp = expression.trim();
    
    // General validation
    if(cleanedExp.length === 0) {
        showValidationError("Expression cannot be empty");
        return false;
    }
    
    // Type-specific validation
    let isValid = false;
    switch(type) {
        case 'infix':
            console.log(type);
            isValid = isValidInfix(cleanedExp);
            break;
        case 'prefix':
            console.log(type);
            isValid = isValidPrefix(cleanedExp);
            break;
        case 'postfix':
            console.log(type);
            isValid = isValidPostfix(cleanedExp);
            break;
        default:
            showValidationError("Invalid expression type");
            return false;
    }
    
    if(!isValid) {
        showValidationError("Invalid expression structure");
        document.getElementById(elementId).classList.add('error-border');
    }
    
    return isValid;
}

function showValidationError(message) {
    const errorElement = document.getElementById('ConvError') || document.getElementById('EvalError') ;
    errorElement.textContent = message;
    errorElement.style.color = 'red';
    setTimeout(() => errorElement.textContent = '', 5000);
}
