<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => true, 'message' => 'Unauthorized']);
    exit;
}

require_once '../../../../server/db.php';

$search = $_GET['search'] ?? '';
$petType = $_GET['pet-type'] ?? '';
$breed = $_GET['pet-species'] ?? '';
$gender = $_GET['pet-gender'] ?? '';
$size = $_GET['pet-size'] ?? '';
$ageMin = $_GET['pet-age-min'] ?? '';
$ageMax = $_GET['pet-age-max'] ?? '';
$weightMin = $_GET['pet-weight-min'] ?? '';
$weightMax = $_GET['pet-weight-max'] ?? '';
$priceMin = $_GET['pet-price-min'] ?? '';
$priceMax = $_GET['pet-price-max'] ?? '';
$status = $_GET['pet-status'] ?? '';
$energy = $_GET['pet-energy'] ?? '';
$isSpayed = isset($_GET['pet-is-spayed-neutered']);
$isHouse = isset($_GET['pet-is-house-trained']);
$isGoodWithKids = isset($_GET['pet-is-good-with-children']);
$isGoodWithPets = isset($_GET['pet-is-good-with-other-pets']);
$isFeatured = isset($_GET['pet-featured-only']);

$sql = "SELECT 
            p.*, pt.type_name, b.breed_name 
        FROM pets p
        JOIN pet_types pt ON p.pet_type_id = pt.id
        JOIN breeds b ON p.breed_id = b.id
        WHERE 1 ";

$params = [];
$types = "";

// Search
if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.microchip_id LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

// Pet Type
if (!empty($petType)) {
    $sql .= " AND LOWER(pt.type_name) = ?";
    $params[] = strtolower($petType);
    $types .= "s";
}

// Breed
if (!empty($breed)) {
    $sql .= " AND LOWER(b.breed_name) = ?";
    $params[] = strtolower($breed);
    $types .= "s";
}

// Gender
if (!empty($gender)) {
    $sql .= " AND LOWER(p.gender) = ?";
    $params[] = ucfirst(strtolower($gender));
    $types .= "s";
}

// Size
if (!empty($size)) {
    $sql .= " AND LOWER(p.size) = ?";
    $params[] = ucfirst(strtolower($size));
    $types .= "s";
}

// Age (convert total months for comparison)
if ($ageMin !== '') {
    $sql .= " AND (p.age_years * 12 + p.age_months) >= ?";
    $params[] = intval($ageMin);
    $types .= "i";
}
if ($ageMax !== '') {
    $sql .= " AND (p.age_years * 12 + p.age_months) <= ?";
    $params[] = intval($ageMax);
    $types .= "i";
}

// Weight
if ($weightMin !== '') {
    $sql .= " AND p.weight >= ?";
    $params[] = floatval($weightMin);
    $types .= "d";
}
if ($weightMax !== '') {
    $sql .= " AND p.weight <= ?";
    $params[] = floatval($weightMax);
    $types .= "d";
}

// Price
if ($priceMin !== '') {
    $sql .= " AND p.adoption_fee >= ?";
    $params[] = floatval($priceMin);
    $types .= "d";
}
if ($priceMax !== '') {
    $sql .= " AND p.adoption_fee <= ?";
    $params[] = floatval($priceMax);
    $types .= "d";
}

// Status
if (!empty($status)) {
    $sql .= " AND LOWER(p.status) = ?";
    $params[] = strtolower($status);
    $types .= "s";
}

// Energy Level
if (!empty($energy)) {
    $sql .= " AND LOWER(p.energy_level) = ?";
    $params[] = ucfirst(strtolower($energy));
    $types .= "s";
}

// Checkboxes
if ($isSpayed) $sql .= " AND p.is_spayed_neutered = 1";
if ($isHouse) $sql .= " AND p.is_house_trained = 1";
if ($isGoodWithKids) $sql .= " AND p.good_with_kids = 1";
if ($isGoodWithPets) $sql .= " AND p.good_with_pets = 1";
if ($isFeatured) $sql .= " AND p.is_featured = 1";

$stmt = $conn->prepare($sql);
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$pets = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['error' => false, 'pets' => $pets]);
