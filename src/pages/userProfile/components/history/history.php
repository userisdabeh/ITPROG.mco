<?php

function pretty_status($s) {
  $map = [
    'submitted' => 'Submitted',
    'under_review' => 'Under Review',
    'interview_required' => 'Interview Required',
    'approved' => 'Approved',
    'denied' => 'Denied',
    'completed' => 'Completed',
    'withdrawn' => 'Withdrawn',
    'pending' => 'Pending',
  ];
  $key = strtolower((string)$s);
  return $map[$key] ?? ucwords(str_replace('_',' ', (string)$s));
}

//  Applications (with current status for fallback)
$applications = [];
if ($appSql = $conn->prepare("
  SELECT aa.id AS application_id,
         p.name AS pet_name,
         aa.status,
         aa.created_at,
         aa.updated_at
  FROM adoption_applications aa
  JOIN pets p ON p.id = aa.pet_id
  WHERE aa.user_id = ?
  ORDER BY aa.created_at DESC
")) {
  $appSql->bind_param("i", $user_id);
  $appSql->execute();
  $appRes = $appSql->get_result();
  $applications = $appRes ? $appRes->fetch_all(MYSQLI_ASSOC) : [];
  $appSql->close();
}

//Status history from adoption_history 
$historyRows = [];
if ($histSql = $conn->prepare("
  SELECT ah.id,
         ah.application_id,
         ah.old_status,
         ah.new_status,
         ah.notes,
         ah.changed_by,
         ah.created_at,
         p.name AS pet_name,
         u.full_name AS changed_by_name
  FROM adoption_history ah
  JOIN adoption_applications aa ON aa.id = ah.application_id
  JOIN pets p ON p.id = aa.pet_id
  LEFT JOIN users u ON u.id = ah.changed_by
  WHERE aa.user_id = ?
  ORDER BY ah.created_at DESC, ah.id DESC
")) {
  $histSql->bind_param("i", $user_id);
  $histSql->execute();
  $histRes = $histSql->get_result();
  $historyRows = $histRes ? $histRes->fetch_all(MYSQLI_ASSOC) : [];
  $histSql->close();
}

// Build latest status per application from history (so we can compare to current)
$latestFromHistory = []; 
foreach ($historyRows as $h) {
  $appId = (int)$h['application_id'];
  if (!isset($latestFromHistory[$appId]) ||
      strtotime($h['created_at']) > strtotime($latestFromHistory[$appId]['created_at'])) {
    $latestFromHistory[$appId] = [
      'new_status' => $h['new_status'],
      'created_at' => $h['created_at'],
    ];
  }
}

// Completed adoptions 
$adoptions = [];
if ($adoptSql = $conn->prepare("
  SELECT a.id AS adoption_id,
         p.name AS pet_name,
         a.adoption_date,
         a.adoption_fee_paid,
         a.created_at
  FROM adoptions a
  JOIN pets p ON p.id = a.pet_id
  WHERE a.user_id = ?
  ORDER BY COALESCE(a.adoption_date, a.created_at) DESC
")) {
  $adoptSql->bind_param("i", $user_id);
  $adoptSql->execute();
  $adoptRes = $adoptSql->get_result();
  $adoptions = $adoptRes ? $adoptRes->fetch_all(MYSQLI_ASSOC) : [];
  $adoptSql->close();
}

// Recent activity logs
$logs = [];
if ($logSql = $conn->prepare("
  SELECT activity_type, description, created_at
  FROM activity_logs
  WHERE user_id = ?
  ORDER BY created_at DESC
  LIMIT 30
")) {
  $logSql->bind_param("i", $user_id);
  $logSql->execute();
  $logRes = $logSql->get_result();
  $logs = $logRes ? $logRes->fetch_all(MYSQLI_ASSOC) : [];
  $logSql->close();
}


$timeline = [];

// Application submitted + fallback current status if history missed it
foreach ($applications as $a) {
  $appId = (int)$a['application_id'];
  $pet   = htmlspecialchars($a['pet_name'] ?? 'Unknown');

  // Submission
  if (!empty($a['created_at'])) {
    $timeline[] = [
      'when'    => $a['created_at'],
      'type'    => 'application_submitted',
      'title'   => 'Adoption Application Submitted',
      'details' => "For <strong>{$pet}</strong> (Application #{$appId})",
    ];
  }

  // Fallback current status (if not 'submitted')
  $currentStatus = strtolower($a['status'] ?? '');
  if ($currentStatus && $currentStatus !== 'submitted') {
    $histLatest = $latestFromHistory[$appId]['new_status'] ?? null;
    if (!$histLatest || strtolower($histLatest) !== $currentStatus) {
      $when = $a['updated_at'] ?: $a['created_at'];
      $timeline[] = [
        'when'    => $when,
        'type'    => 'application_status',
        'title'   => 'Application Status: ' . pretty_status($a['status']),
        'details' => "For <strong>{$pet}</strong> (Application #{$appId})",
      ];
    }
  }
}

// Status change rows from history 
foreach ($historyRows as $h) {
  $pet = htmlspecialchars($h['pet_name'] ?? 'Unknown');
  $by  = $h['changed_by_name'] ? ' by <em>' . htmlspecialchars($h['changed_by_name']) . '</em>' : '';
  $notes = $h['notes'] ? '<br><small>Notes: '. nl2br(htmlspecialchars($h['notes'])) . '</small>' : '';
  $timeline[] = [
    'when'    => $h['created_at'],
    'type'    => 'application_status',
    'title'   => 'Application Status: ' . pretty_status($h['new_status']),
    'details' => "For <strong>{$pet}</strong> (Application #" . (int)$h['application_id'] . ")" . $by . $notes,
  ];
}

// C) Adoptions
foreach ($adoptions as $ad) {
  $pet = htmlspecialchars($ad['pet_name'] ?? 'Unknown');
  $date = $ad['adoption_date'] ?: $ad['created_at'];
  $fee  = number_format((float)$ad['adoption_fee_paid'], 2);
  $timeline[] = [
    'when'    => $date,
    'type'    => 'adoption',
    'title'   => 'Pet Adopted',
    'details' => "You adopted <strong>{$pet}</strong>. Fee paid: ₱{$fee}.",
  ];
}

// D) Optional logs
foreach ($logs as $log) {
  $timeline[] = [
    'when'    => $log['created_at'],
    'type'    => 'activity',
    'title'   => htmlspecialchars($log['activity_type']),
    'details' => htmlspecialchars($log['description']),
  ];
}

// Sort newest at the top
usort($timeline, fn($a,$b) => strtotime($b['when']) <=> strtotime($a['when']));

// Counter + render
$historyCount = count($timeline);
?>

<link rel="stylesheet" href="components/history/history.css?v=<?= time() ?>" />

<section class="history-section">
  <div class="section-header">
    <div>
      <h2 class="section-title">History</h2>
      <p class="section-subtitle">Track your application updates, adoptions, and account activity.</p>
    </div>
    <span class="history-count"><?= $historyCount ?> <?= $historyCount === 1 ? 'item' : 'items' ?></span>
  </div>

  <?php if ($historyCount === 0): ?>
    <div class="empty-history">
      <div class="empty-icon">
        <svg viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/>
          <path d="M12 7v5l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
      </div>
      <h4>No history yet</h4>
      <p>When you submit an application or complete an adoption, you’ll see updates here.</p>
    </div>
  <?php else: ?>
    <ol class="timeline">
      <?php foreach ($timeline as $item): ?>
        <li class="timeline-item timeline-<?= htmlspecialchars($item['type']) ?>">
          <div class="dot"></div>
          <div class="content">
            <div class="row1">
              <span class="title"><?= $item['title'] ?></span>
              <span class="when"><?= date('M d, Y g:i A', strtotime($item['when'])) ?></span>
            </div>
            <div class="details"><?= $item['details'] ?></div>
          </div>
        </li>
      <?php endforeach; ?>
    </ol>
  <?php endif; ?>
</section>
