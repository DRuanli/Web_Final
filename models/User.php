<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Register a new user
     * 
     * @param string $email User's email address
     * @param string $display_name User's display name
     * @param string $password User's password (plain text)
     * @return array Result with success status and user info or error
     */
    public function register($email, $display_name, $password) {
        // Hash the password using bcrypt
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_HASH_COST]);
        
        // Generate activation token
        $activation_token = bin2hex(random_bytes(32));
        
        // Begin transaction
        $this->db->begin_transaction();
        
        try {
            // Insert user data
            $stmt = $this->db->prepare("INSERT INTO users (email, display_name, password, activation_token) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $email, $display_name, $hashed_password, $activation_token);
            
            // Execute the query
            if (!$stmt->execute()) {
                throw new Exception("Failed to create user: " . $this->db->error);
            }
            
            $user_id = $stmt->insert_id;
            $stmt->close();
            
            // Create default user preferences
            $this->createDefaultPreferences($user_id);
            
            // Commit transaction
            $this->db->commit();
            
            return [
                'success' => true,
                'user_id' => $user_id,
                'email' => $email,
                'display_name' => $display_name,
                'activation_token' => $activation_token
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Create default preferences for a new user
     * 
     * @param int $user_id User ID
     * @return bool Success status
     */
    private function createDefaultPreferences($user_id) {
        $stmt = $this->db->prepare("INSERT INTO user_preferences (user_id) VALUES (?)");
        $stmt->bind_param("i", $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Check if email already exists
     * 
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
    
    /**
     * Get user by email
     * 
     * @param string $email User's email
     * @return array|null User data or null if not found
     */
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|null User data or null if not found
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
    
    /**
     * Activate user account
     * 
     * @param string $email User's email
     * @param string $token Activation token
     * @return bool Success status
     */
    public function activateAccount($email, $token) {
        $stmt = $this->db->prepare("UPDATE users SET is_activated = 1, activation_token = NULL WHERE email = ? AND activation_token = ?");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        return $affected > 0;
    }
}