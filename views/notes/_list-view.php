<div class="notes-list">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th width="60"></th>
                    <th>Title</th>
                    <th>Content</th>
                    <th>Labels</th>
                    <th width="180">Last Modified</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['notes'] as $note): ?>
                    <tr class="<?= isset($note['is_pinned']) && $note['is_pinned'] ? 'pinned' : '' ?>">
                        <td class="note-status">
                            <div class="note-indicators">
                                <?php if (isset($note['is_pinned']) && $note['is_pinned']): ?>
                                    <span class="indicator pinned" title="Pinned"><i class="fas fa-thumbtack"></i></span>
                                <?php endif; ?>
                                
                                <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                    <span class="indicator locked" title="Password Protected"><i class="fas fa-lock"></i></span>
                                <?php endif; ?>
                                
                                <?php if (isset($note['is_shared']) && $note['is_shared']): ?>
                                    <span class="indicator shared" title="Shared with others"><i class="fas fa-share-alt"></i></span>
                                <?php endif; ?>
                                
                                <?php if (isset($note['image_count']) && $note['image_count'] > 0): ?>
                                    <span class="indicator" title="<?= $note['image_count'] ?> image(s) attached">
                                        <i class="fas fa-image"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <strong>
                                <?php if (isset($note['is_password_protected']) && $note['is_password_protected']): ?>
                                    <a href="<?= BASE_URL ?>/notes/verify-password/<?= $note['id'] ?>">
                                        <?= htmlspecialchars($note['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>">
                                        <?= htmlspecialchars($note['title']) ?>
                                    </a>
                                <?php endif; ?>
                            </strong>
                        </td>
                        <td class="note-content">
                            <?php 
                            $content = isset($note['content']) ? $note['content'] : '';
                            $preview = strip_tags($content);
                            $preview = substr($preview, 0, 100);
                            if (strlen($content) > 100) $preview .= '...';
                            echo htmlspecialchars($preview);
                            ?>
                        </td>
                        <td>
                            <?php if (isset($note['labels']) && !empty($note['labels'])): ?>
                                <?php foreach ($note['labels'] as $label): ?>
                                    <span class="badge bg-light text-dark border me-1">
                                        <?= htmlspecialchars($label['name'] ?? '') ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <small class="text-muted">None</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small>
                                <?php 
                                if (isset($note['updated_at'])) {
                                    $updated = new DateTime($note['updated_at']);
                                    echo $updated->format('M j, Y g:i A');
                                }
                                ?>
                            </small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary pin-note" data-id="<?= $note['id'] ?>" title="<?= isset($note['is_pinned']) && $note['is_pinned'] ? 'Unpin' : 'Pin' ?>">
                                    <i class="fas fa-thumbtack <?= isset($note['is_pinned']) && $note['is_pinned'] ? 'text-primary' : '' ?>"></i>
                                </button>
                                <a href="<?= BASE_URL ?>/notes/edit/<?= $note['id'] ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/notes/share/<?= $note['id'] ?>" class="btn btn-outline-info" title="Share">
                                    <i class="fas fa-share-alt"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/notes/delete/<?= $note['id'] ?>" class="btn btn-outline-danger delete-note" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>