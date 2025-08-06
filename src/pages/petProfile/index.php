<?php
session_start();
require '../../../server/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../RegistrationAndLogin/login.php');
    exit;
}

// Get pet ID from URL
$pet_id = $_GET['id'] ?? null;

if (!$pet_id || !is_numeric($pet_id)) {
    header('Location: ../home/index.php');
    exit;
}

// Get pet details with related information
$pet_id = (int)$pet_id; // Sanitize input
$sql = "
SELECT  p.*,
        b.breed_name,
        pt.type_name,
        (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) AS age_in_months
FROM pets p
LEFT JOIN breeds b ON b.id = p.breed_id
JOIN pet_types pt ON pt.id = p.pet_type_id
WHERE p.id = $pet_id
";

$result = $conn->query($sql);
$pet = $result ? $result->fetch_assoc() : null;

if (!$pet) {
    header('Location: ../home/index.php');
    exit;
}

// Get additional photos, if any
$photos_sql = "SELECT photo_path, photo_name, is_primary FROM pet_photos WHERE pet_id = $pet_id ORDER BY is_primary DESC, display_order ASC";
$photos_result = $conn->query($photos_sql);
$photos = $photos_result ? $photos_result->fetch_all(MYSQLI_ASSOC) : [];

// Check if user has already applied for this pet
$user_id = (int)$_SESSION['user_id']; // Sanitize input
$application_sql = "SELECT id FROM adoption_applications WHERE user_id = $user_id AND pet_id = $pet_id";
$app_result = $conn->query($application_sql);
$existing_application = $app_result ? $app_result->fetch_assoc() : null;

// Check if pet is favorited by user
$favorite_sql = "SELECT id FROM user_favorites WHERE user_id = $user_id AND pet_id = $pet_id";
$fav_result = $conn->query($favorite_sql);
$is_favorited = $fav_result && $fav_result->num_rows > 0;

$conn->close();

// Format age display
$age_years = (int)($pet['age_years'] ?? 0);
$age_months = (int)($pet['age_months'] ?? 0);
$age_display = '';
if ($age_years > 0) {
    $age_display .= $age_years . ' year' . ($age_years > 1 ? 's' : '');
    if ($age_months > 0) {
        $age_display .= ' and ' . $age_months . ' month' . ($age_months > 1 ? 's' : '');
    }
} else {
    $age_display = $age_months . ' month' . ($age_months > 1 ? 's' : '');
}

// Handle image display
$main_image = '';
if (!empty($pet['pet_image'])) {
    if (!empty($pet['pet_image_type'])) {
        $main_image = 'data:' . $pet['pet_image_type'] . ';base64,' . base64_encode($pet['pet_image']);
    }
} else if (!empty($photos) && !empty($photos[0]['photo_path'])) {
    $main_image = $photos[0]['photo_path'];
} else {
    $main_image = 'https://placehold.co/600x400?text=No+Image+Available';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pet['name']) ?> - Pet Profile | Wonderpets</title>
    <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include '../../components/userHeader.php'; ?>

    <main class="container">
        <!-- Breadcrumb Navigation -->
        <nav class="breadcrumb">
            <a href="../home/index.php">Browse Pets</a>
            <span class="separator">›</span>
            <span class="current"><?= htmlspecialchars($pet['name']) ?></span>
        </nav>

        <div class="pet-profile">
            <!-- Main Content -->
            <div class="profile-main">
                <!-- Image Gallery -->
                <div class="image-gallery">
                    <div class="main-image">
                        <img src="<?= $main_image ?>" alt="<?= htmlspecialchars($pet['name']) ?>" id="mainImage">
                        
                        <?php if ($pet['is_featured']): ?>
                            <div class="featured-badge">⭐ Featured</div>
                        <?php endif; ?>
                        
                        <form method="post" action="../../api/favorites.php" class="favorite-form">
                            <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>">
                            <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                            <button type="submit" class="favorite-btn <?= $is_favorited ? 'favorited' : '' ?>" 
                                    aria-label="<?= $is_favorited ? 'Remove from favorites' : 'Add to favorites' ?>"
                                    title="<?= $is_favorited ? 'Remove from favorites' : 'Add to favorites' ?>">
                                <svg class="heart-icon" viewBox="0 0 24 24" <?= $is_favorited ? 'fill="currentColor"' : 'fill="none"' ?>>
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    
                    <?php if (!empty($photos) && count($photos) > 1): ?>
                        <div class="image-thumbnails">
                            <?php foreach ($photos as $index => $photo): ?>
                                <img src="<?= htmlspecialchars($photo['photo_path']) ?>" 
                                     alt="<?= htmlspecialchars($pet['name']) ?> photo <?= $index + 1 ?>"
                                     class="thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                     onclick="changeMainImage(this.src, this)">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pet Information -->
                <div class="pet-info">
                    <div class="pet-header">
                        <h1 class="pet-name"><?= htmlspecialchars($pet['name']) ?></h1>
                        <div class="pet-meta">
                            <span class="badge badge-primary pet-type <?= strtolower($pet['type_name']) ?>"><?= htmlspecialchars($pet['type_name']) ?></span>
                            <span class="badge status-badge <?= strtolower(str_replace('_', '-', $pet['status'])) ?>">
                                <?php
                                switch($pet['status']) {
                                    case 'available':
                                        echo 'Available for Adoption';
                                        break;
                                    case 'pending':
                                        echo 'Adoption Pending';
                                        break;
                                    case 'adopted':
                                        echo 'Already Adopted';
                                        break;
                                    case 'medical_hold':
                                        echo 'Medical Hold';
                                        break;
                                    case 'unavailable':
                                        echo 'Temporarily Unavailable';
                                        break;
                                    default:
                                        echo ucfirst(str_replace('_', ' ', $pet['status']));
                                }
                                ?>
                            </span>
                        </div>
                    </div>

                    <!-- Info Facts -->
                    <div class="facts">
                        <div class="fact-item">
                            <span class="label">Breed</span>
                            <span class="value"><?= htmlspecialchars($pet['breed_name'] ?? 'Mixed Breed') ?></span>
                        </div>
                        <div class="fact-item">
                            <span class="label">Age</span>
                            <span class="value"><?= $age_display ?></span>
                        </div>
                        <div class="fact-item">
                            <span class="label">Gender</span>
                            <span class="value">
                                <span class="pet-gender <?= strtolower($pet['gender']) ?>">
                                    <?= htmlspecialchars($pet['gender']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="fact-item">
                            <span class="label">Size</span>
                            <span class="value"><?= htmlspecialchars($pet['size'] ?? 'Not specified') ?></span>
                        </div>
                        <?php if ($pet['weight']): ?>
                        <div class="fact-item">
                            <span class="label">Weight</span>
                            <span class="value"><?= htmlspecialchars($pet['weight']) ?> kg</span>
                        </div>
                        <?php endif; ?>
                        <?php if ($pet['color']): ?>
                        <div class="fact-item">
                            <span class="label">Color</span>
                            <span class="value"><?= htmlspecialchars($pet['color']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Description -->
                    <?php if ($pet['description']): ?>
                    <div class="description-section">
                        <p class="description"><?= nl2br(htmlspecialchars($pet['description'])) ?></p>
                    </div>
                    <?php endif; ?>

                    
                    <!-- Personality & Behavior -->
                    <?php if ($pet['personality_traits'] || $pet['energy_level'] || $pet['good_with_kids'] !== null || $pet['good_with_pets'] !== null): ?>
                    <div class="personality-section">
                        <h3>🐾 Personality & Behavior</h3>
                        
                        <?php if ($pet['personality_traits']): ?>
                        <div class="trait-card">
                            <div class="trait-header">
                                <span class="trait-icon">✨</span>
                                <span class="trait-label">Personality Traits</span>
                            </div>
                            <div class="trait-tags">
                                <?php 
                                $traits = preg_split('/[,.]/', $pet['personality_traits']);
                                foreach ($traits as $trait): 
                                    $trait = trim($trait);
                                    if (!empty($trait)):
                                ?>
                                    <span class="trait-tag"><?= htmlspecialchars($trait) ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($pet['energy_level']): ?>
                        <div class="energy-card">
                            <div class="trait-header">
                                <span class="trait-icon">⚡</span>
                                <span class="trait-label">Energy Level</span>
                            </div>
                            <div class="energy-display">
                                <span class="energy-level-text <?php 
                                    $energy_level = strtolower($pet['energy_level']);
                                    $energy_class = 'low';
                                    if (strpos($energy_level, 'low') !== false) {
                                        $energy_class = 'low';
                                    } elseif (strpos($energy_level, 'moderate') !== false || strpos($energy_level, 'medium') !== false) {
                                        $energy_class = 'medium';
                                    } elseif (strpos($energy_level, 'high') !== false) {
                                        $energy_class = 'high';
                                    }
                                    echo $energy_class;
                                ?>"><?= htmlspecialchars($pet['energy_level']) ?></span>
                                <div class="energy-bar">
                                    <?php 
                                    $energy_value = 1;
                                    if (strpos($energy_level, 'low') !== false) $energy_value = 1;
                                    elseif (strpos($energy_level, 'moderate') !== false || strpos($energy_level, 'medium') !== false) $energy_value = 2;
                                    elseif (strpos($energy_level, 'high') !== false) $energy_value = 3;
                                    elseif (strpos($energy_level, 'very high') !== false) $energy_value = 4;
                                    
                                    for ($i = 1; $i <= 4; $i++): 
                                    ?>
                                        <div class="energy-dot <?= $i <= $energy_value ? 'active ' . $energy_class : '' ?>"></div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="compatibility-grid">
                            <?php if ($pet['good_with_kids'] !== null): ?>
                            <div class="compatibility-item">
                                <span class="compat-icon"><?= $pet['good_with_kids'] ? '✅' : '❌' ?></span>
                                <span>Good with Kids</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($pet['good_with_pets'] !== null): ?>
                            <div class="compatibility-item">
                                <span class="compat-icon"><?= $pet['good_with_pets'] ? '✅' : '❌' ?></span>
                                <span>Good with Other Pets</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($pet['is_house_trained'] !== null): ?>
                            <div class="compatibility-item">
                                <span class="compat-icon"><?= $pet['is_house_trained'] ? '✅' : '❌' ?></span>
                                <span>House Trained</span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($pet['is_spayed_neutered'] !== null): ?>
                            <div class="compatibility-item">
                                <span class="compat-icon"><?= $pet['is_spayed_neutered'] ? '✅' : '❌' ?></span>
                                <span>Spayed/Neutered</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Health Information -->
                    <?php if ($pet['health_conditions'] || $pet['special_needs']): ?>
                    <div class="health-section">
                        <h3>🏥 Health Information</h3>
                        
                        <?php if ($pet['health_conditions']): ?>
                        <div class="health-item health">
                            <span class="health-label">Health Conditions:</span>
                            <span class="health-value"><?= nl2br(htmlspecialchars($pet['health_conditions'])) ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($pet['special_needs']): ?>
                        <div class="health-item needs">
                            <span class="health-label">Special Needs:</span>
                            <span class="health-value"><?= nl2br(htmlspecialchars($pet['special_needs'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="profile-sidebar">
                <!-- Adoption Card -->
                <div class="adoption-card">
                    <div class="adoption-header">
                        <?php if ($pet['status'] === 'available'): ?>
                            <h3>Ready to Adopt?</h3>
                            <?php if ($pet['adoption_fee'] > 0): ?>
                                <div class="adoption-fee">
                                    Adoption Fee: <strong>₱<?= number_format($pet['adoption_fee'], 2) ?></strong>
                                </div>
                            <?php endif; ?>
                        <?php elseif ($pet['status'] === 'adopted'): ?>
                            <h3>🎉 Great News!</h3>
                            <p class="adoption-message"><?= htmlspecialchars($pet['name']) ?> has found a loving home!</p>
                        <?php elseif ($pet['status'] === 'pending'): ?>
                            <h3>📝 Adoption in Progress</h3>
                            <p class="adoption-message"><?= htmlspecialchars($pet['name']) ?> is currently under review for adoption.</p>
                        <?php elseif ($pet['status'] === 'medical_hold'): ?>
                            <h3>🏥 Medical Care</h3>
                            <p class="adoption-message"><?= htmlspecialchars($pet['name']) ?> is receiving medical care and will be available soon.</p>
                        <?php else: ?>
                            <h3>Currently Unavailable</h3>
                            <p class="adoption-message"><?= htmlspecialchars($pet['name']) ?> is temporarily unavailable for adoption.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="adoption-actions">
                        <?php if ($pet['status'] === 'available'): ?>
                            <?php if ($existing_application): ?>
                                <div class="application-status">
                                    <span class="status-icon">✅</span>
                                    <span>Application Submitted</span>
                                    <a href="../userApplication/index.php?id=<?= $existing_application['id'] ?>" class="view-application">
                                        View Status
                                    </a>
                                </div>
                            <?php else: ?>
                                <a href="../petApplication/index.php?pet_id=<?= $pet['id'] ?>" class="btn btn-lg btn-success">
                                    Apply for Adoption
                                </a>
                                <p class="adoption-note">
                                    Start your adoption journey by filling out our application form.
                                </p>
                            <?php endif; ?>
                        <?php else: ?>
                            <p class="status-note">
                                <?php if ($pet['status'] === 'adopted'): ?>
                                    Thank you to everyone who showed interest in <?= htmlspecialchars($pet['name']) ?>!
                                <?php else: ?>
                                    Check back later or browse other available pets.
                                <?php endif; ?>
                            </p>
                            <a href="../home/index.php" class="btn btn-lg btn-primary">Browse Available Pets</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="contact-info">
                        <h4>Questions?</h4>
                        <p>Contact our adoption team for more information about <?= htmlspecialchars($pet['name']) ?>.</p>
                        <a href="mailto:adoption@wonderpets.com" class="contact-link">
                            📧 adoption@wonderpets.com
                        </a>
                        <a href="tel:+1234567890" class="contact-link">
                            📞 +1 (234) 567-8890
                        </a>
                    </div>
                </div>

                <!-- Additional Info -->
                <div class="additional-info">
                    <h4>Additional Information</h4>
                    <div class="info-item">
                        <span class="info-label">Intake Date:</span>
                        <span class="info-value"><?= date('F j, Y', strtotime($pet['intake_date'])) ?></span>
                    </div>
                    <?php if ($pet['microchip_id']): ?>
                    <div class="info-item">
                        <span class="info-label">Microchip ID:</span>
                        <span class="info-value"><?= htmlspecialchars($pet['microchip_id']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <span class="info-label">Pet ID:</span>
                        <span class="info-value">#<?= $pet['id'] ?></span>
                    </div>
                </div>

                <!-- Share -->
                <div class="share-section">
                    <h4>Share <?= htmlspecialchars($pet['name']) ?></h4>
                    <div class="share-buttons">
                        <button onclick="shareOnFacebook()" class="btn btn-sm btn-secondary share-btn facebook">Facebook</button>
                        <button onclick="shareOnTwitter()" class="btn btn-sm btn-secondary share-btn twitter">Twitter</button>
                        <button onclick="copyLink()" class="btn btn-sm btn-secondary share-btn link">Copy Link</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Image gallery functionality
        function changeMainImage(src, thumbnail) {
            document.getElementById('mainImage').src = src;
            
            // Update active thumbnail
            document.querySelectorAll('.thumbnail').forEach(thumb => thumb.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        // Share functionality
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent(`Check out ${document.querySelector('.pet-name').textContent} available for adoption!`);
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    </script>
</body>
</html>