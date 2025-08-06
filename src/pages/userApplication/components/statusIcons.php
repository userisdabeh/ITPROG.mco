<?php
function getStatusIcon($status) {
    $icons = [
        'submitted' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><path d="M14 2v6h6"/></g></svg>',
            'class' => 'status-submitted',
            'color' => '#3b82f6'
        ],
        'under_review' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10s10-4.5 10-10S17.5 2 12 2zm3.5 12c-.3.5-.9.6-1.4.4l-2.6-1.5c-.3-.2-.5-.5-.5-.9V7c0-.6.4-1 1-1s1 .4 1 1v4.4l2.1 1.2c.5.3.6.9.4 1.4z"/></svg>',
            'class' => 'status-review',
            'color' => '#f59e0b'
        ],
        'interview_required' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="currentColor" d="M2 6a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2h-4.586l-2.707 2.707a1 1 0 0 1-1.414 0L8.586 19H4a2 2 0 0 1-2-2V6zm18 0H4v11h5a1 1 0 0 1 .707.293L12 19.586l2.293-2.293A1 1 0 0 1 15 17h5V6zM6 9.5a1 1 0 0 1 1-1h10a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1zm0 4a1 1 0 0 1 1-1h6a1 1 0 1 1 0 2H7a1 1 0 0 1-1-1z"/></svg>',
            'class' => 'status-interview',
            'color' => '#8b5cf6'
        ],
        'approved' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M12 21a9 9 0 1 0 0-18a9 9 0 0 0 0 18m4.768-11.36a1 1 0 1 0-1.536-1.28l-3.598 4.317c-.347.416-.542.647-.697.788l-.006.006l-.007-.005c-.168-.127-.383-.339-.765-.722l-1.452-1.451a1 1 0 0 0-1.414 1.414l1.451 1.451l.041.041c.327.327.64.641.933.862c.327.248.756.48 1.305.456c.55-.025.956-.296 1.26-.572c.27-.247.555-.588.85-.943l.037-.044z" clip-rule="evenodd"/></svg>',
            'class' => 'status-approved',
            'color' => '#10b981'
        ],
        'denied' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 12 12"><path fill="currentColor" d="M5.5 1C2.46 1 0 3.46 0 6.5S2.46 12 5.5 12S11 9.54 11 6.5S8.54 1 5.5 1m2.44 7.06c.24.24.24.64 0 .88c-.12.12-.28.18-.44.18s-.32-.06-.44-.18L5.5 7.38L3.94 8.94c-.12.12-.28.18-.44.18s-.32-.06-.44-.18a.63.63 0 0 1 0-.88L4.62 6.5L3.06 4.94c-.24-.24-.24-.64 0-.88s.64-.24.88 0L5.5 5.62l1.56-1.56c.24-.24.64-.24.88 0s.24.64 0 .88L6.38 6.5z"/></svg>',
            'class' => 'status-denied',
            'color' => '#ef4444'
        ],
        'completed' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M13.414 1.5L12 .086l-4 4l-2-2L4.586 3.5L8 6.914zM0 8h6v2h4V8h6v8H0z"/></svg>',
            'class' => 'status-completed',
            'color' => '#277343ff'
        ],
        'withdrawn' => [
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48"><g fill="none" stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="4"><path d="M40 23V14L31 4H10C8.89543 4 8 4.89543 8 6V42C8 43.1046 8.89543 44 10 44H22"/><path d="M31 33L26 38L31 43"/><path d="M26 38H42V30"/><path d="M30 4V14H40"/></g></svg>',
            'class' => 'status-withdrawn',
            'color' => '#6b7280'
        ]
    ];
    
    return $icons[$status] ?? $icons['submitted'];
}

function getStatusText($status, $denial_reason = null) {
    $statusTexts = [
        'submitted' => [
            'title' => 'Application Submitted - Awaiting Review',
            'description' => 'Your application has been successfully submitted and is in our queue for review. We will update you as soon as our team begins processing your application.'
        ],
        'under_review' => [
            'title' => 'Under Review - Application is being assessed',
            'description' => 'Our team is currently reviewing your application. This process typically takes 3-5 business days. We may contact you if we need any additional information.'
        ],
        'interview_required' => [
            'title' => 'Interview Required - We\'ll contact you for a short interview',
            'description' => 'Your application has been reviewed and we would like to schedule a brief interview with you. This helps us ensure the best match between you and your potential new pet. We will contact you using your registered contact information.'
        ],
        'approved' => [
            'title' => 'Approved - Your application has been approved',
            'description' => 'Congratulations! Your application has been approved. Please wait for our contact for the final steps in the adoption process. We will reach out to you within 1-2 business days.'
        ],
        'denied' => [
            'title' => 'Denied - Application not approved',
            'description' => $denial_reason ? $denial_reason : 'Unfortunately, your application was not approved at this time. You may apply for other pets or reapply in the future.'
        ],
        'completed' => [
            'title' => 'Completed - Adoption process complete!',
            'description' => 'Congratulations! The adoption process has been completed successfully. We hope you and your new pet have many happy years together!'
        ],
        'withdrawn' => [
            'title' => 'Withdrawn',
            'description' => 'This application has been withdrawn. You can submit new applications for other available pets.'
        ]
    ];
    
    return $statusTexts[$status] ?? $statusTexts['submitted'];
}

function getStatusBadge($status, $denial_reason = null) {
    $statusInfo = getStatusIcon($status);
    $statusText = getStatusText($status, $denial_reason);
    
    return [
        'icon' => $statusInfo['icon'],
        'class' => $statusInfo['class'],
        'color' => $statusInfo['color'],
        'title' => $statusText['title'],
        'description' => $statusText['description']
    ];
}

function formatStatusDate($date, $label = '') {
    if (!$date) return '';
    
    $timestamp = strtotime($date);
    $formatted = date('M j, Y \a\t g:i A', $timestamp);
    
    return $label ? "$label: $formatted" : $formatted;
}
?>
