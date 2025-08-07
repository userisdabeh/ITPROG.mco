document.addEventListener('DOMContentLoaded', function () {
    // Upload Document Handler
    const uploadBtns = document.querySelectorAll('#upload-document-btn');
    const uploadInput = document.getElementById('pet-documents');

    uploadBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            uploadInput.click();
        });
    });

    // Edit Button Handler
    const editButtons = document.querySelectorAll('.edit-btn');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const {
                bsId: id,
                name,
                ageYears,
                ageMonths,
                weight,
                type: typeId,
                breed: breedId,
                gender,
                size,
                status,
                energy,
                spayed,
                house,
                kids,
                pets,
                featured
            } = this.dataset;

            document.getElementById('edit-pet-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-age-years').value = ageYears;
            document.getElementById('edit-age-months').value = ageMonths;
            document.getElementById('edit-weight').value = weight;

            document.getElementById('editPetType').value = String(typeId);
            document.getElementById('editPetBreed').value = String(breedId);
            document.getElementById('edit-gender').value = String(gender);
            document.getElementById('edit-size').value = String(size);
            document.getElementById('edit-status').value = String(status);
            document.getElementById('edit-energy').value = String(energy);

            document.getElementById('edit-spayed').checked = spayed === '1';
            document.getElementById('edit-house-trained').checked = house === '1';
            document.getElementById('edit-good-kids').checked = kids === '1';
            document.getElementById('edit-good-pets').checked = pets === '1';
            document.getElementById('edit-featured').checked = featured === '1';
        });
    });

    // Delete Button Handler
    const deleteButtons = document.querySelectorAll('.delete-btn');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const petId = this.dataset.bsId;

            if (confirm("Are you sure you want to delete this pet? This action cannot be undone.")) {
                fetch('deletePet.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${encodeURIComponent(petId)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Pet deleted successfully.');
                        window.location.reload();
                    } else {
                        alert('Error deleting pet: ' + (data.error || 'Unknown error.'));
                    }
                })
                .catch(error => {
                    alert('Network error. Please try again.');
                    console.error(error);
                });
            }
        });
    });

    // Clear Button Handler
    const resetBtn = document.getElementById('reset-btn');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            document.getElementById('filter-form').reset();
            window.location.href = window.location.pathname; // Reload with no filters
        });
    }
});
