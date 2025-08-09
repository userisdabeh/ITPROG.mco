document.addEventListener('DOMContentLoaded', () => {
  const scheduleInterviewBtn   = document.getElementById('schedule-interview-btn');
  const completeInterviewBtn   = document.getElementById('complete-interview-btn');
  const approveApplicationBtn  = document.getElementById('approve-application-btn');
  const denyApplicationBtn     = document.getElementById('deny-application-btn');
  const completeApplicationBtn = document.getElementById('complete-application-btn');
  const cancelBtn              = document.getElementById('cancel-btn');
  const applicationStatus      = document.getElementById('application-status');

  const interviewScheduleContainer   = document.getElementById('interview-schedule-container');
  const interviewCompletionContainer = document.getElementById('interview-completion-container');
  const denialReasonContainer        = document.getElementById('denial-reason-container');
  const adminNotesContainer          = document.getElementById('admin-notes-container');

  const adminActionsForm = document.getElementById('admin-actions-form');

  const currentStatus      = applicationStatus.textContent.trim();
  const interviewCompleted = applicationStatus.getAttribute('data-interview-completed');

  let selectedAction = null;

  // ---------- small helper so all fetches behave the same ----------
  const handleApiResponse = (res, fallback = 'Updated successfully') => {
    if (res && (res.error || res.success === false)) {
      alert(res.message || res.error || 'Request failed');
      return false;
    }
    alert((res && res.message) || fallback);
    window.location.reload();
    return true;
  };

  hideInputFields();
  handleButtons();

  scheduleInterviewBtn.addEventListener('click', () => {
    selectedAction = 'scheduleInterview';
    interviewScheduleContainer.style.display   = 'block';
    interviewCompletionContainer.style.display = 'none';
    denialReasonContainer.style.display        = 'none';
    adminNotesContainer.style.display          = 'none';
    displaySubmitContainer();
  });

  completeInterviewBtn.addEventListener('click', () => {
    selectedAction = 'completeInterview';
    interviewScheduleContainer.style.display   = 'none';
    interviewCompletionContainer.style.display = 'block';
    denialReasonContainer.style.display        = 'none';
    adminNotesContainer.style.display          = 'none';
    displaySubmitContainer();
  });

  approveApplicationBtn.addEventListener('click', () => {
    selectedAction = 'approveApplication';
    adminNotesContainer.style.display = 'block';
    displaySubmitContainer();
  });

  // COMPLETE APPLICATION is handled on click (not via the form submit)
  completeApplicationBtn.addEventListener('click', () => {
    const applicationID = document.getElementById('applicationID').value;

    fetch('api/completeApplication.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ applicationID })
    })
      .then(r => r.json())
      .then(data => handleApiResponse(data, 'Application completed'))
      .catch(() => alert('Error completing application'));
  });

  denyApplicationBtn.addEventListener('click', () => {
    selectedAction = 'denyApplication';
    denialReasonContainer.style.display = 'block';
    adminNotesContainer.style.display   = 'block';
    displaySubmitContainer();
  });

  cancelBtn.addEventListener('click', () => {
    adminActionsForm.reset();
    hideInputFields();
  });

  adminActionsForm.addEventListener('submit', (e) => {
    e.preventDefault();

    const interviewSchedule   = document.getElementById('interview-schedule').value;
    const interviewCompletion = document.getElementById('interview-completion').value;
    const denialReason        = document.getElementById('denial-reason').value;
    const adminNotes          = document.getElementById('admin-notes').value;
    const applicationID       = document.getElementById('applicationID').value;
    const approvedBy          = document.getElementById('approvedBy').value;

    switch (selectedAction) {
      case 'scheduleInterview':
        fetch('api/scheduleInterview.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ applicationID, interviewSchedule })
        })
          .then(r => r.json())
          .then(data => handleApiResponse(data, 'Interview scheduled'))
          .catch(() => alert('Error scheduling interview'));
        break;

      case 'completeInterview':
        fetch('api/completeInterview.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ applicationID, interviewCompletion })
        })
          .then(r => r.json())
          .then(data => handleApiResponse(data, 'Interview completed'))
          .catch(() => alert('Error completing interview'));
        break;

      case 'approveApplication':
        fetch('api/approveApplication.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ applicationID, adminNotes, approvedBy })
        })
          .then(r => r.json())
          .then(data => handleApiResponse(data, 'Application approved'))
          .catch(() => alert('Error approving application'));
        break;

      case 'denyApplication':
        fetch('api/denyApplication.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ applicationID, denialReason, adminNotes })
        })
          .then(r => r.json())
          .then(data => handleApiResponse(data, 'Application denied'))
          .catch(() => alert('Error denying application'));
        break;

      // completeApplication is handled above
      default:
        console.error('Invalid action selected');
        break;
    }
  });
});

function handleButtons() {
  const scheduleInterviewBtn   = document.getElementById('schedule-interview-btn');
  const completeInterviewBtn   = document.getElementById('complete-interview-btn');
  const approveApplicationBtn  = document.getElementById('approve-application-btn');
  const denyApplicationBtn     = document.getElementById('deny-application-btn');
  const completeApplicationBtn = document.getElementById('complete-application-btn');
  const applicationStatus      = document.getElementById('application-status');

  const currentStatus      = applicationStatus.textContent.trim();
  const interviewCompleted = applicationStatus.getAttribute('data-interview-completed');

  if (currentStatus === 'Under Review') {
    completeApplicationBtn.disabled = true;
    completeInterviewBtn.disabled   = true;
  } else if (currentStatus === 'Interview Required') {
    if (interviewCompleted === 'false') {
      scheduleInterviewBtn.disabled   = true;
      approveApplicationBtn.disabled  = true;
      denyApplicationBtn.disabled     = true;
      completeApplicationBtn.disabled = true;
    } else {
      scheduleInterviewBtn.disabled   = true;
      completeInterviewBtn.disabled   = true;
      approveApplicationBtn.disabled  = false;
      denyApplicationBtn.disabled     = false;
      completeApplicationBtn.disabled = true;
    }
  } else if (currentStatus === 'Approved') {
    scheduleInterviewBtn.disabled   = true;
    completeInterviewBtn.disabled   = true;
    denyApplicationBtn.disabled     = true;
    approveApplicationBtn.disabled  = true;
    completeApplicationBtn.disabled = false;
  } else if (currentStatus === 'Denied' || currentStatus === 'Completed') {
    scheduleInterviewBtn.disabled   = true;
    completeInterviewBtn.disabled   = true;
    approveApplicationBtn.disabled  = true;
    denyApplicationBtn.disabled     = true;
    completeApplicationBtn.disabled = true;
  }
}

function hideInputFields() {
  const interviewScheduleContainer   = document.getElementById('interview-schedule-container');
  const interviewCompletionContainer = document.getElementById('interview-completion-container');
  const denialReasonContainer        = document.getElementById('denial-reason-container');
  const adminNotesContainer          = document.getElementById('admin-notes-container');
  const submitContainer              = document.getElementById('submit-container');

  interviewScheduleContainer.style.display   = 'none';
  interviewCompletionContainer.style.display = 'none';
  denialReasonContainer.style.display        = 'none';
  adminNotesContainer.style.display          = 'none';
  submitContainer.classList.remove('d-flex');
  submitContainer.classList.add('d-none');
}

function displaySubmitContainer() {
  const submitContainer = document.getElementById('submit-container');
  submitContainer.classList.remove('d-none');
  submitContainer.classList.add('d-flex');
}