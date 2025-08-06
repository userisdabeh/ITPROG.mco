<?php
    session_start();
    $activeAdminPage = 'pets';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    include_once '../../../server/db.php';

    try {
        $getPets = $conn->prepare("SELECT 
                                        p.id,
                                        p.microchip_id,
                                        p.name,
                                        pt.type_name,
                                        b.breed_name,
                                        p.age_years,
                                        p.age_months,
                                        p.gender,
                                        p.size,
                                        p.weight,
                                        p.is_spayed_neutered,
                                        p.is_house_trained,
                                        p.good_with_kids,
                                        p.good_with_pets,
                                        p.energy_level,
                                        p.status,
                                        p.is_featured
                                    FROM pets p
                                    JOIN pet_types pt ON p.pet_type_id = pt.id
                                    JOIN breeds b ON b.pet_type_id = pt.id;");
        if ($getPets) {
            $getPets->execute();
            $result = $getPets->get_result();
            $pets = $result->fetch_all(MYSQLI_ASSOC);
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wonderpets Admin Pet Manager</title>

        <!-- For Bootstrap Icons, Modals, and other functionalities -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>

        <!-- For the global admin styles -->
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">

        <!-- For the navigation bar -->
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">

        <script src="index.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <?php include '../../components/admin/nav.php' ?>
        <main>
            <section class="main-header mb-3">
                <div class="main-header-title">
                    <h3>Pet Manager</h3>
                    <p>Manage pet records and documents.</p>
                </div>
                <div class="main-header-actions">
                    <a href="../adminAddPet" class="btn btn-primary">Add Pet</a>
                </div>
            </section>
            <form method="get" id="filter-form">
                <h3 class="mb-4">Filter Pets</h3>
                <div class="search-container mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="search" class="form-control" placeholder="Search pets" name="search">
                    </div>
                </div>
                <div class="filter-container mb-3">
                    <div class="mb-3">
                        <label for="pet-type">Pet Type</label>
                        <select name="pet-type" id="pet-type" class="form-select">
                            <option value="">All</option>
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                            <option value="bird">Birds</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-species">Pet Species</label>
                        <select name="pet-species" id="pet-species" class="form-select">
                            <option value="">All</option>
                            <option value="pitbull">Pitbull</option>
                            <option value="persian">Persian</option>
                            <option value="siamese">Siamese</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-age">Age (Months)</label>
                        <div class="input-group">
                            <input type="number" name="pet-age-min" id="pet-age-min" class="form-control" placeholder="Min" min="0">
                            <span class="input-group-text">to</span>
                            <input type="number" name="pet-age-max" id="pet-age-max" class="form-control" placeholder="Max" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pet-gender">Gender</label>
                        <select name="pet-gender" id="pet-gender" class="form-select">
                            <option value="">All</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-size">Size</label>
                        <select name="pet-size" id="pet-size" class="form-select">
                            <option value="">All</option>
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-weight">Weight (kg)</label>
                        <div class="input-group">
                            <input type="number" name="pet-weight-min" id="pet-weight-min" class="form-control" placeholder="Min" min="0">
                            <span class="input-group-text">to</span>
                            <input type="number" name="pet-weight-max" id="pet-weight-max" class="form-control" placeholder="Max" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pet-price">Adoption Fee (₱)</label>
                        <div class="input-group">
                            <input type="number" name="pet-price-min" id="pet-price-min" class="form-control" placeholder="Min" min="0">
                            <span class="input-group-text">to</span>
                            <input type="number" name="pet-price-max" id="pet-price-max" class="form-control" placeholder="Max" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="pet-status">Status</label>
                        <select name="pet-status" id="pet-status" class="form-select">
                            <option value="">All</option>
                            <option value="available">Available</option>
                            <option value="adopted">Adopted</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pet-energy">Energy Level</label>
                        <select name="pet-energy" id="pet-energy" class="form-select">
                            <option value="">All</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-spayed-neutered" id="pet-is-spayed-neutered" class="form-check-input">
                            <label for="pet-is-spayed-neutered" class="form-check-label">Spayed/Neutered</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-house-trained" id="pet-is-house-trained" class="form-check-input">
                            <label for="pet-is-house-trained" class="form-check-label">House Trained</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-good-with-children" id="pet-is-good-with-children" class="form-check-input">
                            <label for="pet-is-good-with-children" class="form-check-label">Good with Children</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-is-good-with-other-pets" id="pet-is-good-with-other-pets" class="form-check-input">
                            <label for="pet-is-good-with-other-pets" class="form-check-label">Good with Other Pets</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="pet-featured-only" id="pet-featured-only" class="form-check-input">
                            <label for="pet-featured-only" class="form-check-label">Featured Pets Only</label>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary form-btn">
                        <i class="bi bi-search"></i>
                        Apply
                    </button>
                    <button type="button" class="btn btn-secondary form-btn" id="reset-btn">
                        <i class="bi bi-x-circle"></i>
                        Clear
                    </button>
                </div>
            </form>
            <section class="pet-list">
                <h3 class="mb-4">Pet Records</h3>
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Pet ID</th>
                            <th scope="col">Microchip ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Breed</th>
                            <th scope="col">Age</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Size</th>
                            <th scope="col">Weight</th>
                            <th scope="col">Spayed/Neutered</th>
                            <th scope="col">House Trained</th>
                            <th scope="col">Good with Children</th>
                            <th scope="col">Good with Other Pets</th>
                            <th scope="col">Energy Level</th>
                            <th scope="col">Status</th>
                            <th scope="col">Featured</th>
                            <th scope="col">Documents</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pets as $pet) : ?>
                            <tr>
                                <td scope="row" class="text-center"><?php echo $pet['id']; ?></td>
                                <td class="text-center"><?php echo $pet['microchip_id']; ?></td>
                                <td class="text-center"><?php echo $pet['name']; ?></td>
                                <td class="text-center"><?php echo $pet['type_name']; ?></td>
                                <td class="text-center"><?php echo $pet['breed_name']; ?></td>
                                <td class="text-center"><?php echo $pet['age_years']; ?> years <?php echo $pet['age_months']; ?> months</td>
                                <td class="text-center"><?php echo $pet['gender']; ?></td>
                                <td class="text-center"><?php echo $pet['size']; ?></td>
                                <td class="text-center"><?php echo $pet['weight']; ?></td>
                                <td class="text-center"><?php echo $pet['is_spayed_neutered'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
                                <td class="text-center"><?php echo $pet['is_house_trained'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
                                <td class="text-center"><?php echo $pet['good_with_kids'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
                                <td class="text-center"><?php echo $pet['good_with_pets'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
                                <td class="text-center"><?php echo $pet['energy_level']; ?></td>
                                <td class="text-center text-capitalize"><?php echo $pet['status']; ?></td>
                                <td class="text-center"><?php echo $pet['is_featured'] ? '<i class="bi bi-check-circle text-success"></i>' : '<i class="bi bi-x-circle text-danger"></i>'; ?></td>
                                <td class="text-center">
                                    <input type="file" name="pet-documents" id="pet-documents" class="form-control" hidden multiple enctype="multipart/form-data">
                                    <button type="button" class="btn btn-primary" id="upload-document-btn" data-bs-id="<?php echo $pet['id']; ?>">
                                        <i class="bi bi-upload"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="pet-actions">
                                        <button type="button" class="btn btn-primary" data-bs-id="<?php echo $pet['id']; ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger" data-bs-id="<?php echo $pet['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </body>
</html>