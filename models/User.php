<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    // Register a new user
    public function register($email, $display_name, $password) {
        // Check if email already exists
        if ($this->emailExists($email)) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]);
        
        // Generate activation token
        $activation_token = bin2hex(random_bytes(32));
        
        // Insert user into database
        $stmt = $this->db->prepare("INSERT INTO users (email, display_name, password, activation_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $email, $display_name, $hashed_password, $activation_token);
        
        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            
            // Create default preferences
            $this->createDefaultPreferences($user_id);
            
            return [
                'success' => true, 
                'user_id' => $user_id,
                'activation_token' => $activation_token
            ];
        } else {
            return ['success' => false, 'message' => 'Registration failed: ' . $stmt->error];
        }
    }
    
    // Check if email exists
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
    
    // Create default user preferences
    private function createDefaultPreferences($user_id) {
        $stmt = $this->db->prepare("INSERT INTO user_preferences (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
    
    // Validate login credentials
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, display_name, password, is_activated FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'user_id' => $user['id'],
                    'display_name' => $user['display_name'],
                    'is_activated' => $user['is_activated']
                ];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid email or password'];
    }
    
    // Activate user account
    public function activateAccount($email, $token) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND activation_token = ? AND is_activated = 0");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            $update = $this->db->prepare("UPDATE users SET is_activated = 1, activation_token = NULL WHERE id = ?");
            $update->bind_param("i", $user['id']);
            
            if ($update->execute()) {
                return ['success' => true, 'user_id' => $user['id']];
            }
        }
        
        return ['success' => false, 'message' => 'Invalid activation token or account already activated'];
    }
    
    // Get user by ID
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, email, display_name, is_activated, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Get user by email
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT id, email, display_name, is_activated, created_at FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Generate password reset token
    public function createPasswordResetToken($email) {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        // Generate reset token
        $reset_token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', time() + RESET_TOKEN_EXPIRY);
        
        $stmt = $this->db->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $reset_token, $expiry, $user['id']);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'reset_token' => $reset_token,
                'display_name' => $user['display_name']
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create reset token'];
    }
    
    // Verify password reset token
    public function verifyResetToken($email, $token) {
        $current_time = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_token_expiry > ?");
        $stmt->bind_param("sss", $email, $token, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows === 1;
    }
    
    // Reset password
    public function resetPassword($email, $token, $new_password) {
        if (!$this->verifyResetToken($email, $token)) {
            return ['success' => false, 'message' => 'Invalid or expired token'];
        }
        
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]);
        
        $stmt = $this->db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to reset password'];
    }
    
    // Generate OTP for password reset (adding OTP support)
    public function generateOTP($email) {
        $user = $this->getUserByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour validity
        
        // Store OTP in database (you need to add otp and otp_expiry columns to users table)
        $stmt = $this->db->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $otp, $expiry, $user['id']);
        
        if ($stmt->execute()) {
            return [
                'success' => true,
                'otp' => $otp,
                'display_name' => $user['display_name']
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to generate OTP'];
    }
    
    // Verify OTP
    public function verifyOTP($email, $otp) {
        $current_time = date('Y-m-d H:i:s');
        
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND otp = ? AND otp_expiry > ?");
        $stmt->bind_param("sss", $email, $otp, $current_time);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows === 1;
    }
    
    // Reset password with OTP
    public function resetPasswordWithOTP($email, $otp, $new_password) {
        if (!$this->verifyOTP($email, $otp)) {
            return ['success' => false, 'message' => 'Invalid or expired OTP'];
        }
        
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]);
        
        $stmt = $this->db->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        
        return ['success' => false, 'message' => 'Failed to reset password'];
    }
}