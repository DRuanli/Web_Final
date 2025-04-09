<?php
// Get any flash messages
$errors = Session::getFlash('errors', []);
$old_input = Session::getFlash('old_input', []);
$success = Session::getFlash('success', '');
$error = Session::getFlash('error', '');

// Include header
$pageTitle = "Register";
include VIEWS_PATH . '/components/header.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Create an Account</h2>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($old_input['email'] ?? '') ?>" 
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="display_name">Display Name</label>
                <input 
                    type="text" 
                    id="display_name" 
                    name="display_name" 
                    class="form-control <?= isset($errors['display_name']) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($old_input['display_name'] ?? '') ?>" 
                    required
                >
                <?php if (isset($errors['display_name'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['display_name']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                    required
                >
                <?php if (isset($errors['password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>"
                    required
                >
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['confirm_password']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="<?= BASE_URL ?>/login">Login</a></p>
        </div>
    </div>
</div>

<?php 
// Include footer
include VIEWS_PATH . '/components/footer.php';
?>