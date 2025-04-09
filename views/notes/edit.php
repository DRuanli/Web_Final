<div class="note-editor-container">
    <form id="note-form" method="POST" action="<?= isset($data['note']['id']) ? BASE_URL . '/notes/update/' . $data['note']['id'] : BASE_URL . '/notes/store' ?>" enctype="multipart/form-data">
        <?php if (!empty($data['errors']['general'])): ?>
            <div class="alert alert-danger">
                <?= $data['errors']['general'] ?>
            </div>
        <?php endif; ?>
        
        <div class="editor-header">
            <div class="editor-title">
                <input type="text" name="title" id="note-title" placeholder="Note title" value="<?= htmlspecialchars($data['note']['title'] ?? '') ?>" required>
                <?php if (!empty($data['errors']['title'])): ?>
                    <div class="invalid-feedback"><?= $data['errors']['title'] ?></div>
                <?php endif; ?>
            </div>
            
            <div class="editor-actions">
                <button type="button" class="btn btn-outline" id="toggle-options">
                    <i class="fas fa-sliders-h"></i> Options
                </button>
                <a href="<?= BASE_URL ?>/notes" class="btn btn-outline">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <!-- Remove the explicit Save button to comply with auto-save requirement -->
            </div>
        </div>
        
        <div class="editor-options" style="display: none;">
            <div class="option-section">
                <h4>Labels</h4>
                <div class="label-selector">
                    <?php if (empty($data['labels'])): ?>
                        <p>No labels available. <a href="<?= BASE_URL ?>/labels">Create labels</a></p>
                    <?php else: ?>
                        <?php foreach ($data['labels'] as $label): ?>
                            <div class="checkbox-item">
                                <input type="checkbox" name="labels[]" id="label-<?= $label['id'] ?>" value="<?= $label['id'] ?>"
                                    <?= in_array($label['id'], $data['note']['labels'] ?? []) ? 'checked' : '' ?>>
                                <label for="label-<?= $label['id'] ?>"><?= htmlspecialchars($label['name']) ?></label>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="option-section">
                <h4>Attachments</h4>
                <div class="image-attachments">
                    <div class="file-upload">
                        <input type="file" name="images[]" id="note-images" multiple accept="image/*">
                        <label for="note-images">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Choose images to upload</span>
                        </label>
                    </div>
                    
                    <?php if (!empty($data['note']['images'])): ?>
                        <div class="attached-images">
                            <h5>Current Attachments</h5>
                            <div class="image-previews">
                                <?php foreach ($data['note']['images'] as $image): ?>
                                    <div class="image-preview">
                                        <img src="<?= UPLOADS_URL . '/' . $image['file_path'] ?>" alt="<?= htmlspecialchars($image['file_name']) ?>">
                                        <a href="<?= BASE_URL ?>/notes/delete-image/<?= $image['id'] ?>" class="delete-image" data-id="<?= $image['id'] ?>">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="editor-content">
            <textarea name="content" id="note-content" placeholder="Note content..."><?= htmlspecialchars($data['note']['content'] ?? '') ?></textarea>
        </div>
    </form>
</div>

<!-- Improved auto-save indicator element -->
<div id="save-status" class="save-indicator" style="display: none;">
    <i class="fas fa-circle-notch fa-spin" id="saving-icon"></i>
    <i class="fas fa-check" id="saved-icon" style="display: none;"></i>
    <span id="save-message">Saving...</span>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle options panel
        const toggleOptionsBtn = document.getElementById('toggle-options');
        const optionsPanel = document.querySelector('.editor-options');
        
        toggleOptionsBtn.addEventListener('click', function() {
            if (optionsPanel.style.display === 'none') {
                optionsPanel.style.display = 'block';
                toggleOptionsBtn.innerHTML = '<i class="fas fa-times"></i> Close Options';
            } else {
                optionsPanel.style.display = 'none';
                toggleOptionsBtn.innerHTML = '<i class="fas fa-sliders-h"></i> Options';
            }
        });
        
        // Preview uploaded images
        const imageInput = document.getElementById('note-images');
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                // Create preview container if it doesn't exist
                let previewContainer = document.querySelector('.upload-previews');
                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.className = 'upload-previews';
                    previewContainer.innerHTML = '<h5>Images to Upload</h5><div class="image-previews"></div>';
                    document.querySelector('.image-attachments').appendChild(previewContainer);
                }
                
                const previewsDiv = previewContainer.querySelector('.image-previews');
                previewsDiv.innerHTML = '';
                
                if (this.files.length > 0) {
                    for (let i = 0; i < this.files.length; i++) {
                        const file = this.files[i];
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            const preview = document.createElement('div');
                            preview.className = 'image-preview';
                            
                            reader.onload = function(e) {
                                preview.innerHTML = `
                                    <img src="${e.target.result}" alt="${file.name}">
                                    <span class="file-name">${file.name}</span>
                                `;
                            };
                            
                            reader.readAsDataURL(file);
                            previewsDiv.appendChild(preview);
                        }
                    }
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        }
        
        // Improved Auto-save functionality
        const noteForm = document.getElementById('note-form');
        const titleInput = document.getElementById('note-title');
        const contentInput = document.getElementById('note-content');
        const saveStatus = document.getElementById('save-status');
        const savingIcon = document.getElementById('saving-icon');
        const savedIcon = document.getElementById('saved-icon');
        const saveMessage = document.getElementById('save-message');
        
        let saveTimeout;
        let lastSavedContent = contentInput.value;
        let lastSavedTitle = titleInput.value;
        let autoSaveEnabled = true;
        
        // Add event listener for unload event to prevent data loss
        window.addEventListener('beforeunload', function(e) {
            if (autoSaveEnabled && 
                (lastSavedContent !== contentInput.value || lastSavedTitle !== titleInput.value) &&
                titleInput.value.trim() !== '') {
                // Auto-save before leaving page
                saveChanges();
                
                // Show warning if there are unsaved changes
                const message = 'You have unsaved changes. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            }
        });
        
        function showSaveStatus(status, message) {
            saveStatus.style.display = 'flex';
            
            if (status === 'saving') {
                savingIcon.style.display = 'inline-block';
                savedIcon.style.display = 'none';
                saveMessage.textContent = message || 'Saving...';
                saveStatus.classList.add('is-saving');
                saveStatus.classList.remove('is-saved');
            } else if (status === 'saved') {
                savingIcon.style.display = 'none';
                savedIcon.style.display = 'inline-block';
                saveMessage.textContent = message || 'Saved';
                saveStatus.classList.remove('is-saving');
                saveStatus.classList.add('is-saved');
                
                // Hide after 2 seconds
                setTimeout(() => {
                    saveStatus.style.opacity = '0';
                    setTimeout(() => {
                        saveStatus.style.display = 'none';
                        saveStatus.style.opacity = '1';
                    }, 300);
                }, 2000);
            } else if (status === 'error') {
                savingIcon.style.display = 'none';
                savedIcon.style.display = 'none';
                saveMessage.textContent = message || 'Error saving';
                saveStatus.classList.remove('is-saving');
                saveStatus.classList.remove('is-saved');
                saveStatus.classList.add('is-error');
                
                // Hide after 3 seconds
                setTimeout(() => {
                    saveStatus.style.opacity = '0';
                    setTimeout(() => {
                        saveStatus.style.display = 'none';
                        saveStatus.style.opacity = '1';
                        saveStatus.classList.remove('is-error');
                    }, 300);
                }, 3000);
            }
        }
        
        function saveChanges() {
            // Only auto-save if editing an existing note and content has changed
            if (window.location.href.includes('/edit/') && 
                (lastSavedContent !== contentInput.value || lastSavedTitle !== titleInput.value) &&
                titleInput.value.trim() !== '') {
                
                // Show saving indicator
                showSaveStatus('saving');
                
                const formData = new FormData(noteForm);
                
                // Send AJAX request
                fetch(noteForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update last saved content
                        lastSavedContent = contentInput.value;
                        lastSavedTitle = titleInput.value;
                        
                        // Show saved indicator
                        showSaveStatus('saved');
                    } else {
                        // Show error indicator
                        showSaveStatus('error', data.errors?.general || 'Error saving');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveStatus('error', 'Network error');
                });
            }
        }
        
        function autoSave() {
            // Clear any existing timeout
            clearTimeout(saveTimeout);
            
            // Set a new timeout to save after 1.5 seconds of inactivity
            saveTimeout = setTimeout(saveChanges, 1500);
        }
        
        // Add event listeners for auto-save
        titleInput.addEventListener('input', autoSave);
        contentInput.addEventListener('input', autoSave);
        
        // Add event listeners for label checkboxes
        const labelCheckboxes = document.querySelectorAll('input[name="labels[]"]');
        labelCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', autoSave);
        });
    });
</script>

<style>
/* Enhanced auto-save indicator styles */
.save-indicator {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: rgba(40, 40, 40, 0.8);
    color: white;
    padding: 8px 16px;
    border-radius: 50px;
    font-size: 14px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 9999;
    transition: opacity 0.3s;
}

.save-indicator i {
    font-size: 16px;
}

.save-indicator.is-saved {
    background-color: rgba(40, 167, 69, 0.8);
}

.save-indicator.is-error {
    background-color: rgba(220, 53, 69, 0.8);
}

/* Remove button styles from title */
.note-form .editor-title input {
    width: 100%;
    padding: 10px;
    font-size: 1.25rem;
    font-weight: 600;
    border: 1px solid #ddd;
    border-radius: 4px;
    transition: border-color 0.2s;
}

.note-form .editor-title input:focus {
    outline: none;
    border-color: #4a89dc;
    box-shadow: 0 0 0 3px rgba(74, 137, 220, 0.15);
}

/* Enhanced textarea styles */
.editor-content textarea {
    width: 100%;
    min-height: 400px;
    padding: 15px;
    border: none;
    resize: vertical;
    font-family: inherit;
    font-size: 1rem;
    line-height: 1.6;
}

.editor-content textarea:focus {
    outline: none;
}
</style>