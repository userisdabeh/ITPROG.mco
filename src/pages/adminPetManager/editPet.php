<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../../server/db.php';
require_once '../../api/image.php';

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

    // Handle image upload
    $imageResult = imageUpload('pet_image');
    
    if ($imageResult['error']) {
        echo "Error: " . $imageResult['error'];
        exit;
    }

    // Prepare SQL based on whether image was uploaded
    if ($imageResult['image_data']) {
        // Update with new image
        $sql = "UPDATE pets SET 
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
            is_featured = ?,
            pet_image = ?,
            pet_image_type = ?
            WHERE id = ?";
            
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        $stmt->bind_param("siidissssssiiiiissi", 
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
            $imageResult['image_data'],
            $imageResult['image_type'],
            $id
        );
    } else {
        // Update without changing image
        $sql = "UPDATE pets SET 
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
            WHERE id = ?";
            
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        $stmt->bind_param("siidissssssiiiiii", 
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
    }

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit;
    } else {
        echo "Error executing statement: " . $stmt->error;
        echo "<br>SQL Error: " . $conn->error;
        exit;
    }
} else {
    echo "Invalid request method";
}
?>
