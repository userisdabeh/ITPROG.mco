<?php
$name = htmlspecialchars($pet['name'] ?? '');
$type = htmlspecialchars($pet['type_name'] ?? $pet['type'] ?? '');
$breed = htmlspecialchars($pet['breed_name'] ?? $pet['breed'] ?? '');
$age_years = (int)($pet['age_years'] ?? 0);
$age_months = (int)($pet['age_months'] ?? 0);
$total_age_months = $age_years * 12 + $age_months;
$age_display = $age_years > 0 ? $age_years . ' year' . ($age_years > 1 ? 's' : '') : $age_months . ' month' . ($age_months > 1 ? 's' : '');
$gender = htmlspecialchars($pet['gender'] ?? '');
$description = htmlspecialchars($pet['description'] ?? '');
$petId = htmlspecialchars($pet['id'] ?? '');
$is_featured = !empty($pet['is_featured']) && $pet['is_featured'];

$image_src = '';
if (!empty($pet['pet_image'])) {
    if (!empty($pet['pet_image_type'])) {
        // Binary image data from database
        $image_src = 'data:' . $pet['pet_image_type'] . ';base64,' . base64_encode($pet['pet_image']);
    } else {
        // Image path
        $image_src = htmlspecialchars($pet['pet_image']);
    }
} else {
    // Default placeholder image
    $image_src = 'https://placehold.co/300x300?text=No+Image';
}
?>

<div class='pet-card' data-pet-id='<?= $petId ?>'>
    <?php if ($is_featured) { ?>
        <div class='featured'></div>
    <?php } ?>
    <div class='pet-image-container'>
        <img src='<?= $image_src ?>' alt='<?= $name ?>' class='pet-image' />
        <form method="post" action="../../api/favorites.php" class="favorite-form">
            <input type="hidden" name="pet_id" value="<?= $petId ?>">
            <button type="submit" class='favorite-btn' aria-label='Add to favorites'>
                <svg class='heart-icon' viewBox='0 0 24 24' fill='none'>
                    <path d='M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/>
                </svg>
            </button>
        </form>
    </div>
    
    <div class='pet-info'>
        <div class='pet-header'>
            <h3 class='pet-name'><?= $name ?></h3>
            <span class='pet-type'><?= ucfirst($type) ?></span>
        </div>
        
        <div class='pet-details'>
            <span class='detail-item'><?= $breed ?></span>
            <span class='detail-separator'>•</span>
            <span class='detail-item'><?= $age_display ?></span>
            <span class='detail-separator'>•</span>
            <span class='detail-item'><?= ucfirst($gender) ?></span>
        </div>
        
        <p class='pet-description'><?= $description ?></p>
        
        <div class='pet-actions'>
            <a href='../petProfile/index.php?id=<?= $petId ?>' class='btn btn-sm btn-ghost'>
                View Details
            </a>
            <a href='../adoptionApplication/index.php?pet_id=<?= $petId ?>' class='btn btn-sm btn-primary'>
                Adopt Me
            </a>
        </div>
    </div>
</div>