<?php
class AuthController {
    private $userModel;
    
    public function __construct() {
        require_once MODELS_PATH . '/User.php';
        $this->userModel = new User();
    }
    
    /**
     * Display and process registration
     */
    public function register() {
        // If user is already logged in, redirect to home
        if (Session::isLoggedIn()) {
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegistration();
        } else {
            // Display registration form
            include VIEWS_PATH . '/auth/register.php';
        }
    }
    
    /**
     * Process registration form submission
     */
    private function processRegistration() {
        // Validate input
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $display_name = trim($_POST['display_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        
        // Validate email
        if (!$email) {
            $errors['email'] = 'Please enter a valid email address';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] = 'This email is already registered';
        }
        
        // Validate display name
        if (empty($display_name)) {
            $errors['display_name'] = 'Please enter your display name';
        } elseif (strlen($display_name) < 3 || strlen($display_name) > 100) {
            $errors['display_name'] = 'Display name must be between 3 and 100 characters';
        }
        
        // Validate password
        if (empty($password)) {
            $errors['password'] = 'Please enter a password';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Password must be at least 8 characters long';
        }
        
        // Confirm passwords match
        if ($password !== $confirm_password) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        // If there are errors, redisplay the form with error messages
        if (!empty($errors)) {
            Session::setFlash('errors', $errors);
            Session::setFlash('old_input', [
                'email' => $email,
                'display_name' => $display_name
            ]);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }
        
        // Register the user
        $result = $this->userModel->register($email, $display_name, $password);
        
        if ($result['success']) {
            // Send activation email
            require_once 'config/email.php';
            sendActivationEmail($email, $display_name, $result['activation_token']);
            
            // Log the user in automatically
            Session::setAuth($result['user_id'], $email, $display_name);
            
            // Set success message
            Session::setFlash('success', 'Registration successful! Please check your email to activate your account.');
            
            // Redirect to home or saved redirect URL
            $redirect = Session::get('redirect_url', BASE_URL);
            Session::remove('redirect_url');
            
            header('Location: ' . $redirect);
            exit;
        } else {
            // Registration failed
            Session::setFlash('error', $result['error']);
            Session::setFlash('old_input', [
                'email' => $email,
                'display_name' => $display_name
            ]);
            header('Location: ' . BASE_URL . '/register');
            exit;
        }
    }
    
    /**
     * Account activation handler
     */
    public function activate() {
        $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
        $token = $_GET['token'] ?? '';
        
        if (!$email || !$token) {
            Session::setFlash('error', 'Invalid activation link');
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        
        if ($this->userModel->activateAccount($email, $token)) {
            Session::setFlash('success', 'Your account has been activated successfully! You can now log in.');
        } else {
            Session::setFlash('error', 'Invalid or expired activation link. Please contact support.');
        }
        
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}