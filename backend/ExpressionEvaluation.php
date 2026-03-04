<?php
header('Content-Type: application/json');
require_once 'Database.php';

class ExpressionEvaluator {
    private static $variables = [];
    private static $db;

    public static function evaluate($data) {
        try {
            self::$db = new Database();
            $conn = self::$db->connect();

            if (!isset($data['expression'], $data['type'], $data['variables'])) {
                throw new Exception('Missing required parameters');
            }

            $expression = preg_replace('/\s+/', '', $data['expression']);
            self::$variables = $data['variables'];

            $result = match($data['type']) {
                'infix' => self::evaluateInfix($expression),
                'prefix' => self::evaluatePrefix($expression),
                'postfix' => self::evaluatePostfix($expression),
                default => throw new Exception('Invalid evaluation type')
            };

            session_start();
            if (!isset($_SESSION['user_id'])) {
                throw new Exception('User not logged in');
            }
            self::$db->logEvaluation($_SESSION['user_id'], $data['type'], $expression, $data['variables'], $result);


            return ['result' => $result];
        } catch (Exception $e) {
            http_response_code(400);
            return ['error' => $e->getMessage()];
        }
    }

    private static function evaluateInfix($exp) {
        $postfix = self::infixToPostfix($exp);
        return self::evaluatePostfix($postfix);
    }

    private static function infixToPostfix($exp) {
        $output = [];
        $stack = [];
        $tokens = self::tokenizeExpression($exp, true);

        foreach ($tokens as $token) {
            if (preg_match('/\s/', $token)) continue;
            else if (is_numeric($token) || self::isVariable($token)) {
                $output[] = $token;
            } elseif ($token === '(') {
                array_push($stack, $token);
            } elseif ($token === ')') {
                while (end($stack) !== '(') {
                    $output[] = array_pop($stack);
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

    private static function evaluatePostfix($exp) {
        $stack = [];
        $tokens = self::tokenizeExpression($exp);

        foreach ($tokens as $token) {
            if (preg_match('/\s/', $token)) continue;
            else if (is_numeric($token)) {
                array_push($stack, $token);
            } elseif (isset(self::$variables[$token])) {
                array_push($stack, self::$variables[$token]);
            } elseif (self::isOperator($token)) {
                $b = array_pop($stack);
                $a = array_pop($stack);
                array_push($stack, self::operate($a, $b, $token));
            }
        }

        return array_pop($stack);
    }

    private static function evaluatePrefix($exp) {
        $stack = [];
        $tokens = array_reverse(self::tokenizeExpression($exp));

        foreach ($tokens as $token) {
            if (preg_match('/\s/', $token)) continue;
            else if (is_numeric($token)) {
                array_push($stack, $token);
            } elseif (isset(self::$variables[$token])) {
                array_push($stack, self::$variables[$token]);
            } elseif (self::isOperator($token)) {
                $a = array_pop($stack);
                $b = array_pop($stack);
                array_push($stack, self::operate($a, $b, $token));
            }
        }

        return array_pop($stack);
    }

    private static function tokenizeExpression($exp, $infix = false) {
        $pattern = $infix 
            ? '/([a-zA-Z][a-zA-Z0-9]*)|\d+|([+\-*\/^()])/' 
            : '/([a-zA-Z][a-zA-Z0-9]*)|\d+|([+\-*\/^])/';
        
        preg_match_all($pattern, $exp, $matches);
        
        return array_map(function($token) {
            return is_numeric($token) ? $token + 0 : $token;
        }, array_filter($matches[0]));
    }

    private static function precedence($op) {
        switch ($op) {
            case '+':
            case '-': return 1;
            case '*':
            case '/': return 2;
            case '^': return 3;
            default: return 0;
        }
    }

    private static function isOperator($c) {
        return in_array($c, ['+', '-', '*', '/', '^']);
    }

    private static function isVariable($token) {
        return preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $token);
    }

    private static function operate($a, $b, $op) {
        settype($a, 'float');
        settype($b, 'float');
        
        switch ($op) {
            case '+': return $a + $b;
            case '-': return $a - $b;
            case '*': return $a * $b;
            case '/': 
                if ($b == 0) throw new Exception('Division by zero');
                return $a / $b;
            case '^': return pow($a, $b);
            default: throw new Exception("Invalid operator: $op");
        }
    }
}

// Handle request
try {
    $data = json_decode(file_get_contents('php://input'), true);
    echo json_encode(ExpressionEvaluator::evaluate($data));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
exit;
?>
