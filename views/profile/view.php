<div class="profile-container">
    <div class="profile-header">
        <h2>My Profile</h2>
        <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>
    
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success" data-auto-dismiss="5000">
            <?= Session::getFlash('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger" data-auto-dismiss="5000">
            <?= Session::getFlash('error') ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-card">
        <div class="profile-info">
            <div class="info-group">
                <h3>Account Information</h3>
                
                <div class="info-item">
                    <span class="info-label">Display Name</span>
                    <span class="info-value"><?= htmlspecialchars($data['user']['display_name']) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value"><?= htmlspecialchars($data['user']['email']) ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Account Status</span>
                    <?php if ($data['user']['is_activated']): ?>
                        <span class="info-value status-active">Active</span>
                    <?php else: ?>
                        <span class="info-value status-pending">Pending Activation</span>
                    <?php endif; ?>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Member Since</span>
                    <span class="info-value">
                        <?php 
                        $created_at = new DateTime($data['user']['created_at']);
                        echo $created_at->format('F j, Y');
                        ?>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="<?= BASE_URL ?>/profile/change-password" class="btn btn-outline">
                <i class="fas fa-key"></i> Change Password
            </a>
            <a href="<?= BASE_URL ?>/profile/preferences" class="btn btn-outline">
                <i class="fas fa-cog"></i> Preferences
            </a>
        </div>
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

.profile-info {
    margin-bottom: 2rem;
}

.info-group {
    margin-bottom: 2rem;
}

.info-group h3 {
    margin-bottom: 1rem;
    font-size: 1.2rem;
    color: #333;
    border-bottom: 1px solid #eee;
    padding-bottom: 0.5rem;
}

.info-item {
    display: flex;
    margin-bottom: 1rem;
}

.info-label {
    width: 150px;
    font-weight: 600;
    color: #555;
}

.info-value {
    flex: 1;
    color: #333;
}

.status-active {
    color: #28a745;
}

.status-pending {
    color: #ffc107;
}

.profile-actions {
    display: flex;
    gap: 1rem;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .info-item {
        flex-direction: column;
    }
    
    .info-label {
        width: auto;
        margin-bottom: 0.25rem;
    }
    
    .profile-actions {
        flex-direction: column;
    }
}
</style>