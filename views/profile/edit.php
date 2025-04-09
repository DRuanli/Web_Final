<div class="profile-container">
    <div class="profile-header">
        <h2>Edit Profile</h2>
        <a href="<?= BASE_URL ?>/profile" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Profile
        </a>
    </div>
    
    <?php if (!empty($data['errors']['general'])): ?>
        <div class="alert alert-danger">
            <?= $data['errors']['general'] ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-card">
        <form method="POST" class="profile-form">
            <div class="form-group">
                <label for="display_name">Display Name</label>
                <input type="text" id="display_name" name="display_name" class="form-control" value="<?= htmlspecialchars($data['user']['display_name']) ?>" required>
                <?php if (!empty($data['errors']['display_name'])): ?>
                    <div class="invalid-feedback"><?= $data['errors']['display_name'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" class="form-control" value="<?= htmlspecialchars($data['user']['email']) ?>" disabled>
                <small class="form-text text-muted">Email cannot be changed.</small>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <a href="<?= BASE_URL ?>/profile" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
.profile-container {
    max-width: 800px;
    margin: 0 auto;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.profile-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.profile-form .form-group {
    margin-bottom: 1.5rem;
}

.profile-form label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
}

.text-muted {
    color: #6c757d;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>