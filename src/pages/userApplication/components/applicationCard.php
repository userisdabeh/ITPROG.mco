<?php
function renderApplicationCard($application, $statusBadge, $isCompleted = false, $canWithdraw = false) {
    $completedClass = $isCompleted ? 'completed' : '';
    ?>
    <div class="application-card <?= $statusBadge['class'] ?> <?= $completedClass ?>">
        <div class="card-header">
            <div class="status-indicator" style="background: <?= $statusBadge['color'] ?>;">
                <span class="status-icon"><?= $statusBadge['icon'] ?></span>
            </div>
            <div class="header-content">
                <h4 class="status-label"><?= htmlspecialchars($statusBadge['title']) ?></h4>
                <p class="application-date">Applied <?= formatStatusDate($application['created_at']) ?></p>
            </div>
        </div>
        
        <div class="card-body">
            <!-- pet info -->
            <div class="pet-info-section">
                <div class="pet-avatar">
                    <?php if ($application['image_data']): ?>
                        <img src="data:<?= htmlspecialchars($application['image_type']) ?>;base64,<?= base64_encode($application['image_data']) ?>" 
                             alt="<?= htmlspecialchars($application['pet_name']) ?>" class="pet-photo">
                    <?php else: ?>
                        <div class="pet-placeholder">
                            <img src="https://placehold.co/300x300?text=No+Image" 
                                 alt="No Image" class="pet-photo">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="pet-details-modern">
                    <h3 class="pet-title"><?= htmlspecialchars($application['pet_name']) ?></h3>
                    <div class="pet-tags">
                        <span class="tag primary-tag"><?= htmlspecialchars($application['type_name']) ?></span>
                        <span class="tag"><?= htmlspecialchars($application['breed_name'] ?? 'Mixed') ?></span>
                        <span class="tag"><?= $application['age_years'] ?>y <?= $application['age_months'] ?>m</span>
                    </div>
                </div>
            </div>

            <!-- status details -->
            <div class="status-details">
                <p class="status-message"><?= htmlspecialchars($statusBadge['description']) ?></p>

                <!-- info badges -->
                <?php 
                $hasInfoPills = ($application['status'] === 'interview_required' && $application['interview_scheduled_at']) || 
                               ($application['status'] === 'completed' && $application['completed_at']);
                $rowClass = $hasInfoPills ? 'has-pills' : 'no-pills';
                ?>
                <div class="info-actions-row <?= $rowClass ?>">
                    <div class="info-pills">
                        <!-- additional info -->
                        <?php if ($application['status'] === 'interview_required' && $application['interview_scheduled_at']): ?>
                            <div class="info-badge interview-badge">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
                                </svg>
                                Interview: <?= formatStatusDate($application['interview_scheduled_at']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($application['status'] === 'completed' && $application['completed_at']): ?>
                            <div class="info-badge completion-badge">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                </svg>
                                Completed <?= formatStatusDate($application['completed_at']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="action-buttons">
                        <?php if ($application['pet_status'] === 'available' || !$isCompleted): ?>
                            <a href="../petProfile/index.php?id=<?= $application['pet_id'] ?>" class="btn btn-secondary btn-sm">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                    <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.292-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.292c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z"/>
                                </svg>
                                View Pet
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($canWithdraw): ?>
                            <button type="button" class="btn btn-danger btn-sm withdraw-btn" 
                                    data-application-id="<?= $application['id'] ?>" 
                                    data-pet-name="<?= htmlspecialchars($application['pet_name']) ?>">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                </svg>
                                Withdraw
                            </button>
                        <?php endif; ?>
                        
                        <?php if ($application['status'] === 'denied'): ?>
                            <a href="../home/index.php" class="btn btn-primary btn-sm">
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                                </svg>
                                Find Other Pets
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php
}
?>
