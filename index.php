<?php
// Main entry point for the application

// Load configuration
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'config/email.php';

// Load utility classes
require_once 'utils/Session.php';
require_once 'utils/Validator.php';
require_once 'utils/Security.php';

// Start session
Session::start();

// Check if user is logged in, if not redirect to login page
// Except for login, register, activation, password reset pages
// Except for login, register, activation, password reset pages
$allowed_pages = [
    'login', 'register', 'activate', 'reset-password',
    'password-reset', 'verify-reset', 'new-password'
];

// Also check if the current path has .php extension (for direct access)
$current_path = $_SERVER['REQUEST_URI'];
if (strpos($current_path, '.php') !== false) {
    // Allow direct access to these PHP files
    $php_files = ['login.php', 'register.php', 'activate.php', 'reset-password.php'];
    foreach ($php_files as $file) {
        if (strpos($current_path, $file) !== false) {
            $allowed_pages[] = basename($file, '.php');
        }
    }
}

// Get current page from URL
$url = isset($_GET['url']) ? $_GET['url'] : '';
$page = explode('/', $url)[0];

// Check authentication requirement
if (!Session::isLoggedIn() && !in_array($page, $allowed_pages)) {
    // Remember the requested URL for redirection after login
    Session::set('redirect_url', $_SERVER['REQUEST_URI']);
    
    // Redirect to login page

    header('Location: ' . BASE_URL . '/login');
    exit;
}

// Include router or controller based on URL
// Simple routing based on URL pattern
switch ($page) {
    case '':
    case 'home':
        include_once 'controllers/NoteController.php';
        $controller = new NoteController();
        $controller->index();
        break;
        
    case 'login':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;
        
    case 'register':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;
        
    case 'logout':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'activate':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->activate();
        break;

    case 'verify-otp':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->verifyOTP();
        break;
        
    case 'reset-password':
        include_once 'controllers/AuthController.php';
        $controller = new AuthController();
        $controller->resetPassword();
        break;
        
    case 'notes':
        include_once 'controllers/NoteController.php';
        $controller = new NoteController();
        
        // Get action from URL if available
        $action = isset(explode('/', $url)[1]) ? explode('/', $url)[1] : 'index';
        
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->edit($id);
                break;
            case 'delete':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->delete($id);
                break;
            case 'share':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->share($id);
                break;
            case 'view':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->view($id);
                break;
            default:
                $controller->index();
                break;
        }
        break;
        
    case 'labels':
        include_once 'controllers/LabelController.php';
        $controller = new LabelController();
        
        // Get action from URL if available
        $action = isset(explode('/', $url)[1]) ? explode('/', $url)[1] : 'index';
        
        switch ($action) {
            case 'create':
                $controller->create();
                break;
            case 'edit':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->edit($id);
                break;
            case 'delete':
                $id = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : null;
                $controller->delete($id);
                break;
            default:
                $controller->index();
                break;
        }
        break;
        
    case 'profile':
        include_once 'controllers/ProfileController.php';
        $controller = new ProfileController();
        
        // Get action from URL if available
        $action = isset(explode('/', $url)[1]) ? explode('/', $url)[1] : 'index';
        
        switch ($action) {
            case 'edit':
                $controller->edit();
                break;
            case 'change-password':
                $controller->changePassword();
                break;
            case 'preferences':
                $controller->preferences();
                break;
            default:
                $controller->index();
                break;
        }
        break;
        
    // API routes for AJAX requests
    case 'api':
        header('Content-Type: application/json');
        
        $api = isset(explode('/', $url)[1]) ? explode('/', $url)[1] : '';
        $action = isset(explode('/', $url)[2]) ? explode('/', $url)[2] : '';
        
        switch ($api) {
            case 'notes':
                include_once 'api/notes.php';
                break;
            case 'labels':
                include_once 'api/labels.php';
                break;
            case 'users':
                include_once 'api/users.php';
                break;
            default:
                echo json_encode(['error' => 'API endpoint not found']);
                break;
        }
        break;
        
    default:
        // 404 - Page not found
        header("HTTP/1.0 404 Not Found");
        include 'views/404.php';
        break;
}