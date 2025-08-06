const viewUserModalBtns = document.querySelectorAll('#viewUserBtn');
viewUserModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        const userId = btn.getAttribute('data-user-id');
        fetch(`api/getUsersByID.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.message);
                    return;
                }

                document.getElementById('user-id').textContent = data.id;
                document.getElementById('user-name').textContent = data.full_name;
                document.getElementById('user-email').textContent = data.email;
                document.getElementById('user-phone').textContent = data.phone;
                document.getElementById('user-join-date').textContent = data.created_at;
                document.getElementById('user-status').textContent = data.is_active ? 'Active' : 'Inactive';
                document.getElementById('user-role').textContent = data.role;
            })
            .catch(err => {
                console.error('Error fetching user details:', err);
                return;
            });
    });
});

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