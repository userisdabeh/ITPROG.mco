document.addEventListener('DOMContentLoaded', () => {
    const petTypeSelect = document.getElementById('pet-type');
    const petBreedSelect = document.getElementById('pet-breed');

    petTypeSelect.addEventListener('change', () => {
        const selectedType = petTypeSelect.value;

        if(!selectedType) {
            petBreedSelect.innerHTML = '<option value="">Select Breed</option>';
            return;
        }

        fetch(`getBreeds.php?type_id=${selectedType}`)
            .then(response => response.json())
            .then(data => {
                petBreedSelect.innerHTML = '<option value="">Select Breed</option>';
                data.forEach(breed => {
                    const option = document.createElement('option');
                    option.value = breed.id;
                    option.textContent = breed.breed_name;
                    petBreedSelect.appendChild(option);
                })
            })
            .catch(error => {
                console.error('Error fetching breeds:', error);
            });
    });
})
