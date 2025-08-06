<?php
require_once '../../../server/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $ageYears = $_POST['age_years'];
    $ageMonths = $_POST['age_months'];
    $weight = $_POST['weight'];

    $pet_type_id = $_POST['pet_type_id'];
    $breed_id = $_POST['breed_id'];
    $gender = $_POST['gender'];
    $size = $_POST['size'];
    $status = $_POST['status'];
    $energy_level = $_POST['energy_level'];
    $description = $_POST['description'];

    // Checkboxes: convert to 1 or 0 (sent only if checked)
    $is_spayed_neutered = isset($_POST['is_spayed_neutered']) ? 1 : 0;
    $is_house_trained = isset($_POST['is_house_trained']) ? 1 : 0;
    $good_with_kids = isset($_POST['good_with_kids']) ? 1 : 0;
    $good_with_pets = isset($_POST['good_with_pets']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE pets SET 
        name = ?, 
        age_years = ?, 
        age_months = ?, 
        weight = ?, 
        pet_type_id = ?, 
        breed_id = ?, 
        gender = ?, 
        size = ?, 
        status = ?, 
        energy_level = ?, 
        description = ?, 
        is_spayed_neutered = ?, 
        is_house_trained = ?, 
        good_with_kids = ?, 
        good_with_pets = ?, 
        is_featured = ? 
        WHERE id = ?");

    $stmt->bind_param("iiidissssssiiiiii", 
        $name, 
        $ageYears, 
        $ageMonths, 
        $weight, 
        $pet_type_id, 
        $breed_id, 
        $gender, 
        $size, 
        $status, 
        $energy_level, 
        $description, 
        $is_spayed_neutered, 
        $is_house_trained, 
        $good_with_kids, 
        $good_with_pets, 
        $is_featured, 
        $id
    );

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Error updating pet.";
    }
}
?>
