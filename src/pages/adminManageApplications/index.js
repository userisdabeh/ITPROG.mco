document.addEventListener('DOMContentLoaded', () => {
    const scheduleInterviewBtn = document.getElementById('schedule-interview-btn');
    const completeInterviewBtn = document.getElementById('complete-interview-btn');
    const approveApplicationBtn = document.getElementById('approve-application-btn');
    const denyApplicationBtn = document.getElementById('deny-application-btn');
    const completeApplicationBtn = document.getElementById('complete-application-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const applicationStatus = document.getElementById('application-status');

    const interviewScheduleContainer = document.getElementById('interview-schedule-container');
    const interviewCompletionContainer = document.getElementById('interview-completion-container');
    const denialReasonContainer = document.getElementById('denial-reason-container');
    const adminNotesContainer = document.getElementById('admin-notes-container');

    const adminActionsForm = document.getElementById('admin-actions-form');

    const currentStatus = applicationStatus.textContent.trim();
    const interviewCompleted = applicationStatus.getAttribute('data-interview-completed');

    let selectedAction = null;

    hideInputFields();
    handleButtons();

    scheduleInterviewBtn.addEventListener('click', () => {
        selectedAction = 'scheduleInterview';
        interviewScheduleContainer.style.display = 'block';
        interviewCompletionContainer.style.display = 'none';
        denialReasonContainer.style.display = 'none';
        adminNotesContainer.style.display = 'none';
        displaySubmitContainer();
    });

    completeInterviewBtn.addEventListener('click', () => {
        selectedAction = 'completeInterview';
        interviewScheduleContainer.style.display = 'none';
        interviewCompletionContainer.style.display = 'block';
        denialReasonContainer.style.display = 'none';
        adminNotesContainer.style.display = 'none';
        displaySubmitContainer();
    });

    approveApplicationBtn.addEventListener('click', () => {
        selectedAction = 'approveApplication';
        adminNotesContainer.style.display = 'block';
        displaySubmitContainer();
    });

    completeApplicationBtn.addEventListener('click', () => {
        selectedAction = 'completeApplication';
        const applicationID = document.getElementById('applicationID').value;

        fetch('api/completeApplication.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                applicationID: applicationID
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            alert(data.success);
            window.location.reload();
        })
        .catch(error => {
            alert('Error completing application');
        });
    })
    
    denyApplicationBtn.addEventListener('click', () => {
        selectedAction = 'denyApplication';
        denialReasonContainer.style.display = 'block';
        adminNotesContainer.style.display = 'block';
        displaySubmitContainer();
    });

    cancelBtn.addEventListener('click', () => {
        adminActionsForm.reset();
        hideInputFields();
    });

    adminActionsForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const interviewSchedule = document.getElementById('interview-schedule').value;
        const interviewCompletion = document.getElementById('interview-completion').value;
        const denialReason = document.getElementById('denial-reason').value;
        const adminNotes = document.getElementById('admin-notes').value;
        const applicationID = document.getElementById('applicationID').value;
        const approvedBy = document.getElementById('approvedBy').value;

        switch (selectedAction) {
            case 'scheduleInterview':
                // Insert fetch request to schedule interview (used params: applicationID, interviewSchedule)
                fetch('api/scheduleInterview.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        applicationID: applicationID,
                        interviewSchedule: interviewSchedule
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    alert(data.success);
                    window.location.reload();
                })
                .catch(error => {
                    alert('Error scheduling interview');
                });
                break;
            case 'completeInterview':
                // Insert fetch request to complete interview (used params: applicationID, interviewCompletion)
                fetch('api/completeInterview.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        applicationID: applicationID,
                        interviewCompletion: interviewCompletion
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    alert(data.success);
                    window.location.reload();
                })
                .catch(error => {
                    alert('Error completing interview');
                });
                break;
            case 'approveApplication':
                // Insert fetch request to approve application (used params: applicationID, adminNotes)
                fetch('api/approveApplication.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        applicationID: applicationID,
                        adminNotes: adminNotes,
                        approvedBy: approvedBy
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    alert(data.success);
                    window.location.reload();
                })
                .catch(error => {
                    alert('Error approving application');
                });
                break;
            case 'denyApplication':
                // Insert fetch request to deny application (used params: applicationID, denialReason, adminNotes)
                fetch('api/denyApplication.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        applicationID: applicationID,
                        denialReason: denialReason,
                        adminNotes: adminNotes
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    alert(data.success);
                    window.location.reload();
                })
                .catch(error => {
                    alert('Error denying application');
                });
                break;
            case 'completeApplication':
                // Insert fetch request to complete application
                break;
            default:
                console.error('Invalid action selected');
                break;
        }
    });
});

function handleButtons() {
    const scheduleInterviewBtn = document.getElementById('schedule-interview-btn');
    const completeInterviewBtn = document.getElementById('complete-interview-btn');
    const approveApplicationBtn = document.getElementById('approve-application-btn');
    const denyApplicationBtn = document.getElementById('deny-application-btn');
    const completeApplicationBtn = document.getElementById('complete-application-btn');
    const applicationStatus = document.getElementById('application-status');

    const currentStatus = applicationStatus.textContent.trim();
    const interviewCompleted = applicationStatus.getAttribute('data-interview-completed');

    // If an application is under review, it will disable the complete application button
    if (currentStatus === 'Under Review') {
        completeApplicationBtn.disabled = true;
        completeInterviewBtn.disabled = true;
    } else if (currentStatus === 'Interview Required') {
        // If an application is interview required, it will check if the interview has been completed
        if (interviewCompleted === 'false') {
            scheduleInterviewBtn.disabled = true;
            approveApplicationBtn.disabled = true;
            denyApplicationBtn.disabled = true;
            completeApplicationBtn.disabled = true;
        } else {
            scheduleInterviewBtn.disabled = true;
            completeInterviewBtn.disabled = true;
            approveApplicationBtn.disabled = false;
            denyApplicationBtn.disabled = false;
            completeApplicationBtn.disabled = true;
        }
    } else if (currentStatus === 'Approved') {
        scheduleInterviewBtn.disabled = true;
        completeInterviewBtn.disabled = true;
        denyApplicationBtn.disabled = true;
        approveApplicationBtn.disabled = true;
        completeApplicationBtn.disabled = false;
    } else if (currentStatus === 'Denied') {
        scheduleInterviewBtn.disabled = true;
        completeInterviewBtn.disabled = true;
        approveApplicationBtn.disabled = true;
        denyApplicationBtn.disabled = true;
        completeApplicationBtn.disabled = true;
    } else if (currentStatus === 'Completed') {
        scheduleInterviewBtn.disabled = true;
        completeInterviewBtn.disabled = true;
        approveApplicationBtn.disabled = true;
        denyApplicationBtn.disabled = true;
        completeApplicationBtn.disabled = true;
    }
}

function hideInputFields() {
    const interviewScheduleContainer = document.getElementById('interview-schedule-container');
    const interviewCompletionContainer = document.getElementById('interview-completion-container');
    const denialReasonContainer = document.getElementById('denial-reason-container');
    const adminNotesContainer = document.getElementById('admin-notes-container');
    const submitContainer = document.getElementById('submit-container');

    interviewScheduleContainer.style.display = 'none';
    interviewCompletionContainer.style.display = 'none';
    denialReasonContainer.style.display = 'none';
    adminNotesContainer.style.display = 'none';
    submitContainer.classList.remove('d-flex');
    submitContainer.classList.add('d-none');
}

function displaySubmitContainer() {
    const submitContainer = document.getElementById('submit-container');

    submitContainer.classList.remove('d-none');
    submitContainer.classList.add('d-flex');
}