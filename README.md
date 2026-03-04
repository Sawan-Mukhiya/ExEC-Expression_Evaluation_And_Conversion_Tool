# ExEC - Expression Evaluation & Conversion Tool

## Project Overview

**ExEC** (Expression Evaluation and Conversion) is a web application designed to simplify the manipulation and analysis of mathematical expressions by providing instant conversion between different notation systems and accurate evaluation with variable substitution.

## What It Does

ExEC enables users to:

1. **Evaluate Mathematical Expressions** - Calculate the result of expressions in three different notation formats
2. **Convert Between Notations** - Transform expressions seamlessly between infix, prefix, and postfix notations
3. **Track History** - Maintain a personal history of all conversions and evaluations for reference
4. **Secure Authentication** - Register and login with secure password hashing

## Key Features

- ✅ Support for **three expression notations**:
  - **Infix** (a + b * c) - Most human-readable format
  - **Prefix** (* + a b c) - Polish notation used in stack-based languages
  - **Postfix** (a b c + *) - Reverse Polish notation used in calculators

- ✅ **Real-time Expression Validation** - Ensures expressions follow correct syntax before processing
- ✅ **Variable Substitution** - Evaluate expressions with custom variable values
- ✅ **User History Tracking** - Store and view past conversions and evaluations
- ✅ **Secure Authentication System** - User registration and login with bcrypt password hashing
- ✅ **Modern Dark UI** - Professional dark theme for comfortable extended use

## How It Works

### Architecture

The application follows a **three-tier architecture**:

```
┌─────────────────────────────────────────────────────────┐
│           PUBLIC (Frontend - User Interface)             │
│   Home.php | Evaluation.php | Conversion.php | etc.      │
│   Assets: CSS, JavaScript, Images                        │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│            AUTH (Authentication Layer)                   │
│   Login.php | SignUp.php | LoginBackend.php              │
│   Handles user registration and session management       │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│            BACKEND (Business Logic & API)                │
│   ExpressionConversion.php | ExpressionEvaluation.php    │
│   Database.php | Session.php                             │
└─────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────┐
│                  DATABASE (MySQL)                        │
│   users | conversion_history | evaluation_history        │
└─────────────────────────────────────────────────────────┘
```

### Workflow

#### 1. **Expression Conversion Process**
- User selects source notation type (infix, prefix, or postfix)
- Enters the mathematical expression
- Selects target notation type
- Frontend validates the expression using JavaScript
- AJAX request sends validated data to `backend/ExpressionConversion.php`
- Backend algorithm converts between notations using shunting-yard algorithm
- Result is displayed and saved to `conversion_history` table

#### 2. **Expression Evaluation Process**
- User must be logged in (Session.php enforces authentication)
- Selects expression type notation
- Enters expression and clicks "Next"
- JavaScript extracts variables from the expression
- User inputs values for each variable
- Frontend validates expression using JavaScript
- AJAX request sends to `backend/ExpressionEvaluation.php`
- Backend calculates result using stack-based evaluation
- Result is displayed and saved to `evaluation_history` table

#### 3. **Authentication Flow**
- New users register with username, email, and password
- Password is hashed using bcrypt (PASSWORD_BCRYPT)
- Data stored in `users` table
- On login, credentials verified against stored hash
- Session created with user_id and username
- User can access conversion/evaluation features
- Logout destroys session

### Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Frontend** | HTML5, CSS3, JavaScript | User interface and client-side validation |
| **Backend** | PHP 7.4+ | Server-side logic, authentication, API endpoints |
| **Database** | MySQL | Persistent data storage |
| **Server** | Apache (XAMPP) | Web server |

### Algorithms Used

1. **Shunting Yard Algorithm** - Converts infix to postfix notation
2. **Stack-based Conversion** - Converts between prefix/postfix/infix
3. **Postfix Evaluation** - Calculates expression results using operand and operator stacks

### File Structure

```
ExpressionWebsite/
├── public/                    # Frontend pages
│   ├── Home.php              # Landing page
│   ├── Evaluation.php         # Expression evaluation page
│   ├── Conversion.php         # Expression conversion page
│   ├── About.php              # About page
│   ├── Contact.php            # Contact page
│   ├── index.php              # Entry point
│   └── assets/
│       ├── css/Expression.css
│       ├── js/DisplayCondition.js
│       ├── js/ExpressionValidation.js
│       └── images/
├── auth/                      # Authentication
│   ├── Login.php              # Login UI
│   ├── SignUp.php             # Registration UI
│   ├── LoginBackend.php       # Login API
│   ├── SignUpBackend.php      # Registration API
│   └── LogOut.php             # Logout handler
├── backend/                   # Business logic
│   ├── Database.php           # Database connection class
│   ├── Session.php            # Session validation
│   ├── ExpressionConversion.php # Conversion API
│   ├── ExpressionEvaluation.php # Evaluation API
│   ├── GetConversionHistory.php # History API
│   └── GetEvaluationHistory.php # History API
└── database_schema.sql        # Database setup script
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (XAMPP recommended)

### Steps

1. **Copy project to web root**
   ```bash
   cp -r ExpressionWebsite C:\\xampp\\htdocs\\
   ```

2. **Create database**
   ```bash
   mysql -u root < database_schema.sql
   ```

3. **Configure database credentials** (if needed)
   - Edit: `backend/Database.php`
   - Update: host, username, password

4. **Access the application**
   ```
   http://localhost/ExpressionWebsite/public/
   ```

## Usage Examples

### Convert Infix to Postfix
- Input: `a + b * c`
- Output: `a b c * +`

### Convert Prefix to Infix
- Input: `+ a * b c`
- Output: `(a + (b * c))`

### Evaluate Postfix Expression
- Input: `2 3 + 5 *` with variables: {2=2, 3=3, 5=5}
- Output: `25`

## Database Schema

### users table
- `user_id` (INT, PK, AI)
- `username` (VARCHAR 50, UNIQUE)
- `email` (VARCHAR 100, UNIQUE)
- `password_hash` (VARCHAR 255)
- `created_at`, `updated_at` (TIMESTAMP)

### conversion_history table
- `conversion_id` (INT, PK, AI)
- `user_id` (INT, FK)
- `source_type`, `target_type` (ENUM: infix, prefix, postfix)
- `original_expression`, `converted_expression` (VARCHAR 500)
- `conversion_timestamp` (TIMESTAMP)

### evaluation_history table
- `evaluation_id` (INT, PK, AI)
- `user_id` (INT, FK)
- `expression_type` (ENUM: infix, prefix, postfix)
- `expression` (VARCHAR 500)
- `variables` (JSON)
- `result` (DECIMAL)
- `evaluation_timestamp` (TIMESTAMP)

## Security Features

- ✅ Password hashing with bcrypt
- ✅ SQL prepared statements (PDO)
- ✅ Session-based authentication
- ✅ Input validation on frontend and backend
- ✅ Protected routes (Session.php enforces login)
- ✅ Error logging without exposing details

## Future Enhancements

- Add support for more mathematical operators
- Implement user profile settings
- Add export history to PDF/CSV
- Real-time expression graphs/visualization
- Mobile app version
- Support for trigonometric functions

## Support & Contribution

For issues, questions, or contributions, please refer to the project documentation or contact the development team.

---

**ExEC** - Making Mathematical Expressions Simple! 🚀
