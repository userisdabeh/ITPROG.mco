
const deleteUserModalBtns = document.querySelectorAll('#deleteUserBtn');
deleteUserModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const userId = btn.getAttribute('data-user-id');
        document.getElementById('confirmDeleteBtn').setAttribute('data-user-id', userId);
    });
})

const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
confirmDeleteBtn.addEventListener('click', () => {
    const userId = confirmDeleteBtn.getAttribute('data-user-id');
    fetch(`api/deleteUserByID.php?id=${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.message);
                return;
            }

            alert(data.message);
            window.location.reload();
        })
        .catch(err => {
            console.error('Error deleting user:', err);
            alert('Failed to delete user. Please try again.');
        });
})

document.addEventListener('DOMContentLoaded', () => {
    const filterForm = document.querySelector('.filter-form');

    filterForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const params = new URLSearchParams(new FormData(filterForm)).toString();

        fetch(`api/filterUsers.php?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.message);
                    return;
                }

                updateUserTable(data.users);
            })
            .catch(err => {
                console.error('Filter error:', err);
            });
    });
});

function updateUserTable(users) {
    const tbody = document.querySelector('tbody');
    tbody.innerHTML = ''; // Clear current rows

    users.forEach(user => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${user.id}</td>
            <td>${user.full_name}</td>
            <td>${user.email}</td>
            <td>${user.phone}</td>
            <td>${user.created_at}</td>
            <td>${user.is_active == 1 ? 'Active' : 'Inactive'}</td>
            <td class="text-capitalize">${user.role}</td>
            <td>
                <div class="user-actions">
                    <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#viewUserModal" data-user-id="${user.id}">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-action" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-user-id="${user.id}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}
