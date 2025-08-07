document.addEventListener('DOMContentLoaded', () => {
    fetch('api/fetch_reports.php')
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error(data.message);
                return;
            }

            displayReports(data.data);
        })
        .catch(err => console.error("Error:", err));
});

function displayReports(data) {
    const update = (id, value) => {
        document.getElementById(id).textContent = value ?? 0;
        document.getElementById(id + "-percentage").textContent =
            data.total_applications > 0
                ? `${((value / data.total_applications) * 100).toFixed(2)}% of total applications`
                : '0% of total applications';
    };

    update('total-submitted', data.total_submitted);
    update('total-for-review', data.total_for_review);
    update('total-for-interview', data.total_for_interview);
    update('total-approved', data.total_approved);
    update('total-denied', data.total_denied);
    update('total-completed', data.total_completed);
    update('total-withdrawn', data.total_withdrawn);

    document.getElementById('total-applications').textContent = data.total_applications;
    document.getElementById('total-applications-percentage').textContent = 'Overall';
}

document.addEventListener('DOMContentLoaded', () => {
    const filterSelect = document.getElementById('report-filter');

    // Load default (overall) on page load
    fetchReports('overall');

    filterSelect.addEventListener('change', () => {
        const selectedFilter = filterSelect.value;
        fetchReports(selectedFilter);
    });

    function fetchReports(filter) {
        fetch(`api/filter.php?filter=${filter}`)
            .then(response => response.json())
            .then(result => {
                if (result.error) {
                    console.error(result.message);
                    return;
                }

                displayReports(result.data, filter);
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
});

function displayReports(data, selectedFilter) {
    const update = (id, value) => {
        document.getElementById(id).textContent = value ?? 0;
        document.getElementById(`${id}-percentage`).textContent = 
            data.total_applications > 0
                ? `${((value / data.total_applications) * 100).toFixed(2)}% of total applications`
                : '0% of total applications';
    };

    update('total-submitted', data.total_submitted);
    update('total-for-review', data.total_for_review);
    update('total-for-interview', data.total_for_interview);
    update('total-approved', data.total_approved);
    update('total-denied', data.total_denied);
    update('total-completed', data.total_completed);
    update('total-withdrawn', data.total_withdrawn);

    document.getElementById('total-applications').textContent = data.total_applications;

    // Update label
    const totalLabel = document.getElementById('total-applications-percentage');
    const labelMap = {
        'monthly': 'Monthly',
        'six_months': 'Last Six Months',
        'yearly': 'This Year',
        'overall': 'Overall'
    };
    totalLabel.textContent = labelMap[selectedFilter] || 'Overall';
}
