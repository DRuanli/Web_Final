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
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save
                </button>
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
        
        // Auto-save functionality
        const noteForm = document.getElementById('note-form');
        const titleInput = document.getElementById('note-title');
        const contentInput = document.getElementById('note-content');
        
        let saveTimeout;
        let lastSavedContent = contentInput.value;
        let lastSavedTitle = titleInput.value;
        
        function autoSave() {
            // Only auto-save if editing an existing note and content has changed
            if (
                window.location.href.includes('/edit/') && 
                (lastSavedContent !== contentInput.value || lastSavedTitle !== titleInput.value) &&
                titleInput.value.trim() !== ''
            ) {
                // Clear any existing timeout
                clearTimeout(saveTimeout);
                
                // Set a new timeout to save after 2 seconds of inactivity
                saveTimeout = setTimeout(function() {
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
                            
                            // Show save indicator
                            const saveIndicator = document.createElement('div');
                            saveIndicator.className = 'save-indicator';
                            saveIndicator.innerHTML = '<i class="fas fa-check"></i> Saved';
                            document.body.appendChild(saveIndicator);
                            
                            // Remove indicator after 2 seconds
                            setTimeout(() => {
                                saveIndicator.style.opacity = '0';
                                setTimeout(() => {
                                    document.body.removeChild(saveIndicator);
                                }, 300);
                            }, 2000);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }, 2000);
            }
        }
        
        // Add event listeners for auto-save
        titleInput.addEventListener('input', autoSave);
        contentInput.addEventListener('input', autoSave);
    });
</script>