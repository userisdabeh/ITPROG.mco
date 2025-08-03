<?php
session_start();
require '../../../server/db.php';

// Simple auth gate (optional)
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// ---- Filters / Search ----
$q        = trim($_GET['q'] ?? '');
$type_id  = $_GET['type']  ?? '';
$breed_id = $_GET['breed'] ?? '';
$age_min  = $_GET['age_min'] ?? '';
$age_max  = $_GET['age_max'] ?? '';
$gender   = $_GET['gender'] ?? '';
$size     = $_GET['size'] ?? '';
$featured_only = isset($_GET['featured']);

// Build SQL with WHERE clause
$sql = "
SELECT  p.*,
        b.breed_name,
        pt.type_name,
        (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) AS age_in_months,
        (SELECT photo_path FROM pet_photos WHERE pet_id = p.id AND is_primary = 1 LIMIT 1) AS primary_photo
FROM pets p
LEFT JOIN breeds b     ON b.id = p.breed_id
JOIN pet_types pt      ON pt.id = p.pet_type_id
WHERE 1=1
";

if ($q !== '') {
    $escaped_q = $conn->real_escape_string($q);
    $sql .= " AND (p.name LIKE '%$escaped_q%' OR b.breed_name LIKE '%$escaped_q%' OR p.description LIKE '%$escaped_q%')";
}

if ($type_id !== '') {
    $type_id = (int)$type_id;
    $sql .= " AND p.pet_type_id = $type_id";
}

if ($breed_id !== '') {
    $breed_id = (int)$breed_id;
    $sql .= " AND p.breed_id = $breed_id";
}

if ($gender !== '') {
    $escaped_gender = $conn->real_escape_string($gender);
    $sql .= " AND p.gender = '$escaped_gender'";
}

if ($size !== '') {
    $escaped_size = $conn->real_escape_string($size);
    $sql .= " AND p.size = '$escaped_size'";
}

if ($age_min !== '') {
    $age_min = (int)$age_min;
    $sql .= " AND (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) >= $age_min";
}

if ($age_max !== '') {
    $age_max = (int)$age_max;
    $sql .= " AND (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) <= $age_max";
}

if ($featured_only) {
    $sql .= " AND p.is_featured = 1";
}

$sql .= " ORDER BY p.is_featured DESC, p.intake_date DESC";

$result = $conn->query($sql);
$pets = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

// filter dropdowns
$typesRes  = $conn->query("SELECT id, type_name FROM pet_types WHERE is_active = 1 ORDER BY type_name");
$breedsRes = $conn->query("SELECT id, breed_name FROM breeds WHERE is_active = 1 ORDER BY breed_name");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Pets - Wonderpets</title>
    <link rel="stylesheet" href="index.css?v=<?php echo time() ?>">
    <link rel="stylesheet" href="../../components/petCard/petCard.css?v=<?php echo time(); ?>">
</head>
<body>
<?php include __DIR__ . '/../../components/userHeader.php'; ?>

<main class="container">
    <section class="hero">
        <h1>Find Your Perfect Pet</h1>
        <p>Discover amazing pets waiting for their forever homes</p>
    </section>

    <!-- Search + Filters -->
    <div class="search-filters">
        <form method="GET" class="filters-form" action="">
            <div class="search-section">
                <div class="search-box">
                    <span class="icon">🔍</span>
                    <input
                        type="text"
                        name="q"
                        class="input input-sm"
                        placeholder="Search by name, breed, or description..."
                        value="<?= htmlspecialchars($q) ?>"
                    />
                </div>
                
                <button type="button" class="filters-toggle" id="filtersToggle" onclick="toggleFilters()">
                    <span class="toggle-icon">&#128295;</span>
                    <span class="toggle-text">Filters</span>
                    <span class="filter-count"><?= count(array_filter([$type_id, $breed_id, $gender, $size, $age_min, $age_max, $featured_only])) ?></span>
                </button>
            </div>

            <div id="filters" class="filters-panel <?= (isset($_GET['type']) || isset($_GET['breed']) || isset($_GET['gender']) || isset($_GET['size']) || isset($_GET['age_min']) || isset($_GET['age_max']) || isset($_GET['featured'])) ? 'open' : '' ?>">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="type" class="form-label">Pet Type</label>
                        <select name="type" id="type" class="input input-sm select">
                            <option value="">All Types</option>
                            <?php 
                            $typesRes->data_seek(0);
                            while ($type = $typesRes->fetch_assoc()): 
                            ?>
                                <option value="<?= $type['id'] ?>" <?= $type_id == $type['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['type_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="breed" class="form-label">Breed</label>
                        <select name="breed" id="breed" class="input input-sm select">
                            <option value="">All Breeds</option>
                            <?php 
                            $breedsRes->data_seek(0);
                            while ($breed = $breedsRes->fetch_assoc()): 
                            ?>
                                <option value="<?= $breed['id'] ?>" <?= $breed_id == $breed['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($breed['breed_name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="gender" class="form-label">Gender</label>
                        <select name="gender" id="gender" class="input input-sm select">
                            <option value="">Any Gender</option>
                            <option value="Male" <?= ($gender ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= ($gender ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                            <option value="Unknown" <?= ($gender ?? '') === 'Unknown' ? 'selected' : '' ?>>Unknown</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="size" class="form-label">Size</label>
                        <select name="size" id="size" class="input input-sm select">
                            <option value="">Any Size</option>
                            <option value="Small" <?= ($size ?? '') === 'Small' ? 'selected' : '' ?>>Small</option>
                            <option value="Medium" <?= ($size ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                            <option value="Large" <?= ($size ?? '') === 'Large' ? 'selected' : '' ?>>Large</option>
                            <option value="Extra Large" <?= ($size ?? '') === 'Extra Large' ? 'selected' : '' ?>>Extra Large</option>
                        </select>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-group">
                        <label for="age_min" class="form-label">Min Age (months)</label>
                        <input type="number" name="age_min" id="age_min" min="0" max="300" 
                               class="input input-sm"
                               value="<?= htmlspecialchars($age_min) ?>">
                    </div>

                    <div class="filter-group">
                        <label for="age_max" class="form-label">Max Age (months)</label>
                        <input type="number" name="age_max" id="age_max" min="0" max="300" 
                               class="input input-sm"
                               value="<?= htmlspecialchars($age_max) ?>">
                    </div>

                    <div class="filter-group">
                        <div class="checkbox-group">
                            <label class="checkbox-wrapper" for="featured">
                                <input type="checkbox" name="featured" id="featured" class="checkbox" <?= isset($_GET['featured']) ? 'checked' : '' ?>>
                                <span>Featured Pets Only</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="btn btn-sm btn-primary">Apply Filters</button>
                    <a href="index.php" class="btn btn-sm btn-ghost">Clear All</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Pet Cards Grid -->
    <section class="pets-grid">
        <?php if (!$pets): ?>
            <p class="empty">No pets found. Try adjusting filters.</p>
        <?php else: ?>
            <?php foreach ($pets as $pet): ?>
                <?php include __DIR__ . '/../../components/petCard/petCard.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<script>
function toggleFilters() {
    const filtersPanel = document.getElementById('filters');
    const filtersToggle = document.getElementById('filtersToggle');
    const toggleIcon = filtersToggle.querySelector('.toggle-icon');
    
    filtersPanel.classList.toggle('open');
    filtersToggle.classList.toggle('active');
    
    if (filtersPanel.classList.contains('open')) {
        toggleIcon.innerHTML = '&#9650;';
    } else {
        toggleIcon.innerHTML = '&#128295;';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const filtersPanel = document.getElementById('filters');
    const filtersToggle = document.getElementById('filtersToggle');
    
    if (filtersPanel.classList.contains('open')) {
        filtersToggle.classList.add('active');
        filtersToggle.querySelector('.toggle-icon').innerHTML = '&#9650;';
    }
});
</script>

</body>
</html>
