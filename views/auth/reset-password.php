<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?> - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/auth.css">
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><?= APP_NAME ?></h1>
                <h2><?= $data['step'] === 'request' ? 'Reset Password' : 'Set New Password' ?></h2>
                <p><?= $data['step'] === 'request' ? 'Enter your email to reset your password' : 'Create a new password for your account' ?></p>
            </div>
            
            <?php if (Session::hasFlash('success')): ?>
                <div class="alert alert-success">
                    <?= Session::getFlash('success') ?>
                </div>
            <?php endif; ?>
            
            <?php if (Session::hasFlash('error')): ?>
                <div class="alert alert-danger">
                    <?= Session::getFlash('error') ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($data['errors']['general'])): ?>
                <div class="alert alert-danger">
                    <?= $data['errors']['general'] ?>
                </div>
            <?php endif; ?>
            
            <?php if ($data['step'] === 'request'): ?>
                <!-- Password Reset Request Form -->
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-envelope"></i></span>
                            <input type="email" name="email" id="email" value="<?= htmlspecialchars($data['email']) ?>" required>
                        </div>
                        <?php if (!empty($data['errors']['email'])): ?>
                            <div class="error-message"><?= $data['errors']['email'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Reset Method Selection -->
                    <div class="form-group">
                        <label>Reset Method</label>
                        <div class="reset-methods">
                            <div class="reset-method-option">
                                <input type="radio" name="reset_method" id="method-link" value="link" 
                                       <?= (!isset($data['reset_method']) || $data['reset_method'] === 'link') ? 'checked' : '' ?>>
                                <label for="method-link">
                                    <i class="fas fa-link reset-icon"></i>
                                    <div class="reset-method-info">
                                        <div class="reset-method-title">Reset Link</div>
                                        <div class="reset-method-desc">Receive a password reset link via email</div>
                                    </div>
                                </label>
                            </div>
                            
                            <div class="reset-method-option">
                                <input type="radio" name="reset_method" id="method-otp" value="otp" 
                                       <?= (isset($data['reset_method']) && $data['reset_method'] === 'otp') ? 'checked' : '' ?>>
                                <label for="method-otp">
                                    <i class="fas fa-key reset-icon"></i>
                                    <div class="reset-method-info">
                                        <div class="reset-method-title">Verification Code (OTP)</div>
                                        <div class="reset-method-desc">Receive a one-time code via email</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit" class="btn btn-primary">Send Reset Instructions</button>
                    </div>
                </form>
            <?php else: ?>
                <!-- New Password Form -->
                <form method="POST" class="auth-form">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" name="password" id="password" required minlength="8">
                            <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if (!empty($data['errors']['password'])): ?>
                            <div class="error-message"><?= $data['errors']['password'] ?></div>
                        <?php endif; ?>
                        <div class="password-strength" id="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-icon"><i class="fas fa-lock"></i></span>
                            <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
                            <span class="toggle-password" onclick="togglePasswordVisibility('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        <?php if (!empty($data['errors']['confirm_password'])): ?>
                            <div class="error-message"><?= $data['errors']['confirm_password'] ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-action">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            <?php endif; ?>
            
            <div class="auth-footer">
                <p>
                    <a href="<?= BASE_URL ?>/login">
                        <i class="fas fa-arrow-left"></i> Back to Login
                    </a>
                </p>
            </div>
        </div>
    </div>
    
    <style>
    /* Additional styling for reset method options */
    .reset-methods {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 10px;
    }
    
    .reset-method-option {
        position: relative;
    }
    
    .reset-method-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        height: 0;
        width: 0;
    }
    
    .reset-method-option label {
        display: flex;
        align-items: center;
        padding: 15px;
        border: 1px solid #dcdfe6;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .reset-method-option input[type="radio"]:checked + label {
        border-color: #3498db;
        background-color: #f0f7ff;
    }
    
    .reset-icon {
        font-size: 24px;
        color: #3498db;
        margin-right: 15px;
        width: 30px;
        text-align: center;
    }
    
    .reset-method-info {
        flex: 1;
    }
    
    .reset-method-title {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .reset-method-desc {
        font-size: 12px;
        color: #7f8c8d;
    }
    </style>
    
    <script>
        function togglePasswordVisibility(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = passwordInput.parentElement.querySelector('.toggle-password i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
        
        <?php if ($data['step'] === 'new_password'): ?>
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('password-strength');
        
        passwordInput.addEventListener('input', function() {
            const value = passwordInput.value;
            let strength = 0;
            
            // Length check
            if (value.length >= 8) strength += 1;
            if (value.length >= 12) strength += 1;
            
            // Character type checks
            if (/[0-9]/.test(value)) strength += 1;
            if (/[a-z]/.test(value)) strength += 1;
            if (/[A-Z]/.test(value)) strength += 1;
            if (/[^A-Za-z0-9]/.test(value)) strength += 1;
            
            // Update the strength meter
            strengthMeter.innerHTML = '';
            for (let i = 0; i < 5; i++) {
                const bar = document.createElement('div');
                bar.className = 'strength-bar';
                if (i < strength) {
                    if (strength <= 2) {
                        bar.classList.add('weak');
                    } else if (strength <= 4) {
                        bar.classList.add('medium');
                    } else {
                        bar.classList.add('strong');
                    }
                }
                strengthMeter.appendChild(bar);
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>