<div class="profile-container">
    <div class="profile-header">
        <h2>User Preferences</h2>
        <a href="<?= BASE_URL ?>/profile" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to Profile
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
    
    <?php if (!empty($data['errors']['general'])): ?>
        <div class="alert alert-danger">
            <?= $data['errors']['general'] ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-card">
        <form method="POST" class="preferences-form">
            <div class="preferences-section">
                <h3>Appearance</h3>
                
                <div class="form-group">
                    <label>Theme</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="theme-light" name="theme" value="light" checked>
                            <label for="theme-light">Light</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="theme-dark" name="theme" value="dark">
                            <label for="theme-dark">Dark</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="theme-system" name="theme" value="system">
                            <label for="theme-system">System Default</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Font Size</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="font-small" name="font_size" value="small">
                            <label for="font-small">Small</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="font-medium" name="font_size" value="medium" checked>
                            <label for="font-medium">Medium</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="font-large" name="font_size" value="large">
                            <label for="font-large">Large</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="preferences-section">
                <h3>Note Display</h3>
                
                <div class="form-group">
                    <label>Default Note View</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="view-grid" name="default_view" value="grid" checked>
                            <label for="view-grid">Grid View</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="view-list" name="default_view" value="list">
                            <label for="view-list">List View</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Note Color</label>
                    <div class="color-selector">
                        <div class="color-option">
                            <input type="radio" id="color-white" name="note_color" value="white" checked>
                            <label for="color-white" class="color-label" style="background-color: #fff;"></label>
                        </div>
                        <div class="color-option">
                            <input type="radio" id="color-blue" name="note_color" value="blue">
                            <label for="color-blue" class="color-label" style="background-color: #e6f2ff;"></label>
                        </div>
                        <div class="color-option">
                            <input type="radio" id="color-green" name="note_color" value="green">
                            <label for="color-green" class="color-label" style="background-color: #e6fff2;"></label>
                        </div>
                        <div class="color-option">
                            <input type="radio" id="color-yellow" name="note_color" value="yellow">
                            <label for="color-yellow" class="color-label" style="background-color: #fffde6;"></label>
                        </div>
                        <div class="color-option">
                            <input type="radio" id="color-purple" name="note_color" value="purple">
                            <label for="color-purple" class="color-label" style="background-color: #f2e6ff;"></label>
                        </div>
                        <div class="color-option">
                            <input type="radio" id="color-pink" name="note_color" value="pink">
                            <label for="color-pink" class="color-label" style="background-color: #ffe6f2;"></label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="preferences-section">
                <h3>Other Settings</h3>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="auto-save" name="auto_save" value="1" checked>
                        <label for="auto-save">Enable auto-save when editing notes</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-item">
                        <input type="checkbox" id="email-notifications" name="email_notifications" value="1" checked>
                        <label for="email-notifications">Receive email notifications when notes are shared with me</label>
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Preferences
                </button>
                <button type="reset" class="btn btn-outline">
                    <i class="fas fa-undo"></i> Reset to Defaults
                </button>
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

.preferences-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.preferences-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.preferences-section h3 {
    margin-bottom: 1.25rem;
    font-size: 1.1rem;
    color: #333;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.radio-group {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.radio-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.color-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.color-option {
    position: relative;
}

.color-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.color-label {
    display: block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 1px solid #ddd;
    cursor: pointer;
    position: relative;
}

.color-option input[type="radio"]:checked + .color-label::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #4a89dc;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

@media (max-width: 768px) {
    .profile-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>