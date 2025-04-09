<?php
require_once MODELS_PATH . '/User.php';

class ProfileController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    // Display user profile
    public function index() {
        // Get user data
        $user_id = Session::getUserId();
        $user = $this->user->getUserById($user_id);
        
        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Set page data
        $data = [
            'pageTitle' => 'My Profile',
            'user' => $user
        ];
        
        // Load view
        include VIEWS_PATH . '/components/header.php';
        include VIEWS_PATH . '/profile/view.php';
        include VIEWS_PATH . '/components/footer.php';
    }
    
    // Display and process profile edit form
    public function edit() {
        // Get user data
        $user_id = Session::getUserId();
        $user = $this->user->getUserById($user_id);
        
        if (!$user) {
            Session::setFlash('error', 'User not found');
            header('Location: ' . BASE_URL);
            exit;
        }
        
        // Set default data
        $data = [
            'pageTitle' => 'Edit Profile',
            'user' => $user,
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $display_name = trim($_POST['display_name'] ?? '');
            
            // Validate input
            if (empty($display_name)) {
                $data['errors']['display_name'] = 'Display name is required';
            }
            
            // If no errors, update profile
            if (empty($data['errors'])) {
                // Update display name function would need to be added to User model
                $result = $this->user->updateProfile($user_id, $display_name);
                
                if ($result['success']) {
                    Session::setFlash('success', 'Profile updated successfully');
                    header('Location: ' . BASE_URL . '/profile');
                    exit;
                } else {
                    $data['errors']['general'] = $result['message'];
                }
            }
            
            // Update user data for form
            $data['user']['display_name'] = $display_name;
        }
        
        // Load view
        include VIEWS_PATH . '/components/header.php';
        include VIEWS_PATH . '/profile/edit.php';
        include VIEWS_PATH . '/components/footer.php';
    }
    
    // Display and process change password form
    public function changePassword() {
        // Get user data
        $user_id = Session::getUserId();
        
        // Set default data
        $data = [
            'pageTitle' => 'Change Password',
            'errors' => []
        ];
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            // Validate input
            if (empty($current_password)) {
                $data['errors']['current_password'] = 'Current password is required';
            }
            
            if (empty($new_password)) {
                $data['errors']['new_password'] = 'New password is required';
            } elseif (strlen($new_password) < 8) {
                $data['errors']['new_password'] = 'Password must be at least 8 characters';
            }
            
            if ($new_password !== $confirm_password) {
                $data['errors']['confirm_password'] = 'Passwords do not match';
            }
            
            // If no errors, change password
            if (empty($data['errors'])) {
                // Need to add changePassword method to User model
                $result = $this->user->changePassword($user_id, $current_password, $new_password);
                
                if ($result['success']) {
                    Session::setFlash('success', 'Password changed successfully');
                    header('Location: ' . BASE_URL . '/profile');
                    exit;
                } else {
                    $data['errors']['general'] = $result['message'];
                }
            }
        }
        
        // Load view
        include VIEWS_PATH . '/components/header.php';
        include VIEWS_PATH . '/profile/change-password.php';
        include VIEWS_PATH . '/components/footer.php';
    }
    
    // Display and process user preferences form
    public function preferences() {
        // Set default data
        $data = [
            'pageTitle' => 'Preferences',
            'errors' => []
        ];
        
        // Load view
        include VIEWS_PATH . '/components/header.php';
        include VIEWS_PATH . '/profile/preferences.php';
        include VIEWS_PATH . '/components/footer.php';
    }
}