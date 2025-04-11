<div class="row mb-4">
    <div class="col">
        <div class="d-sm-flex justify-content-between align-items-center">
            <h2 class="h3 mb-3 mb-sm-0">
                <i class="fas fa-sticky-note me-2 text-primary"></i>My Notes
            </h2>
            <div class="d-flex flex-wrap gap-2">
                <div class="input-group">
                    <input type="text" id="search-input" class="form-control" placeholder="Search notes..." 
                           value="<?= htmlspecialchars($data['search']) ?>">
                    <button id="clear-search" class="btn btn-outline-secondary" type="button" 
                            <?= empty($data['search']) ? 'style="display:none"' : '' ?>>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="btn-group">
                    <a href="<?= BASE_URL ?>/notes?view=grid<?= isset($_GET['label']) ? '&label=' . $_GET['label'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" 
                       class="btn <?= $data['view'] === 'grid' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="<?= BASE_URL ?>/notes?view=list<?= isset($_GET['label']) ? '&label=' . $_GET['label'] : '' ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>" 
                       class="btn <?= $data['view'] === 'list' ? 'btn-primary' : 'btn-outline-primary' ?>">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
                <a href="<?= BASE_URL ?>/notes/create" class="btn btn-success">
                    <i class="fas fa-plus me-1"></i> New Note
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sidebar with Labels -->
    <div class="col-md-3 mb-4">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h5 class="card-title mb-0">Labels</h5>
                <a href="<?= BASE_URL ?>/labels" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-cog"></i>
                </a>
            </div>
            <div class="list-group list-group-flush">
                <a href="<?= BASE_URL ?>/notes?view=<?= $data['view'] ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>"
                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= empty($data['current_label']) ? 'active' : '' ?>">
                    <span><i class="fas fa-sticky-note me-2"></i> All Notes</span>
                    <span class="badge bg-primary rounded-pill"><?= count($data['notes']) ?></span>
                </a>
                
                <?php if(isset($data['labels']) && is_array($data['labels'])): ?>
                    <?php foreach ($data['labels'] as $label): ?>
                        <a href="<?= BASE_URL ?>/notes?view=<?= $data['view'] ?>&label=<?= $label['id'] ?><?= isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '' ?>"
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= isset($data['current_label']) && $data['current_label'] == $label['id'] ? 'active' : '' ?>">
                            <span><i class="fas fa-tag me-2"></i> <?= htmlspecialchars($label['name']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if(isset($data['shared_notes']) && is_array($data['shared_notes']) && count($data['shared_notes']) > 0): ?>
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Shared</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="<?= BASE_URL ?>/notes/shared"
                       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-share-alt me-2"></i> Shared with me</span>
                        <span class="badge bg-info rounded-pill"><?= count($data['shared_notes']) ?></span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Main Notes Content -->
    <div class="col-md-9">
        <?php if (Session::hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= Session::getFlash('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (Session::hasFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= Session::getFlash('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($data['notes'])): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <!-- Empty state content -->
                </div>
            </div>
        <?php else: ?>
            <?php if ($data['view'] === 'grid'): ?>
                <!-- Grid View for Notes -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                    <?php foreach ($data['notes'] as $note): ?>
                        <div class="col">
                            <!-- Note card content -->
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- List View for Notes - Fix might be needed here -->
                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <!-- Table content -->
                    </div>
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
                
                // Check if BASE_URL is defined, if not, get it from window location
                const baseUrl = typeof BASE_URL !== 'undefined' ? BASE_URL : 
                               window.location.protocol + '//' + window.location.host + '/note_app';
                
                // Send AJAX request
                fetch(`${baseUrl}/notes/toggle-pin/${noteId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the page to reflect changes
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error pinning/unpinning the note. Please try again.');
                });
            });
        });
    }
    
    // Delete confirmation
    const deleteLinks = document.querySelectorAll('.delete-note');
    
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