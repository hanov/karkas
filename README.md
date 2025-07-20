# Karkas Framework

A lightweight, minimalist PHP framework designed for rapid web application development. Karkas provides a simple MVC-like architecture with SQLite database integration and a straightforward template system.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
- [Architecture](#architecture)
- [Routing System](#routing-system)
- [Controllers](#controllers)
- [Database Operations](#database-operations)
- [Template System](#template-system)
- [AJAX Endpoints](#ajax-endpoints)
- [File Structure](#file-structure)
- [Configuration](#configuration)
- [Advanced Usage](#advanced-usage)
- [Security Considerations](#security-considerations)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Overview

Karkas is a PHP micro-framework built for developers who need a simple, no-nonsense solution for web applications. It eschews complex configuration files and extensive boilerplate code in favor of convention over configuration, making it ideal for:

- Rapid prototyping
- Small to medium web applications
- API development
- Learning MVC patterns
- Projects requiring minimal overhead

The framework uses SQLite as its default database engine, eliminating the need for complex database server setup during development. It features automatic class loading, simple routing, and a flexible template system.

## Features

### Core Features
- **Zero Configuration**: Works out of the box with sensible defaults
- **SQLite Integration**: Built-in SQLite database support with simple query methods
- **Automatic Routing**: File-based routing system that maps URLs to controllers
- **Template Engine**: Simple template system with variable injection
- **AJAX Support**: Built-in AJAX endpoint handling
- **Auto-loading**: Automatic class loading for models
- **Error Handling**: Custom 404 error pages
- **File Upload Support**: Built-in file upload and image resize functions
- **Security**: Input sanitization and SQL injection protection
- **Mobile Detection**: User agent detection for responsive applications

### Development Features
- **PHP Built-in Server**: Designed to work seamlessly with PHP's development server
- **Debug Support**: Error reporting and debugging capabilities
- **Flexible Architecture**: Easy to extend and customize
- **Minimal Dependencies**: No external dependencies required

## Requirements

- **PHP 7.0 or higher** (PHP 8.0+ recommended)
- **SQLite3 extension** (usually included with PHP)
- **Web server** (Apache, Nginx, or PHP built-in server)

### Optional Requirements
- **GD extension** for image processing
- **Memcache extension** for caching (if enabled)

## Installation

### Method 1: Direct Download
1. Download or clone the Karkas framework
2. Extract to your web server directory
3. Ensure proper file permissions
4. Start development server

### Method 2: Git Clone
```bash
git clone https://github.com/your-username/karkas.git
cd karkas
php -S localhost:8080
```

### File Permissions
Ensure the following directories have write permissions:
- `i/` (for file uploads)
- Root directory (for SQLite database file)

## Quick Start

### 1. Start the Development Server
Navigate to your Karkas installation directory and run:
```bash
php -S localhost:8080
```

### 2. Access Your Application
Open your browser and navigate to:
```
http://localhost:8080
```

You should see the Karkas documentation homepage.

### 3. Test AJAX Functionality
Visit the AJAX test endpoint:
```
http://localhost:8080/ajax/primer
```

### 4. Create Your First Controller
Create a new file `controller/hello.php`:
```php
<?php
class Controller
{
    function exec()
    {
        $data['title'] = 'Hello World';
        $data['body'] = '<h1>Welcome to Karkas!</h1>';
        return load_tpl($data);
    }
}
```

Access it via: `http://localhost:8080/hello`

## Architecture

Karkas follows a simplified MVC (Model-View-Controller) pattern:

### Request Flow
1. **Request**: User makes HTTP request
2. **Routing**: `index.php` determines which controller to load
3. **Controller**: Handles business logic and data processing
4. **Model**: Interacts with database (auto-loaded as needed)
5. **View**: Templates render the final output
6. **Response**: HTML is returned to the browser

### Core Components

#### Entry Point (`index.php`)
- Defines database configuration
- Loads core functions
- Handles routing logic
- Instantiates and executes controllers

#### Core Functions (`core/fns-min.php`)
- Database connection management
- Query execution functions
- Template loading system
- Utility functions (file upload, image resize, etc.)

#### Controllers (`controller/`)
- Handle HTTP requests
- Process business logic
- Interact with models
- Return rendered templates

#### Views (`view/`)
- HTML templates with PHP variable injection
- Global template for consistent layout
- Specific templates for different pages

#### Models (`model/`)
- Database interaction classes
- Business logic encapsulation
- Auto-loaded when referenced

## Routing System

Karkas uses a simple file-based routing system that maps URLs directly to controller files.

### Basic Routing
```
URL Pattern          →  Controller File
/                   →  controller/main.php
/about              →  controller/about.php
/user/profile       →  controller/user.php (with route parsing)
/ajax/method        →  controller/ajax.php (calls specific method)
/nonexistent        →  controller/404.php
```

### Route Parameters
Controllers can access route segments via `$_GET['route']`:
```php
// URL: /user/profile/123
$segments = explode('/', $_GET['route']);
$controller = $segments[0]; // 'user'
$action = $segments[1];     // 'profile'
$id = $segments[2];         // '123'
```

### Custom Routes
To create custom routing logic, modify the routing section in `index.php`:
```php
// Custom routing example
$route = $_GET['route'];
if (strpos($route, 'api/') === 0) {
    // Handle API routes
    $controller = 'api';
} else {
    $controller = current(explode("/", $route));
}
```

## Controllers

Controllers are the heart of your application logic. Every controller must follow a specific structure.

### Controller Structure
```php
<?php
class Controller
{
    function exec()
    {
        // Your application logic here
        $data['title'] = 'Page Title';
        $data['body'] = 'Page content';
        
        // Return rendered template
        return load_tpl($data);
    }
}
```

### Controller Examples

#### Basic Page Controller
```php
<?php
class Controller
{
    function exec()
    {
        $data['title'] = 'About Us';
        $data['body'] = load_tpl('about-content');
        return load_tpl($data);
    }
}
```

#### Database-Driven Controller
```php
<?php
class Controller
{
    function exec()
    {
        // Fetch users from database
        $users = q_array("SELECT * FROM users ORDER BY name");
        
        $data['title'] = 'User List';
        $data['body'] = load_tpl('user-list', ['users' => $users]);
        
        return load_tpl($data);
    }
}
```

#### Form Processing Controller
```php
<?php
class Controller
{
    function exec()
    {
        if ($_POST['name'] && $_POST['email']) {
            // Process form submission
            $name = SQLite3::escapeString($_POST['name']);
            $email = SQLite3::escapeString($_POST['email']);
            
            query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
            
            $data['message'] = 'User created successfully!';
        }
        
        $data['title'] = 'Add User';
        $data['body'] = load_tpl('user-form', $data);
        
        return load_tpl($data);
    }
}
```

## Database Operations

Karkas provides three simple functions for database interaction, all using SQLite.

### Database Functions

#### `q_array($query)`
Returns an array of all matching rows:
```php
// Get all users
$users = q_array("SELECT * FROM users");

// Get users with condition
$active_users = q_array("SELECT * FROM users WHERE status = 'active'");

// Get with limit
$recent_users = q_array("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
```

#### `query($query)`
Executes queries without returning data (INSERT, UPDATE, DELETE):
```php
// Insert new record
query("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");

// Update existing record
query("UPDATE users SET status = 'active' WHERE id = 1");

// Delete record
query("DELETE FROM users WHERE id = 1");

// Create table
query("CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    price DECIMAL(10,2)
)");
```

#### `row($query)`
Returns a single row as an associative array:
```php
// Get single user
$user = row("SELECT * FROM users WHERE id = 1");
echo $user['name']; // Access the name field

// Get count
$count = row("SELECT COUNT(*) as total FROM users");
echo $count['total'];

// Get single field
$name = row("SELECT name FROM users WHERE id = 1");
```

### Database Schema Examples

#### Users Table
```sql
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    status TEXT DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### Products Table
```sql
CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INTEGER,
    image_url TEXT,
    stock_quantity INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

#### Log Table
```sql
CREATE TABLE IF NOT EXISTS log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip TEXT,
    date INTEGER,
    sid TEXT,
    client_name TEXT,
    host TEXT,
    rewarded_from TEXT,
    filename TEXT
);
```

### Data Sanitization
Always sanitize user input before database operations:
```php
// Using built-in function
$safe_data = safe_db_input($_POST);

// Manual escaping
$name = SQLite3::escapeString($_POST['name']);
$email = SQLite3::escapeString($_POST['email']);
```

## Template System

Karkas uses a simple but powerful template system that allows you to separate presentation from logic.

### Template Structure

#### Global Template (`view/global-template.html`)
```html
<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Files -->
    <?php if(isset($css)): ?>
        <?php foreach($css as $file): ?>
            <link rel="stylesheet" href="<?= $file ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <main>
        <?= $body ?>
    </main>
    
    <!-- JavaScript Files -->
    <?php if(isset($js)): ?>
        <?php foreach($js as $file): ?>
            <script src="<?= $file ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
```

### Template Loading

#### Basic Template Loading
```php
// Load template without data
$content = load_tpl('page-header');

// Load template with data
$data = ['username' => 'John', 'email' => 'john@example.com'];
$content = load_tpl('user-profile', $data);

// Load global template
$page_data = [
    'title' => 'My Page',
    'body' => $content
];
return load_tpl($page_data);
```

#### Template Examples

**User Profile Template (`view/user-profile.html`)**
```html
<div class="user-profile">
    <h2>Welcome, <?= $username ?>!</h2>
    <p>Email: <?= $email ?></p>
    <p>Last login: <?= $last_login ?></p>
</div>
```

**User List Template (`view/user-list.html`)**
```html
<div class="user-list">
    <h2>All Users</h2>
    <?php if (!empty($users)): ?>
        <ul>
            <?php foreach ($users as $user): ?>
                <li>
                    <strong><?= htmlspecialchars($user['name']) ?></strong> 
                    - <?= htmlspecialchars($user['email']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>
```

### Adding CSS and JavaScript
```php
$data['title'] = 'My Page';
$data['body'] = 'Content here';

// Add CSS files
$data['css'][] = '/css/main.css';
$data['css'][] = '/css/page-specific.css';
$data['css'][] = 'https://cdn.example.com/framework.css';

// Add JavaScript files
$data['js'][] = '/js/main.js';
$data['js'][] = '/js/page-specific.js';
$data['js'][] = 'https://cdn.example.com/library.js';

return load_tpl($data);
```

## AJAX Endpoints

The AJAX controller provides a powerful way to create API endpoints and handle asynchronous requests.

### AJAX Controller Structure
The `controller/ajax.php` file automatically routes methods based on URL segments:

```php
<?php
class Controller
{
    function exec()
    {
        // Get method name from URL segment
        // URL: /ajax/get_users → calls get_users() method
        $method = next(explode('/', $_GET['route']));
        
        if(method_exists($this, $method)) {
            call_user_func(array($this, $method));
        }
    }
    
    // Example AJAX methods
    function get_users()
    {
        $users = q_array("SELECT * FROM users");
        header('Content-Type: application/json');
        echo json_encode($users);
    }
    
    function create_user()
    {
        if ($_POST['name'] && $_POST['email']) {
            $name = SQLite3::escapeString($_POST['name']);
            $email = SQLite3::escapeString($_POST['email']);
            
            query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
            
            echo json_encode(['status' => 'success', 'message' => 'User created']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        }
    }
    
    function delete_user()
    {
        if ($_POST['id']) {
            $id = intval($_POST['id']);
            query("DELETE FROM users WHERE id = $id");
            echo json_encode(['status' => 'success']);
        }
    }
}
```

### AJAX Usage Examples

#### JavaScript Client Code
```javascript
// GET request
fetch('/ajax/get_users')
    .then(response => response.json())
    .then(data => console.log(data));

// POST request
fetch('/ajax/create_user', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'name=John&email=john@example.com'
})
.then(response => response.json())
.then(data => console.log(data));
```

#### jQuery Examples
```javascript
// GET with jQuery
$.get('/ajax/get_users', function(data) {
    console.log(data);
});

// POST with jQuery
$.post('/ajax/create_user', {
    name: 'John',
    email: 'john@example.com'
}, function(response) {
    if (response.status === 'success') {
        alert('User created!');
    }
}, 'json');
```

## File Structure

```
karkas/
├── controller/                 # Controllers
│   ├── main.php               # Homepage controller
│   ├── ajax.php               # AJAX endpoints
│   ├── 404.php                # Error page controller
│   └── [custom].php           # Your custom controllers
├── view/                      # HTML templates
│   ├── global-template.html   # Main page template
│   ├── 404.html               # Error page template
│   └── [custom].html          # Your custom templates
├── core/                      # Core framework files
│   └── fns-min.php           # Core functions and database
├── model/                     # Model classes (auto-loaded)
│   └── [ModelName].php       # Your model classes
├── i/                         # Images and uploads
│   └── uploads/               # File upload directory
├── css/                       # Stylesheets (optional)
├── js/                        # JavaScript files (optional)
├── index.php                  # Main entry point
├── database.sqlite            # SQLite database file
├── .htaccess                  # Apache rewrite rules (optional)
└── README.md                  # This documentation
```

### Directory Purposes

- **`controller/`**: Contains all controller classes that handle HTTP requests
- **`view/`**: HTML template files with PHP variable injection
- **`core/`**: Framework core functions and utilities
- **`model/`**: Business logic classes (automatically loaded)
- **`i/`**: File uploads and images
- **`css/js/`**: Static assets (stylesheets and JavaScript)

## Configuration

### Database Configuration
Edit `index.php` to change database settings:
```php
// SQLite database path
define('DB_PATH', 'database.sqlite');

// For different database file
define('DB_PATH', 'data/myapp.db');

// For absolute path
define('DB_PATH', '/var/www/data/database.sqlite');
```

### Error Reporting
```php
// Enable all error reporting (development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Disable error reporting (production)
error_reporting(0);
```

### Session Configuration
Sessions are started automatically. Configure session settings:
```php
// Set session parameters
ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400);
session_start();
```

## Advanced Usage

### Custom Model Classes
Create model classes in the `model/` directory:

**`model/user.php`**
```php
<?php
class User
{
    public function getAll()
    {
        return q_array("SELECT * FROM users ORDER BY name");
    }
    
    public function getById($id)
    {
        return row("SELECT * FROM users WHERE id = " . intval($id));
    }
    
    public function create($name, $email)
    {
        $name = SQLite3::escapeString($name);
        $email = SQLite3::escapeString($email);
        
        return query("INSERT INTO users (name, email) VALUES ('$name', '$email')");
    }
    
    public function authenticate($email, $password)
    {
        $email = SQLite3::escapeString($email);
        $user = row("SELECT * FROM users WHERE email = '$email'");
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
```

**Using Models in Controllers**
```php
<?php
class Controller
{
    function exec()
    {
        // Models are auto-loaded
        $userModel = new User();
        $users = $userModel->getAll();
        
        $data['title'] = 'Users';
        $data['body'] = load_tpl('user-list', ['users' => $users]);
        
        return load_tpl($data);
    }
}
```

### File Upload Handling
```php
// In your controller
if ($_FILES['upload']) {
    // Upload file to 'i/' directory
    $filename = file_upload('upload', 'custom-name', '.jpg', 'i');
    
    if ($filename) {
        // Create thumbnail
        $thumb = resize_im($filename, 'thumb_', 150, 150, 'i', 80);
        
        // Save to database
        query("UPDATE users SET profile_image = '$filename' WHERE id = 1");
    }
}
```

### Custom Pagination
```php
// Get total pages
$total_pages = paginator_qty('users', 20); // 20 items per page

// Get current page data
$current_page = $_GET['page'] ?? 1;
$limit = paginator_limit('users', $current_page, 20);

// Get users for current page
$users = q_array("SELECT * FROM users ORDER BY name $limit");

// Generate pagination interface
$pagination = paginator_interface('users', 'page', $current_page, $total_pages);
```

### Logging System
```php
// Enable automatic logging
function custom_log($message, $level = 'info') {
    query("INSERT INTO log (
        ip, date, sid, client_name, host, rewarded_from, filename
    ) VALUES (
        '" . client_ip() . "',
        '" . time() . "',
        '" . session_id() . "',
        '" . $_SERVER['HTTP_USER_AGENT'] . "',
        '" . $_SERVER['HTTP_HOST'] . "',
        '" . ($_SERVER['HTTP_REFERER'] ?? '') . "',
        '$message'
    )");
}

// Use in controllers
custom_log('User login attempt');
```

### Mobile Detection
```php
// Check if user is on mobile device
if (is_mobile()) {
    $data['body'] = load_tpl('mobile-template', $data);
} else {
    $data['body'] = load_tpl('desktop-template', $data);
}
```

## Security Considerations

### Input Sanitization
```php
// Always sanitize database inputs
$safe_input = safe_db_input($_POST);

// Or manually escape
$name = SQLite3::escapeString($_POST['name']);

// Sanitize output
$safe_output = safe_output($data);
```

### Password Hashing
```php
// Hash passwords before storing
$hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
query("INSERT INTO users (email, password) VALUES ('$email', '$hashed')");

// Verify passwords
if (password_verify($_POST['password'], $user['password'])) {
    // Login successful
}
```

### CSRF Protection
```php
// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Verify CSRF token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token mismatch');
}
```

### SQL Injection Prevention
```php
// Use parameterized queries (manual implementation)
function safe_query($query, $params = []) {
    foreach ($params as $key => $value) {
        $safe_value = SQLite3::escapeString($value);
        $query = str_replace(":$key", "'$safe_value'", $query);
    }
    return query($query);
}

// Usage
safe_query("INSERT INTO users (name, email) VALUES (:name, :email)", [
    'name' => $_POST['name'],
    'email' => $_POST['email']
]);
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Errors
```
Error: SQLite query error: unable to open database file
```
**Solution**: Ensure the database file and directory have write permissions:
```bash
chmod 755 /path/to/karkas
chmod 644 database.sqlite
```

#### 2. Template Not Found
```
Error: Template not_found not found in folder view
```
**Solution**: Check template file exists and has correct name:
```bash
ls -la view/your-template.html
```

#### 3. Controller Not Loading
```
Error: 404 Page
```
**Solution**: Verify controller file exists and has correct structure:
```php
<?php
class Controller {
    function exec() {
        // Must return something
        return load_tpl(['title' => 'Test', 'body' => 'Test']);
    }
}
```

#### 4. AJAX Method Not Found
**Solution**: Ensure method exists in `controller/ajax.php` and matches URL:
```
URL: /ajax/my_method
Method: function my_method() { ... }
```

### Debugging Tips

#### Enable Error Reporting
```php
// Add to index.php for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

#### Debug Database Queries
```php
// Add before database operations
function debug_query($query) {
    echo "<pre>Query: $query</pre>";
    return query($query);
}
```

#### Check Request Data
```php
// Add to controller to debug request
echo "<pre>";
print_r($_GET);
print_r($_POST);
print_r($_SERVER);
echo "</pre>";
```

## Performance Optimization

### Database Optimization
```sql
-- Add indexes for better query performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_status ON users(status);
CREATE INDEX idx_log_date ON log(date);
```

### Template Caching
```php
// Simple template caching
function cached_load_tpl($template, $data = [], $cache_time = 3600) {
    $cache_file = "cache/" . md5($template . serialize($data)) . ".html";
    
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        return file_get_contents($cache_file);
    }
    
    $content = load_tpl($template, $data);
    file_put_contents($cache_file, $content);
    
    return $content;
}
```

### Static File Serving
Use web server (Apache/Nginx) to serve static files directly:
```apache
# .htaccess for static files
<FilesMatch "\.(css|js|png|jpg|gif|ico)$">
    ExpiresActive On
    ExpiresDefault "access plus 1 month"
</FilesMatch>
```

## Contributing

### Development Setup
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Coding Standards
- Use PSR-2 coding style
- Comment your code thoroughly
- Follow existing naming conventions
- Write tests for new features

### Reporting Issues
When reporting issues, include:
- PHP version
- Error messages
- Steps to reproduce
- Expected vs actual behavior

## License

Karkas Framework is open-source software released under the MIT License. See the LICENSE file for details.

---

**Happy coding with Karkas! 🚀**

For more information, visit the project homepage or check the inline documentation at `http://localhost:8080` when running your Karkas application.