<?php
// Check and load Composer's autoloader if available
if (file_exists(ROOT_PATH . '/vendor/autoload.php')) {
    require_once ROOT_PATH . '/vendor/autoload.php';
}

// Always declare the namespace aliases at the file level
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if PHPMailer is available, otherwise use default email functions
function hasPhpMailer() {
    return class_exists('PHPMailer\PHPMailer\PHPMailer');
}

// Send email with PHPMailer if available
function sendEmailWithPhpMailer($to, $subject, $message) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        // Recipients
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        return $mail->send();
    } catch (Exception $e) {
        // Log error
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Send activation email
function sendActivationEmail($user_email, $user_name, $activation_token) {
    $activation_link = BASE_URL . '/activate?token=' . $activation_token . '&email=' . urlencode($user_email);
    
    $subject = "Activate Your Account - " . APP_NAME;
    
    $message = "
    <html>
    <head>
        <title>Account Activation</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; 
                     text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { margin-top: 30px; font-size: 12px; color: #777; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Welcome to " . APP_NAME . "</h2>
            <p>Hello " . htmlspecialchars($user_name) . ",</p>
            <p>Thank you for registering. Please click the button below to activate your account:</p>
            <p><a href='" . $activation_link . "' class='button'>Activate Your Account</a></p>
            <p>If the button doesn't work, copy and paste this URL into your browser:</p>
            <p>" . $activation_link . "</p>
            <p>This link will expire in " . (ACTIVATION_TOKEN_EXPIRY / 3600) . " hours.</p>
            <div class='footer'>
                <p>Best regards,<br>The " . APP_NAME . " Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Use PHPMailer if available, otherwise use default function
    if (hasPhpMailer()) {
        return sendEmailWithPhpMailer($user_email, $subject, $message);
    } else {
        return sendEmail($user_email, $subject, $message);
    }
}

// Send password reset email
function sendPasswordResetEmail($user_email, $user_name, $reset_token) {
    $reset_link = BASE_URL . '/reset-password?token=' . $reset_token . '&email=' . urlencode($user_email);
    
    $subject = "Password Reset - " . APP_NAME;
    
    $message = "
    <html>
    <head>
        <title>Password Reset</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; 
                     text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .footer { margin-top: 30px; font-size: 12px; color: #777; }
            .warning { color: #ff6600; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>" . APP_NAME . " - Password Reset</h2>
            <p>Hello " . htmlspecialchars($user_name) . ",</p>
            <p>You requested a password reset. Please click the button below to reset your password:</p>
            <p><a href='" . $reset_link . "' class='button'>Reset Your Password</a></p>
            <p>If the button doesn't work, copy and paste this URL into your browser:</p>
            <p>" . $reset_link . "</p>
            <p>This link will expire in " . (RESET_TOKEN_EXPIRY / 3600) . " hour(s).</p>
            <p class='warning'>If you didn't request this password reset, please ignore this email or contact support if you're concerned.</p>
            <div class='footer'>
                <p>Best regards,<br>The " . APP_NAME . " Team</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Use PHPMailer if available, otherwise use default function
    if (hasPhpMailer()) {
        return sendEmailWithPhpMailer($user_email, $subject, $message);
    } else {
        return sendEmail($user_email, $subject, $message);
    }
}
