<?php
    session_start();
    $activeAdminPage = 'users';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit;
    }

    include_once '../../../server/db.php';

    $query = "SELECT * FROM users";
    $result = mysqli_query($conn, $query);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Wonderpets - Admin User Manager</title>

        <!-- For Bootstrap Icons, Modals, and other functionalities -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous" defer></script>

        <!-- For the global admin styles -->
        <link rel="stylesheet" href="index.css?v=<?php echo time(); ?>">

        <!-- For the navigation bar -->
        <link rel="stylesheet" href="../../components/admin/nav.css?v=<?php echo time(); ?>">

        <!-- For the API -->
        <script src="index.js?v=<?php echo time(); ?>" defer></script>
    </head>
    <body>
        <?php include '../../components/admin/nav.php'; ?>
        <main>
            <section class="main-header mb-3">
                <div class="main-header-title">
                    <h3>User Manager</h3>
                    <p>Manage user records and documents.</p>
                </div>
            </section>
            <form method="get" class="filter-form">
                <h5 class="mb-3">Filter Users</h5>
                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="search" name="search" id="search" placeholder="Search for a user" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select">
                        <option value="">All</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="adoptions" class="form-label">Adoptions</label>
                    <div class="input-group">
                        <input type="number" name="min-adoptions" id="min-adoptions" placeholder="Min" class="form-control">
                        <span class="input-group-text">to</span>
                        <input type="number" name="max-adoptions" id="max-adoptions" placeholder="Max" class="form-control">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="join-date" class="form-label">Join Date</label>
                    <input type="month" name="join-date" id="join-date" class="form-control">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <button type="reset" class="btn btn-secondary">Clear Filters</button>
                </div>
            </form>
            <section class="users-list">
                <h5 class="mb-4">User Records</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Full Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Join Date</th>
                                <th scope="col">Status</th>
                                <th scope="col">Role</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                            <tr>
                                <td scope="row"><?php echo $user['id']; ?></td>
                                <td><?php echo $user['full_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['phone']; ?></td>
                                <td><?php echo $user['created_at']; ?></td> 
                                <td><?php echo $user['is_active'] == 1 ? 'Active' : 'Inactive'; ?></td>
                                <td class="text-capitalize"><?php echo $user['role']; ?></td>
                                <td>
                                    <div class="user-actions">
                                        <button class="btn btn-action" id="deleteUserBtn" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="<?php echo $user['id']; ?>">
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
        <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title fs-5" id="deleteUserModalLabel">Delete User</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this user?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>