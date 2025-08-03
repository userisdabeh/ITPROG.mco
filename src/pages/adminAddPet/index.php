<?php
    session_start();
    include_once '../../../server/db.php';

    if(!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $getPetTypesQuery = "SELECT * FROM pet_types";
    $getPetBreedsQuery = "SELECT * FROM breeds";

    try {
        $petTypesResult = $conn->query($getPetTypesQuery);
        $petTypes = $petTypesResult->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pet_name = $_POST['pet-name'];
        $pet_type = $_POST['pet-type'];
        $pet_breed = $_POST['pet-breed'];
        $pet_age_years = $_POST['pet-age-years'];
        $pet_age_months = $_POST['pet-age-months'];
        $pet_gender = $_POST['pet-gender'];
        $pet_size = $_POST['pet-size'];
        $pet_weight = $_POST['pet-weight'];
        $pet_color = $_POST['pet-color'];
        $pet_description = $_POST['pet-description'];
        $pet_image = $_FILES['pet-image'];
        $pet_personality_traits = $_POST['pet-personality-traits'];
        $pet_energy_level = $_POST['pet-energy-level'];
        $pet_is_good_with_children = isset($_POST['pet-is-good-with-children']) ? 1 : 0;
        $pet_is_good_with_other_pets = isset($_POST['pet-is-good-with-other-pets']) ? 1 : 0;
        $pet_is_house_trained = isset($_POST['pet-is-house-trained']) ? 1 : 0;
        $pet_medical_conditions = $_POST['pet-medical-conditions'];
        $pet_special_needs = $_POST['pet-special-needs'];
        $pet_is_spayed_neutered = isset($_POST['pet-is-spayed/neutered']) ? 1 : 0;
        $pet_adoption_fee = $_POST['pet-adoption-fee'];
        $pet_intake_date = $_POST['pet-intake-date'];
        $pet_is_featured = isset($_POST['pet-is-featured']) ? 1 : 0;

        $image = addslashes(file_get_contents($_FILES['pet-image']['tmp_name']));
        $image_type = $_FILES['pet-image']['type'];

        $insertNewPetQuery = "INSERT INTO pets (
            name,
            pet_type_id,
            breed_id,
            age_years,
            age_months,
            gender,
            size,
            weight,
            color,
            personality_traits,
            health_conditions,
            special_needs,
            description,
            adoption_fee,
            is_spayed_neutered,
            is_house_trained,
            good_with_kids,
            good_with_pets,
            energy_level,
            intake_date,
            is_featured,
            pet_image,
            pet_image_type) VALUES (
            '$pet_name',
            $pet_type,
            $pet_breed,
            $pet_age_years,
            $pet_age_months,
            '$pet_gender',
            '$pet_size',
            '$pet_weight',
            '$pet_color',
            '$pet_personality_traits',
            '$pet_medical_conditions',
            '$pet_special_needs',
            '$pet_description',
            $pet_adoption_fee,
            $pet_is_spayed_neutered,
            $pet_is_house_trained,
            $pet_is_good_with_children,
            $pet_is_good_with_other_pets,
            '$pet_energy_level',
            '$pet_intake_date',
            $pet_is_featured,
            '$image',
            '$image_type')";
        
        $insertNewPetQuery = mysqli_query($conn, $insertNewPetQuery);        

        if($insertNewPetQuery) {
            echo "<script>alert('Pet added successfully'); window.location.href='../adminDashboard'</script>";
        } else {
            echo "<script>alert('Failed to add pet');</script>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>
        <script src="index.js?v=<?php echo time(); ?>" defer></script>
        <title>Wonderpets - Add New Pet</title>
    </head>
    <body>
        <?php include '../../components/admin/nav.php' ?>
        <main>
            <div class="main-header mb-4">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <i class="bi bi-arrow-left"></i>
                    Back
                </a>
                <h3>Add New Pet</h3>
            </div>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Pet Information</legend>
                    <div class="mb-3">
                        <label for="pet-name" class="form-label">Pet Name</label>
                        <input type="text" name="pet-name" id="pet-name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pet-type" class="form-label">Pet Type</label>
                        <select name="pet-type" id="pet-type" class="form-select" required>
                            <option value="">Select Type</option>
                            <?php foreach ($petTypes as $type) : ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo $type['type_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-breed" class="form-label">Pet Breed</label>
                        <select name="pet-breed" id="pet-breed" class="form-select" required>
                            <option value="">Select Breed</option>
                        </select>
                    </div>
                    <div class="row mb-3 g-3">
                        <div class="col">
                            <label for="pet-age-years" class="form-label">Pet Age (Years)</label>
                            <input type="number" name="pet-age-years" id="pet-age-years" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="pet-age-months" class="form-label">Pet Age (Months)</label>
                            <input type="number" name="pet-age-months" id="pet-age-months" class="form-control" required>
                        </div>
                    </div>
                    <div class="row mb-3 g-3">
                        <div class="col">
                            <label for="pet-gender" class="form-label">Pet Gender</label>
                            <select name="pet-gender" id="pet-gender" class="form-select" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="unknown">Unknown</option>
                            </select>
                        </div>
                        <div class="col">
                            <label for="pet-size" class="form-label">Pet Size</label>
                            <select name="pet-size" id="pet-size" class="form-select" required>
                                <option value="">Select Size</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3 g-3">
                        <div class="col">
                            <label for="pet-weight" class="form-label">Pet Weight (kg)</label>
                            <input type="number" name="pet-weight" id="pet-weight" class="form-control" required>
                        </div>
                        <div class="col">
                            <label for="pet-color" class="form-label">Pet Color</label>
                            <input type="text" name="pet-color" id="pet-color" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pet-description" class="form-label">Pet Description</label>
                        <textarea name="pet-description" id="pet-description" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pet-image" class="form-label">Pet Image</label>
                        <input type="file" name="pet-image" id="pet-image" class="form-control" accept="image/*" required>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Personality and Behavior</legend>
                    <div class="mb-3">
                        <label for="pet-personality-traits" class="form-label">Pet Personality Traits</label>
                        <textarea name="pet-personality-traits" id="pet-personality-traits" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pet-energy-level" class="form-label">Pet Energy Level</label>
                        <select name="pet-energy-level" id="pet-energy-level" class="form-select" required>
                            <option value="">Select Energy Level</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3 g-3">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="pet-is-good-with-children" id="pet-is-good-with-children" class="form-check-input">
                            <label for="pet-is-good-with-children" class="form-check-label">Good with Children</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-good-with-other-pets" id="pet-is-good-with-other-pets" class="form-check-input">
                            <label for="pet-is-good-with-other-pets" class="form-check-label">Good with Other Pets</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-house-trained" id="pet-is-house-trained" class="form-check-input">
                            <label for="pet-is-house-trained" class="form-check-label">House Trained</label>
                        </div>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Health and Medical Information</legend>
                    <div class="mb-3">
                        <label for="pet-medical-conditions" class="form-label">Pet Medical Conditions</label>
                        <textarea name="pet-medical-conditions" id="pet-medical-conditions" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pet-special-needs" class="form-label">Pet Special Needs</label>
                        <textarea name="pet-special-needs" id="pet-special-needs" class="form-control"></textarea>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="pet-is-spayed/neutered" id="pet-is-spayed/neutered" class="form-check-input">
                        <label for="pet-is-spayed/neutered" class="form-check-label">Spayed/Neutered</label>
                    </div>
                </fieldset>
                <fieldset>
                    <legend>Adoption Information</legend>
                    <div class="mb-3">
                        <label for="pet-adoption-fee" class="form-label">Pet Adoption Fee</label>
                        <input type="number" name="pet-adoption-fee" id="pet-adoption-fee" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pet-intake-date" class="form-label">Pet Intake Date</label>
                        <input type="date" name="pet-intake-date" id="pet-intake-date" class="form-control" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="pet-is-featured" id="pet-is-featured" class="form-check-input">
                        <label for="pet-is-featured" class="form-check-label">Featured</label>
                    </div>
                </fieldset>
                <div class="submit-buttons">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='javascript:history.back();'">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Pet</button>
                </div>
            </form>
        </main>
    </body>
</html>