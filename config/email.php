<?php
// Email configuration

// Email settings - replace with your actual settings
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'your-email@gmail.com'); // Replace with your actual email
define('MAIL_PASSWORD', 'your-email-password');  // Replace with your actual password
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'your-email@gmail.com');
define('MAIL_FROM_NAME', 'Note Management App');

// For local development, you can use a local email sending service
// like Mailtrap for testing.
// Or for a quick setup, use a PHP mail() function
function sendEmail($to, $subject, $message) {
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM_ADDRESS . '>' . "\r\n";
    
    // For local development, you can log emails instead of sending them
    $log_message = "To: $to\nSubject: $subject\nMessage: $message";
    
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        // Log email instead of sending
        file_put_contents(ROOT_PATH . '/logs/emails.log', $log_message, FILE_APPEND);
        return true;
    } else {
        // Actually send email
        return mail($to, $subject, $message, $headers);
    }
}

// For activation emails
function sendActivationEmail($user_email, $user_name, $activation_token) {
    $activation_link = BASE_URL . '/activate.php?token=' . $activation_token . '&email=' . urlencode($user_email);
    
    $subject = "Activate Your Account - " . APP_NAME;
    
    $message = "
    <html>
    <head>
        <title>Account Activation</title>
    </head>
    <body>
        <h2>Welcome to " . APP_NAME . "</h2>
        <p>Hello " . htmlspecialchars($user_name) . ",</p>
        <p>Thank you for registering. Please click the link below to activate your account:</p>
        <p><a href='" . $activation_link . "'>Activate Your Account</a></p>
        <p>If the button doesn't work, copy and paste this URL into your browser:</p>
        <p>" . $activation_link . "</p>
        <p>This link will expire in " . (ACTIVATION_TOKEN_EXPIRY / 3600) . " hours.</p>
        <p>Best regards,<br>The " . APP_NAME . " Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($user_email, $subject, $message);
}

// For password reset emails
function sendPasswordResetEmail($user_email, $user_name, $reset_token) {
    $reset_link = BASE_URL . '/reset-password.php?token=' . $reset_token . '&email=' . urlencode($user_email);
    
    $subject = "Password Reset - " . APP_NAME;
    
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
    </head>
    <body>
        <h2>" . APP_NAME . " - Password Reset</h2>
        <p>Hello " . htmlspecialchars($user_name) . ",</p>
        <p>You requested a password reset. Please click the link below to reset your password:</p>
        <p><a href='" . $reset_link . "'>Reset Your Password</a></p>
        <p>If the button doesn't work, copy and paste this URL into your browser:</p>
        <p>" . $reset_link . "</p>
        <p>This link will expire in " . (RESET_TOKEN_EXPIRY / 3600) . " hour(s).</p>
        <p>If you didn't request this password reset, please ignore this email.</p>
        <p>Best regards,<br>The " . APP_NAME . " Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($user_email, $subject, $message);
}

// For note sharing emails
function sendNoteSharedEmail($recipient_email, $recipient_name, $sender_name, $note_title, $permission) {
    $login_link = BASE_URL . '/login.php';
    
    $subject = $sender_name . " shared a note with you - " . APP_NAME;
    
    $access_type = ($permission == 'edit') ? 'edit' : 'view';
    
    $message = "
    <html>
    <head>
        <title>Note Shared With You</title>
    </head>
    <body>
        <h2>A Note Has Been Shared With You</h2>
        <p>Hello " . htmlspecialchars($recipient_name) . ",</p>
        <p>" . htmlspecialchars($sender_name) . " has shared a note titled <strong>\"" . htmlspecialchars($note_title) . "\"</strong> with you.</p>
        <p>You have <strong>" . $access_type . "</strong> permission for this note.</p>
        <p>To access this note, please <a href='" . $login_link . "'>log in to your account</a>.</p>
        <p>Best regards,<br>The " . APP_NAME . " Team</p>
    </body>
    </html>
    ";
    
    return sendEmail($recipient_email, $subject, $message);
}