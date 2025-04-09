<?php
// views/notes/index.php
?>

<div class="notes-header">
    <div class="header-left">
        <h2>My Notes</h2>
    </div>
    <div class="header-right">
        <div class="view-toggle">
            <a href="<?= BASE_URL ?>/notes?view=grid<?= isset($_GET['label']) ? '&label=' . $_GET['label'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="view-option <?= $data['view'] === 'grid' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i>
            </a>
            <a href="<?= BASE_URL ?>/notes?view=list<?= isset($_GET['label']) ? '&label=' . $_GET['label'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" class="view-option <?= $data['view'] === 'list' ? 'active' : '' ?>">
                <i class="fas fa-list"></i>
            </a>
        </div>
        <div class="search-container">
            <input type="text" id="search-input" placeholder="Search notes..." value="<?= htmlspecialchars($data['search']) ?>">
            <button id="clear-search" <?= empty($data['search']) ? 'style="display:none"' : '' ?>><i class="fas fa-times"></i></button>
        </div>
        <a href="<?= BASE_URL ?>/notes/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> New Note
        </a>
    </div>
</div>

<div class="notes-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Labels</h3>
            <a href="<?= BASE_URL ?>/labels" class="btn-manage-labels" title="Manage Labels">
                <i class="fas fa-cog"></i>
            </a>
        </div>
        <ul class="label-list">
            <li class="<?= empty($data['current_label']) ? 'active' : '' ?>">
                <a href="<?= BASE_URL ?>/notes?view=<?= $data['view'] ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                    <i class="fas fa-sticky-note"></i> All Notes
                </a>
            </li>
            <?php if(isset($data['labels']) && is_array($data['labels'])): ?>
                <?php foreach ($data['labels'] as $label): ?>
                    <li class="<?= isset($data['current_label']) && $data['current_label'] == $label['id'] ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>/notes?view=<?= $data['view'] ?>&label=<?= $label['id'] ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>">
                            <i class="fas fa-tag"></i> <?= htmlspecialchars($label['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
        
        <?php if(isset($data['shared_notes']) && is_array($data['shared_notes']) && count($data['shared_notes']) > 0): ?>
            <div class="sidebar-header mt-4">
                <h3>Shared Notes</h3>
            </div>
            <ul class="shared-list">
                <li>
                    <a href="<?= BASE_URL ?>/notes/shared">
                        <i class="fas fa-share-alt"></i> Shared with me
                        <span class="badge"><?= count($data['shared_notes']) ?></span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>
    </div>
    
    <div class="notes-content">
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
        
        <?php if (empty($data['notes'])): ?>
            <div class="empty-state">
                <?php if (!empty($data['search'])): ?>
                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                    <h3>No notes found</h3>
                    <p>No notes match your search "<?= htmlspecialchars($data['search']) ?>"</p>
                    <a href="<?= BASE_URL ?>/notes" class="btn btn-primary">Clear Search</a>
                <?php elseif (!empty($data['current_label'])): ?>
                    <div class="empty-icon"><i class="fas fa-tag"></i></div>
                    <h3>No notes with this label</h3>
                    <p>You don't have any notes with this label yet</p>
                    <a href="<?= BASE_URL ?>/notes/create" class="btn btn-primary">Create a Note</a>
                <?php else: ?>
                    <div class="empty-icon"><i class="fas fa-sticky-note"></i></div>
                    <h3>No notes yet</h3>
                    <p>Create your first note to get started</p>
                    <a href="<?= BASE_URL ?>/notes/create" class="btn btn-primary">Create a Note</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php if ($data['view'] === 'grid'): ?>
                <div class="notes-grid">
                    <?php foreach ($data['notes'] as $note): ?>
                        <div class="note-card <?= isset($note['is_pinned']) && $note['is_pinned'] ? 'pinned' : '' ?>">
                            <div class="note-actions">
                                <button class="pin-note" data-id="<?= $note['id'] ?>" title="<?= isset($note['is_pinned']) && $note['is_pinned'] ? 'Unpin' : 'Pin' ?>">
                                    <i class="fas fa-thumbtack <?= isset($note['is_pinned']) && $note['is_pinned'] ? 'pinned' : '' ?>"></i>
                                </button>
                                <div class="dropdown">
                                    <button class="dropdown-toggle">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="<?= BASE_URL ?>/notes/share/<?= $note['id'] ?>">
                                            <i class="fas fa-share-alt"></i> Share
                                        </a>
                                        <a href="<?= BASE_URL ?>/notes/toggle-password/<?= $note['id'] ?>">
                                            <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                                <i class="fas fa-unlock"></i> Remove Password
                                            <?php else: ?>
                                                <i class="fas fa-lock"></i> Add Password
                                            <?php endif; ?>
                                        </a>
                                        <a href="<?= BASE_URL ?>/notes/delete/<?= $note['id'] ?>" class="text-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="note-content">
                                <h3 class="note-title">
                                    <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                        <a href="<?= BASE_URL ?>/notes/verify-password/<?= $note['id'] ?>">
                                            <?= htmlspecialchars($note['title']) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>">
                                            <?= htmlspecialchars($note['title']) ?>
                                        </a>
                                    <?php endif; ?>
                                </h3>
                                
                                <div class="note-body">
                                    <?php 
                                    $preview = strip_tags($note['content']);
                                    $preview = substr($preview, 0, 150);
                                    if (strlen($note['content']) > 150) $preview .= '...';
                                    echo nl2br(htmlspecialchars($preview));
                                    ?>
                                </div>
                                
                                <?php if (isset($note['labels']) && !empty($note['labels'])): ?>
                                    <div class="note-labels">
                                        <?php foreach ($note['labels'] as $label): ?>
                                            <span class="label">
                                                <?= htmlspecialchars($label['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="note-footer">
                                    <div class="note-indicators">
                                        <?php if (isset($note['is_pinned']) && $note['is_pinned']): ?>
                                            <span class="indicator" title="Pinned"><i class="fas fa-thumbtack"></i></span>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                            <span class="indicator" title="Password Protected"><i class="fas fa-lock"></i></span>
                                        <?php endif; ?>
                                        
                                        <?php if (isset($note['image_count']) && $note['image_count'] > 0): ?>
                                            <span class="indicator" title="<?= $note['image_count'] ?> image(s) attached">
                                                <i class="fas fa-image"></i> <?= $note['image_count'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="note-date">
                                        <?php 
                                        if (isset($note['updated_at'])) {
                                            $updated = new DateTime($note['updated_at']);
                                            
                                            if ($updated->format('Y-m-d') === date('Y-m-d')) {
                                                // Today, show time
                                                echo 'Today at ' . $updated->format('g:i A');
                                            } else if ($updated->format('Y-m-d') === date('Y-m-d', strtotime('-1 day'))) {
                                                // Yesterday
                                                echo 'Yesterday at ' . $updated->format('g:i A');
                                            } else {
                                                // Another day
                                                echo $updated->format('M j, Y');
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="notes-list">
                    <table class="table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Title</th>
                                <th>Content</th>
                                <th>Labels</th>
                                <th>Last Modified</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['notes'] as $note): ?>
                                <tr class="<?= isset($note['is_pinned']) && $note['is_pinned'] ? 'pinned' : '' ?>">
                                    <td class="note-status">
                                        <div class="note-indicators">
                                            <?php if (isset($note['is_pinned']) && $note['is_pinned']): ?>
                                                <span class="indicator" title="Pinned"><i class="fas fa-thumbtack"></i></span>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                                <span class="indicator" title="Password Protected"><i class="fas fa-lock"></i></span>
                                            <?php endif; ?>
                                            
                                            <?php if (isset($note['image_count']) && $note['image_count'] > 0): ?>
                                                <span class="indicator" title="<?= $note['image_count'] ?> image(s) attached">
                                                    <i class="fas fa-image"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="note-title">
                                        <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                            <a href="<?= BASE_URL ?>/notes/verify-password/<?= $note['id'] ?>">
                                                <?= htmlspecialchars($note['title']) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>">
                                                <?= htmlspecialchars($note['title']) ?>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td class="note-content">
                                        <?php 
                                        $preview = strip_tags($note['content']);
                                        $preview = substr($preview, 0, 100);
                                        if (strlen($note['content']) > 100) $preview .= '...';
                                        echo htmlspecialchars($preview);
                                        ?>
                                    </td>
                                    <td class="note-labels">
                                        <?php if (isset($note['labels']) && !empty($note['labels'])): ?>
                                            <?php foreach ($note['labels'] as $label): ?>
                                                <span class="label">
                                                    <?= htmlspecialchars($label['name']) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="no-labels">None</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="note-date">
                                        <?php 
                                        if (isset($note['updated_at'])) {
                                            $updated = new DateTime($note['updated_at']);
                                            
                                            if ($updated->format('Y-m-d') === date('Y-m-d')) {
                                                // Today, show time
                                                echo 'Today at ' . $updated->format('g:i A');
                                            } else if ($updated->format('Y-m-d') === date('Y-m-d', strtotime('-1 day'))) {
                                                // Yesterday
                                                echo 'Yesterday at ' . $updated->format('g:i A');
                                            } else {
                                                // Another day
                                                echo $updated->format('M j, Y g:i A');
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="note-actions">
                                        <div class="action-buttons">
                                            <button class="pin-note" data-id="<?= $note['id'] ?>" title="<?= isset($note['is_pinned']) && $note['is_pinned'] ? 'Unpin' : 'Pin' ?>">
                                                <i class="fas fa-thumbtack <?= isset($note['is_pinned']) && $note['is_pinned'] ? 'pinned' : '' ?>"></i>
                                            </button>
                                            <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>" class="edit-note" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/notes/share/<?= $note['id'] ?>" class="share-note" title="Share">
                                                <i class="fas fa-share-alt"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/notes/delete/<?= $note['id'] ?>" class="delete-note" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('search-input');
    const clearSearchBtn = document.getElementById('clear-search');
    
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Show/hide clear button
            if (clearSearchBtn) {
                clearSearchBtn.style.display = this.value ? 'block' : 'none';
            }
            
            // Set timeout for search
            searchTimeout = setTimeout(function() {
                // Get current URL and update search parameter
                const url = new URL(window.location.href);
                if (searchInput.value) {
                    url.searchParams.set('search', searchInput.value);
                } else {
                    url.searchParams.delete('search');
                }
                
                // Navigate to the URL
                window.location.href = url.toString();
            }, 300); // 300ms delay for typing
        });
        
        // Clear search button
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                this.style.display = 'none';
                
                // Remove search parameter and reload
                const url = new URL(window.location.href);
                url.searchParams.delete('search');
                window.location.href = url.toString();
            });
        }
    }
    
    // Pin/unpin note functionality
    const pinButtons = document.querySelectorAll('.pin-note');
    
    if (pinButtons.length > 0) {
        pinButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                const noteId = this.getAttribute('data-id');
                const icon = this.querySelector('i');
                
                // Send AJAX request
                fetch(BASE_URL + '/notes/toggle-pin/' + noteId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Toggle pinned state visually
                        if (data.is_pinned) {
                            icon.classList.add('pinned');
                            this.setAttribute('title', 'Unpin');
                            
                            // Add pinned class to parent note card/row
                            if (this.closest('.note-card')) {
                                this.closest('.note-card').classList.add('pinned');
                            } else if (this.closest('tr')) {
                                this.closest('tr').classList.add('pinned');
                            }
                        } else {
                            icon.classList.remove('pinned');
                            this.setAttribute('title', 'Pin');
                            
                            // Remove pinned class from parent note card/row
                            if (this.closest('.note-card')) {
                                this.closest('.note-card').classList.remove('pinned');
                            } else if (this.closest('tr')) {
                                this.closest('tr').classList.remove('pinned');
                            }
                        }
                        
                        // Optional: Reload page to update note order
                        // window.location.reload();
                        
                        // Show success message
                        const successMsg = document.createElement('div');
                        successMsg.className = 'alert alert-success';
                        successMsg.setAttribute('data-auto-dismiss', '3000');
                        successMsg.textContent = data.message;
                        
                        // Insert at the top of notes-content
                        const notesContent = document.querySelector('.notes-content');
                        if (notesContent) {
                            notesContent.insertBefore(successMsg, notesContent.firstChild);
                            
                            // Auto dismiss
                            setTimeout(function() {
                                successMsg.style.opacity = '0';
                                setTimeout(function() {
                                    if (successMsg.parentNode) {
                                        successMsg.parentNode.removeChild(successMsg);
                                    }
                                }, 300);
                            }, 3000);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    }
    
    // Delete confirmation
    const deleteLinks = document.querySelectorAll('a.delete-note');
    
    if (deleteLinks.length > 0) {
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this note? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    }
});
</script>