<?php
session_start();
require __DIR__ . '/../../../server/db.php'; // adjust if needed

// Simple auth gate (optional)
if (!isset($_SESSION['user_id'])) {
    header('Location: /ITPROG.mco/src/pages/RegistrationAndLogin/login.php');
    exit;
}

// ---- Filters / Search ----
$q        = trim($_GET['q'] ?? '');
$type_id  = $_GET['type']  ?? '';
$breed_id = $_GET['breed'] ?? '';
$age_min  = $_GET['age_min'] ?? '';
$age_max  = $_GET['age_max'] ?? '';

// Build SQL
$sql = "
SELECT  p.*,
        b.breed_name,
        pt.type_name,
        (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) AS age_in_months,
        (SELECT photo_path FROM pet_photos WHERE pet_id = p.id AND is_primary = 1 LIMIT 1) AS primary_photo
FROM pets p
LEFT JOIN breeds b     ON b.id = p.breed_id
JOIN pet_types pt      ON pt.id = p.pet_type_id
WHERE p.status = 'available'
";

$params = [];
$types  = '';

if ($q !== '') {
    $sql   .= " AND (p.name LIKE ? OR b.breed_name LIKE ?)";
    $like   = "%$q%";
    $params[] = $like; $params[] = $like;
    $types  .= 'ss';
}

if ($type_id !== '') {
    $sql     .= " AND p.pet_type_id = ?";
    $params[] = $type_id;
    $types   .= 'i';
}

if ($breed_id !== '') {
    $sql     .= " AND p.breed_id = ?";
    $params[] = $breed_id;
    $types   .= 'i';
}

if ($age_min !== '') {
    $sql     .= " AND (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) >= ?";
    $params[] = (int)$age_min;
    $types   .= 'i';
}

if ($age_max !== '') {
    $sql     .= " AND (COALESCE(p.age_years,0) * 12 + COALESCE(p.age_months,0)) <= ?";
    $params[] = (int)$age_max;
    $types   .= 'i';
}

$sql .= " ORDER BY p.intake_date DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// For the Filters dropdowns
$typesRes  = $conn->query("SELECT id, type_name FROM pet_types WHERE is_active = 1 ORDER BY type_name");
$breedsRes = $conn->query("SELECT id, breed_name FROM breeds WHERE is_active = 1 ORDER BY breed_name");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Pets - Wonderpets</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="../../components/petCard/petCard.css">
</head>
<body>
<?php include __DIR__ . '/../landingPage/components/header/userHeader.php'; ?>

<main class="container">
    <section class="hero-heading">
        <h1>Find Your Perfect Pet</h1>
        <p>Discover amazing pets waiting for their forever homes</p>
    </section>

    <!-- Search + Filters -->
    <form class="search-filter" method="GET" action="">
        <div class="search-box">
            <span class="icon">🔍</span>
            <input
                type="text"
                name="q"
                placeholder="Search by name or breed…"
                value="<?= htmlspecialchars($q) ?>"
            />
        </div>

        <button type="button" class="filters-toggle" onclick="document.getElementById('filters').classList.toggle('open')">
            &#128295; Filters
        </button>

        <div id="filters" class="filters <?= (isset($_GET['type']) || isset($_GET['breed']) || isset($_GET['age_min']) || isset($_GET['age_max'])) ? 'open' : '' ?>">
            <label>
                Type
                <select name="type">
                    <option value="">Any</option>
                    <?php while ($row = $typesRes->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= $type_id == $row['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['type_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </label>

            <label>
                Breed
                <select name="breed">
                    <option value="">Any</option>
                    <?php while ($row = $breedsRes->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>" <?= $breed_id == $row['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($row['breed_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </label>

            <label>
                Min Age (months)
                <input type="number" name="age_min" min="0" value="<?= htmlspecialchars($age_min) ?>">
            </label>

            <label>
                Max Age (months)
                <input type="number" name="age_max" min="0" value="<?= htmlspecialchars($age_max) ?>">
            </label>

            <button type="submit" class="btn-apply">Apply</button>
            <a href="home.php" class="btn-clear">Clear</a>
        </div>
    </form>

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

</body>
</html>
