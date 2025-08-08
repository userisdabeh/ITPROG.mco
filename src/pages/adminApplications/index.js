const startReviewApplication = document.getElementById('startReviewApplication');

startReviewApplication.addEventListener('click', () => {
    const applicationID = startReviewApplication.getAttribute('data-bs-application-id');

    fetch(`api/reviewApplication.php?applicationID=${applicationID}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            window.location.reload();
        })
        .catch(error => {
            console.error('Error reviewing application:', error);
        });
});