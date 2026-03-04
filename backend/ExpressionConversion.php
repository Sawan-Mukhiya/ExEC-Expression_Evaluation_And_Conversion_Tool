<?php
header('Content-Type: application/json');
require_once 'Database.php';

class ExpressionConverter {
    private static $db;
    public static function handleRequest($data) {
        try {
            self::$db = new Database();
            $conn = self::$db->connect();

            if (!isset($data['expression'], $data['source_type'], $data['target_type'])) {
                throw new Exception('Missing required parameters');
            }

            $expression = preg_replace('/\s+/', '', $data['expression']);
            $sourceType = $data['source_type'];
            $targetType = $data['target_type'];

            $method = "{$sourceType}To{$targetType}";
            if (!method_exists(__CLASS__, $method)) {
                throw new Exception("Unsupported conversion: $sourceType to $targetType");
            }

            $result = self::$method($expression);
            
            session_start();
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }
            self::$db->logConversion($_SESSION['user_id'], $sourceType, $targetType, $expression, $result);

            return ['result' => $result];
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private static function precedence($c) {
        switch ($c) {
            case '+':
            case '-': return 1;
            case '*':
            case '/': return 2;
            case '^':
            case '$': return 3;
            default: return 0;
        }
    }

    // Infix to Prefix
    public static function infixToPrefix($exp) {
        // Step 1: Reverse the infix expression
        $exp = str_replace(' ', '', $exp); // Remove any spaces
        $reversed = '';
        
        // Properly reverse and swap parentheses
        for ($i = strlen($exp) - 1; $i >= 0; $i--) {
            if ($exp[$i] === '(') {
                $reversed .= ')';
            } elseif ($exp[$i] === ')') {
                $reversed .= '(';
            } else {
                $reversed .= $exp[$i];
            }
        }
    
        // Step 2: Tokenize with support for multi-character variables/numbers
        preg_match_all('/[a-zA-Z]+|[+\-*\/\^$()]/', $reversed, $matches);
        $tokens = $matches[0];
    
        $stack = [];
        $output = [];
    
        // Step 3: Convert reversed infix to postfix using the shunting yard algorithm
        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]+$/', $token)) {
                // Operand - add directly to output
                $output[] = $token;
            } elseif ($token === '(') {
                // Opening parenthesis - push to stack
                $stack[] = $token;
            } elseif ($token === ')') {
                // Closing parenthesis - pop from stack until matching opening parenthesis
                while (!empty($stack) && end($stack) !== '(') {
                    $output[] = array_pop($stack);
                }
                if (!empty($stack) && end($stack) === '(') {
                    array_pop($stack); // Remove the opening parenthesis
                }
            } else {
                // Operator
                while (!empty($stack) && 
                       end($stack) !== '(' && 
                       self::precedence(end($stack)) >= self::precedence($token)) {
                    $output[] = array_pop($stack);
                }
                $stack[] = $token;
            }
        }
    
        // Pop remaining operators from stack to output
        while (!empty($stack)) {
            $output[] = array_pop($stack);
        }
    
        // Step 4: Reverse the postfix result to get prefix
        $output = array_reverse($output);
        
        return implode(' ', $output);
    }

    // Infix to Postfix
    public static function infixToPostfix($exp) {
        $output = [];
        $stack = [];
        preg_match_all('/[a-zA-Z]|[+\-*\/^()]/', $exp, $matches);
        $tokens = $matches[0];
    
        foreach ($tokens as $token) {
            if (preg_match('/^[a-zA-Z]+$/', $token)) {
                $output[] = $token;
            } elseif ($token === '(') {
                array_push($stack, $token);
            } elseif ($token === ')') {
                while (end($stack) !== '(') {
                    $output[] = array_pop($stack);
                    
                    if (empty($stack)) throw new Exception("Mismatched parentheses");
                }
                array_pop($stack);
            } else {
                while (!empty($stack) && self::precedence(end($stack)) >= self::precedence($token)) {
                    $output[] = array_pop($stack);
                }
                array_push($stack, $token);
            }
        }
    
        while (!empty($stack)) {
            $output[] = array_pop($stack);
        }
    
        return implode(' ', $output);
    }

    

    // Prefix to Infix
    public static function prefixToInfix($exp) {
        // Tokenize the prefix expression 
        preg_match_all('/[a-zA-Z]|[+\-*\/^()]/', $exp, $matches);
        $tokens = $matches[0];
    
        $stack = [];
    
        for ($i = count($tokens) - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (preg_match('/^[a-zA-Z]+$/', $token)) {
                array_push($stack, $token);
            } else {
                if (count($stack) < 2) {
                    throw new Exception("Invalid prefix expression");
                }
                $operand1 = array_pop($stack);
                $operand2 = array_pop($stack);
                array_push($stack, "($operand1 $token $operand2)");
            }
        }
    
        return array_pop($stack);
    }
    
    

    // Prefix to Postfix
    public static function prefixToPostfix($exp) {
        // Tokenize without reversing
        preg_match_all('/[a-zA-Z]|[+\-*\/^()]/', $exp, $matches);
        $tokens = $matches[0]; 
        $stack = [];
    
        // Process tokens from RIGHT TO LEFT (end to start)
        for ($i = count($tokens) - 1; $i >= 0; $i--) {
            $token = $tokens[$i];
            if (preg_match('/^[a-zA-Z]+$/', $token)) {
                array_push($stack, $token);
            } else {
                if (count($stack) < 2) {
                    throw new Exception("Invalid prefix expression");
                }
                $operand1 = array_pop($stack);
                $operand2 = array_pop($stack);
                // Combine operands with operator at the end
                array_push($stack, "$operand1 $operand2 $token");
            }
        }
    
        return implode(' ', $stack);
    }

    // Postfix to Infix
    public static function postfixToInfix($exp) {
        preg_match_all('/[a-zA-Z]|[+\-*\/^()]/', $exp, $matches);
        $stack = [];
    
        foreach ($matches[0] as $token) {
            if (preg_match('/\s/', $token)) continue;
            else if (preg_match('/^[a-zA-Z]+$/', $token)) {
                array_push($stack, $token);
            } else {
                if (count($stack) < 2) throw new Exception("Invalid postfix expression");
                $b = array_pop($stack);
                $a = array_pop($stack);
                array_push($stack, "($a $token $b)");
            }
        }
        return array_pop($stack);
    }

    public static function postfixToPrefix($exp) {
        preg_match_all('/[a-zA-Z]|[+\-*\/^()]/', $exp, $matches);
        $stack = [];
    
        foreach ($matches[0] as $token) {
            if (preg_match('/\s/', $token)) continue;
            else if (preg_match('/^[a-zA-Z]+$/', $token)) {
                array_push($stack, $token);
            } else {
                if (count($stack) < 2) throw new Exception("Invalid postfix expression");
                $b = array_pop($stack);
                $a = array_pop($stack);
                array_push($stack, "$token $a $b");
            }
        }
        return array_pop($stack);
    }
}

// Handle request
try {
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(ExpressionConverter::handleRequest($data));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
exit;
?>
