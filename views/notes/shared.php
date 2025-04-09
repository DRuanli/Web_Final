<div class="notes-header">
    <div class="header-left">
        <h2>Notes Shared With Me</h2>
    </div>
    <div class="header-right">
        <div class="view-toggle">
            <a href="<?= BASE_URL ?>/notes/shared?view=grid" class="view-option <?= !isset($_GET['view']) || $_GET['view'] === 'grid' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i>
            </a>
            <a href="<?= BASE_URL ?>/notes/shared?view=list" class="view-option <?= isset($_GET['view']) && $_GET['view'] === 'list' ? 'active' : '' ?>">
                <i class="fas fa-list"></i>
            </a>
        </div>
        <a href="<?= BASE_URL ?>/notes" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Back to My Notes
        </a>
    </div>
</div>

<div class="notes-container">
    <div class="notes-content full-width">
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
                <div class="empty-icon"><i class="fas fa-share-alt"></i></div>
                <h3>No shared notes</h3>
                <p>You don't have any notes shared with you yet</p>
                <a href="<?= BASE_URL ?>/notes" class="btn btn-primary">Back to My Notes</a>
            </div>
        <?php else: ?>
            <?php if (!isset($_GET['view']) || $_GET['view'] === 'grid'): ?>
                <div class="notes-grid">
                    <?php foreach ($data['notes'] as $note): ?>
                        <div class="note-card shared-note">
                            <div class="note-content">
                                <h3 class="note-title">
                                    <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                        <a href="<?= BASE_URL ?>/notes/verify-password/<?= $note['id'] ?>?redirect=view">
                                            <?= htmlspecialchars($note['title']) ?>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= BASE_URL ?>/notes/view/<?= $note['id'] ?>">
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
                                
                                <div class="shared-by">
                                    <span>Shared by: <?= htmlspecialchars($note['owner_name']) ?></span>
                                    <?php if (isset($note['can_edit']) && $note['can_edit']): ?>
                                        <span class="can-edit" title="You can edit this note"><i class="fas fa-edit"></i></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="note-footer">
                                    <div class="note-indicators">
                                        <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                            <span class="indicator" title="Password Protected"><i class="fas fa-lock"></i></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="note-date">
                                        <?php 
                                        if (isset($note['shared_at'])) {
                                            $shared_at = new DateTime($note['shared_at']);
                                            
                                            if ($shared_at->format('Y-m-d') === date('Y-m-d')) {
                                                // Today, show time
                                                echo 'Shared today at ' . $shared_at->format('g:i A');
                                            } else if ($shared_at->format('Y-m-d') === date('Y-m-d', strtotime('-1 day'))) {
                                                // Yesterday
                                                echo 'Shared yesterday at ' . $shared_at->format('g:i A');
                                            } else {
                                                // Another day
                                                echo 'Shared on ' . $shared_at->format('M j, Y');
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
                                <th>Title</th>
                                <th>Content</th>
                                <th>Owner</th>
                                <th>Shared On</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['notes'] as $note): ?>
                                <tr>
                                    <td class="note-title">
                                        <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                            <a href="<?= BASE_URL ?>/notes/verify-password/<?= $note['id'] ?>?redirect=view">
                                                <?= htmlspecialchars($note['title']) ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/notes/view/<?= $note['id'] ?>">
                                                <?= htmlspecialchars($note['title']) ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                            <span class="indicator" title="Password Protected"><i class="fas fa-lock"></i></span>
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
                                    <td>
                                        <?= htmlspecialchars($note['owner_name']) ?>
                                        <?php if (isset($note['can_edit']) && $note['can_edit']): ?>
                                            <span class="can-edit" title="You can edit this note"><i class="fas fa-edit"></i></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (isset($note['shared_at'])) {
                                            $shared_at = new DateTime($note['shared_at']);
                                            echo $shared_at->format('M j, Y g:i A');
                                        }
                                        ?>
                                    </td>
                                    <td class="note-actions">
                                        <div class="action-buttons">
                                            <a href="<?= BASE_URL ?>/notes/view/<?= $note['id'] ?>" class="view-note" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (isset($note['can_edit']) && $note['can_edit']): ?>
                                                <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>" class="edit-note" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
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

<style>
.full-width {
    width: 100%;
}
.shared-by {
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.can-edit {
    color: #4a89dc;
}
</style>