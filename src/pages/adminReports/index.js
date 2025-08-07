document.addEventListener('DOMContentLoaded', () => {
    const filterSelect = document.getElementById('report-filter');

    filterSelect.addEventListener('change', () => {
        const selectedFilter = filterSelect.value;
        
        fetch(`api/filter.php?filter=${selectedFilter}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.message);
                } else {
                    console.log(data.data);
                }

                displayReports(data.data, selectedFilter);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    })
});

function displayReports(data, selectedFilter) {
    const totalSubmitted = document.getElementById('total-submitted');
    const totalForReview = document.getElementById('total-for-review');
    const totalForInterview = document.getElementById('total-for-interview');
    const totalApproved = document.getElementById('total-approved');
    const totalDenied = document.getElementById('total-denied');
    const totalCompleted = document.getElementById('total-completed');
    const totalWithdrawn = document.getElementById('total-withdrawn');
    const totalApplications = document.getElementById('total-applications');
    
    const totalSubmittedPercentage = document.getElementById('total-submitted-percentage');
    const totalForReviewPercentage = document.getElementById('total-for-review-percentage');
    const totalForInterviewPercentage = document.getElementById('total-for-interview-percentage');
    const totalApprovedPercentage = document.getElementById('total-approved-percentage');
    const totalDeniedPercentage = document.getElementById('total-denied-percentage');
    const totalCompletedPercentage = document.getElementById('total-completed-percentage');
    const totalWithdrawnPercentage = document.getElementById('total-withdrawn-percentage');
    const totalApplicationsPercentage = document.getElementById('total-applications-percentage');

    totalSubmitted.textContent = data.total_submitted;
    totalForReview.textContent = data.total_for_review;
    totalForInterview.textContent = data.total_for_interview;
    totalApproved.textContent = data.total_approved;
    totalDenied.textContent = data.total_denied;
    totalCompleted.textContent = data.total_completed;
    totalWithdrawn.textContent = data.total_withdrawn;
    totalApplications.textContent = data.total_applications;

    totalSubmittedPercentage.textContent = calculatePercentage(data.total_submitted, data.total_applications);
    totalForReviewPercentage.textContent = calculatePercentage(data.total_for_review, data.total_applications);
    totalForInterviewPercentage.textContent = calculatePercentage(data.total_for_interview, data.total_applications);
    totalApprovedPercentage.textContent = calculatePercentage(data.total_approved, data.total_applications);
    totalDeniedPercentage.textContent = calculatePercentage(data.total_denied, data.total_applications);
    totalCompletedPercentage.textContent = calculatePercentage(data.total_completed, data.total_applications);
    totalWithdrawnPercentage.textContent = calculatePercentage(data.total_withdrawn, data.total_applications);

    let percentageText = 'Overall';
    switch (selectedFilter) {
        case 'monthly':
            percentageText = 'Monthly';
            break;
        case 'six_months':
            percentageText = 'Last Six Months';
            break;
        case 'yearly':
            percentageText = 'This Year';
            break;
        default:
            percentageText = 'Overall';
            break;
    }
    totalApplicationsPercentage.textContent = percentageText;
}

function calculatePercentage(total, totalApplications) {
    if (!total || !totalApplications) {
        return '0% of total applications';
    }

    const percentage = ((total / totalApplications) * 100).toFixed(2);
    return `${percentage}% of total applications`;
}