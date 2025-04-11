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
                        <a href="<?= BASE_URL ?>/notes/delete/<?= $note['id'] ?>" class="delete-note text-danger">
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
                    $content = isset($note['content']) ? $note['content'] : '';
                    $preview = strip_tags($content);
                    $preview = substr($preview, 0, 150);
                    if (strlen($content) > 150) $preview .= '...';
                    echo nl2br(htmlspecialchars($preview));
                    ?>
                </div>
                
                <?php if (isset($note['labels']) && !empty($note['labels'])): ?>
                    <div class="note-labels">
                        <?php foreach ($note['labels'] as $label): ?>
                            <span class="label">
                                <?= htmlspecialchars($label['name'] ?? '') ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>