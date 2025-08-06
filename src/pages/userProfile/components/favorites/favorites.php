<?php
// Get user's favorite pets
$user_id = (int)$_SESSION['user_id'];
$favorites_sql = "
    SELECT p.*,
           b.breed_name,
           pt.type_name,
           1 as is_favorited,
           uf.created_at as favorited_at
    FROM user_favorites uf
    JOIN pets p ON p.id = uf.pet_id
    LEFT JOIN breeds b ON b.id = p.breed_id
    JOIN pet_types pt ON pt.id = p.pet_type_id
    WHERE uf.user_id = $user_id
    ORDER BY uf.created_at DESC
";

$favorites_result = $conn->query($favorites_sql);
$favorites = $favorites_result ? $favorites_result->fetch_all(MYSQLI_ASSOC) : [];
?>

<head>
    <link rel="stylesheet" href="../../components/petCard/petCard.css?v=<?php echo time(); ?>">
</head>
<div class="tab-content">
    <div class="section-header">
        <h3>Favorites</h3>
        <span class="favorites-count" style="float: right;"><?= count($favorites) ?> pet<?= count($favorites) !== 1 ? 's' : '' ?></span>
    </div>
    <p class="section-subheader"> Keep checking back on your favorites - their availability might change!</p>

    <?php if (empty($favorites)): ?>
        <div class="empty-favorites">
            <div class="empty-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </div>
            <h4>No favorites yet</h4>
            <p>Start browsing pets and add some to your favorites!</p>
            <a href="../home/index.php" class="btn btn-md btn-primary">Browse Pets</a>
        </div>
    <?php else: ?>
        <div class="favorites-grid">
            <?php foreach ($favorites as $pet): ?>
                <?php include '../../components/petCard/petCard.php'; ?>
            <?php endforeach; ?>
        </div>
        </div>
    <?php endif; ?>
</div>
