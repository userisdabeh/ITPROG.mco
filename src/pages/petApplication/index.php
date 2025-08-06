<?php
session_start();
require '../../../server/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Check if pet_id is provided
if (!isset($_GET['pet_id'])) {
    header("Location: ../home/index.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$pet_id = (int) $_GET['pet_id'];
$success_message = '';
$error_message = '';
$errors = [];

// Handle success/error messages from URL parameters
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = "Your adoption application has been submitted successfully!";
}
if (isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}

// Fetch user information
$user_sql = "SELECT full_name, email, age, current_address, permanent_address, phone FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result ? $user_result->fetch_assoc() : null;

if (!$user) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch pet information
$pet_sql = "
    SELECT p.*, b.breed_name, pt.type_name 
    FROM pets p 
    LEFT JOIN breeds b ON b.id = p.breed_id 
    JOIN pet_types pt ON pt.id = p.pet_type_id 
    WHERE p.id = $pet_id AND p.status = 'available'
";
$pet_result = $conn->query($pet_sql);
$pet = $pet_result ? $pet_result->fetch_assoc() : null;

if (!$pet) {
    header("Location: ../home/index.php");
    exit();
}

// Check if user already has a pending application for this pet
$existing_app_sql = "SELECT id FROM adoption_applications WHERE user_id = $user_id AND pet_id = $pet_id AND status IN ('submitted', 'under_review', 'interview_required', 'approved')";
$existing_result = $conn->query($existing_app_sql);

if ($existing_result && $existing_result->num_rows > 0) {
    $error_message = "You already have a pending application for this pet.";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !$existing_result->num_rows) {
    $home_environment = $conn->real_escape_string(trim($_POST['home_environment']));
    $previous_experience = $conn->real_escape_string(trim($_POST['previous_experience']));
    $commitment_statement = $conn->real_escape_string(trim($_POST['commitment_statement']));
    $terms_accepted = isset($_POST['terms_accepted']) ? 1 : 0;

    // Validation
    if (strlen($home_environment) < 50) {
        $errors[] = "Home environment description must be at least 50 characters long.";
    }
    if (strlen($commitment_statement) < 100) {
        $errors[] = "Commitment statement must be at least 100 characters long.";
    }
    if (!$terms_accepted) {
        $errors[] = "You must accept the terms and conditions to proceed.";
    }

    // Insert application if no errors
    if (empty($errors)) {
        $terms_accepted_at = date('Y-m-d H:i:s');
        
        $insert_sql = "
            INSERT INTO adoption_applications 
            (user_id, pet_id, home_environment, previous_experience, commitment_statement, terms_accepted, terms_accepted_at, status) 
            VALUES 
            ($user_id, $pet_id, '$home_environment', '$previous_experience', '$commitment_statement', $terms_accepted, '$terms_accepted_at', 'submitted')
        ";
        
        if ($conn->query($insert_sql)) {
            header("Location: index.php?pet_id=$pet_id&success=1");
            exit();
        } else {
            $error_message = "Error submitting application: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption Application - <?= htmlspecialchars($pet['name']) ?></title>
    <link rel="stylesheet" href="index.css?v=<?= time() ?>">
</head>
<body>
<?php include '../../components/userHeader.php'; ?>
    
    <div class="application-container">
        <div class="application-header">
            <h1>Pet Adoption Application</h1>
            <p class="application-subtitle">Complete this form to apply for adopting <?= htmlspecialchars($pet['name']) ?></p>
        </div>

        <!-- Pet Information Summary -->
        <div class="pet-summary">
            <h2>Pet Information</h2>
            <div class="pet-details">
                <h3><?= htmlspecialchars($pet['name']) ?></h3>
                <p><strong>Breed:</strong> <?= htmlspecialchars($pet['breed_name'] ?? 'Mixed Breed') ?></p>
                <p><strong>Type:</strong> <?= htmlspecialchars($pet['type_name']) ?></p>
                <p><strong>Age:</strong> <?= $pet['age_years'] ?> years, <?= $pet['age_months'] ?> months</p>
                <p><strong>Gender:</strong> <?= htmlspecialchars($pet['gender']) ?></p>
                <p><strong>Size:</strong> <?= htmlspecialchars($pet['size']) ?></p>
                <p><strong>Adoption Fee:</strong> $<?= number_format($pet['adoption_fee'], 2) ?></p>
            </div>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/>
                </svg>
                <?= $success_message ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($existing_result && $existing_result->num_rows > 0): ?>
            <div class="application-exists">
                <h2>Application Already Submitted</h2>
                <p>You have already submitted an application for this pet. Please wait for our team to review your application.</p>
                <div class="action-buttons">
                    <a href="../home/index.php" class="btn btn-md btn-primary">Browse Other Pets</a>
                    <a href="../userApplication/index.php" class="btn btn-md btn-secondary">View Application Status</a>
                </div>
            </div>
        <?php else: ?>
            <form method="POST" class="application-form">
                <!-- Applicant Information -->
                <section class="form-section">
                    <h2>Applicant Information</h2>
                    <p class="section-note">The following information is pulled from your account profile and cannot be edited here.</p>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="input input-md" value="<?= htmlspecialchars($user['full_name']) ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Age</label>
                            <input type="text" class="input input-md" value="<?= htmlspecialchars($user['age']) ?> years" disabled>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="input input-md" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" class="input input-md" value="<?= htmlspecialchars($user['phone']) ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Current Address</label>
                        <input type="text" class="input input-md" value="<?= htmlspecialchars($user['current_address']) ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Permanent Address</label>
                        <input type="text" class="input input-md" value="<?= htmlspecialchars($user['permanent_address']) ?>" disabled>
                    </div>

                    <p class="edit-note">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                        </svg>
                        Need to update your information? <a href="../userProfile/index.php?tab=personal&edit=true">Edit your profile</a>
                    </p>
                </section>

                <!-- Home Environment -->
                <section class="form-section">
                    <h2>Home Environment</h2>
                    <div class="form-group">
                        <label for="home_environment" class="form-label required">Describe your home environment and neighborhood</label>
                        <textarea 
                            id="home_environment" 
                            name="home_environment" 
                            class="input textarea" 
                            rows="5" 
                            placeholder="Please describe your living situation, home type (apartment, house, etc.), yard space, neighborhood characteristics, and any other relevant details about your home environment..."
                            required
                        ><?= isset($_POST['home_environment']) ? htmlspecialchars($_POST['home_environment']) : '' ?></textarea>
                        <span class="form-help">Minimum 50 characters. Be specific about your living space, yard, neighborhood, and household members.</span>
                    </div>
                </section>

                <!-- Previous Experience -->
                <section class="form-section">
                    <h2>Previous Pet Ownership Experience</h2>
                    <div class="form-group">
                        <label for="previous_experience" class="form-label">Tell us about your previous experience with pets (if any)</label>
                        <textarea 
                            id="previous_experience" 
                            name="previous_experience" 
                            class="input textarea" 
                            rows="4" 
                            placeholder="Describe any previous pets you've owned, how long you had them, what happened to them, and any relevant experience with this type of pet. If you have no previous experience, please explain why you feel ready to adopt a pet now..."
                        ><?= isset($_POST['previous_experience']) ? htmlspecialchars($_POST['previous_experience']) : '' ?></textarea>
                        <span class="form-help">Include details about pet care, training, veterinary care, and any challenges you faced.</span>
                    </div>
                </section>

                <!-- Commitment Statement -->
                <section class="form-section">
                    <h2>Commitment Statement</h2>
                    <div class="form-group">
                        <label for="commitment_statement" class="form-label required">Why should you be able to adopt this pet?</label>
                        <textarea 
                            id="commitment_statement" 
                            name="commitment_statement" 
                            class="input textarea" 
                            rows="6" 
                            placeholder="Explain why you want to adopt this specific pet, how you plan to care for them, your long-term commitment, and what makes you the right adopter. Include details about daily care, exercise, training, veterinary care, and how this pet will fit into your lifestyle..."
                            required
                        ><?= isset($_POST['commitment_statement']) ? htmlspecialchars($_POST['commitment_statement']) : '' ?></textarea>
                        <span class="form-help">Minimum 100 characters. Be specific about your commitment to this pet's wellbeing and your adoption plans.</span>
                    </div>
                </section>

                <!-- Terms and Conditions -->
                <section class="form-section">
                    <h2>Terms and Conditions</h2>
                    <div class="terms-content">
                        <h3>Adoption Agreement</h3>
                        <ul>
                            <li>I understand that this is an application and does not guarantee adoption</li>
                            <li>I agree to provide proper veterinary care, including regular check-ups and vaccinations</li>
                            <li>I understand that if I can no longer care for this pet, I must return them to the shelter</li>
                            <li>I agree to provide a safe, loving, and permanent home for this pet</li>
                            <li>I understand that the adoption fee helps cover the cost of spaying/neutering, vaccinations, and medical care</li>
                            <li>I agree to comply with all local pet licensing and registration requirements</li>
                            <li>I understand that a home visit may be required before adoption approval</li>
                            <li>I agree to provide references if requested during the application review process</li>
                        </ul>
                    </div>

                    <div class="checkbox-wrapper">
                        <input type="checkbox" id="terms_accepted" name="terms_accepted" class="checkbox" required>
                        <label for="terms_accepted">I have read and agree to all terms and conditions</label>
                    </div>
                </section>

                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-lg btn-primary">Submit Application</button>
                    <a href="../petProfile/index.php?id=<?= $pet_id ?>" class="btn btn-lg btn-secondary">Back to Pet Profile</a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Character count for textareas
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea[required]');
            
            textareas.forEach(textarea => {
                const helpText = textarea.nextElementSibling;
                if (helpText && helpText.classList.contains('form-help')) {
                    const updateCount = () => {
                        const length = textarea.value.length;
                        const minLength = textarea.name === 'commitment_statement' ? 100 : 50;
                        const remaining = Math.max(0, minLength - length);
                        
                        if (remaining > 0) {
                            helpText.textContent = helpText.textContent.split('(')[0] + ` (${remaining} characters remaining)`;
                            helpText.style.color = '#ef4444';
                        } else {
                            helpText.textContent = helpText.textContent.split('(')[0];
                            helpText.style.color = '#6b7280';
                        }
                    };
                    
                    textarea.addEventListener('input', updateCount);
                    updateCount();
                }
            });
        });
    </script>
</body>
</html>
