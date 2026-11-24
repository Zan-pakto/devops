<?php
require_once __DIR__ . '/db.php';

$query = trim($_GET['query'] ?? '');
$results = [];
if ($query) {
    $stmt = $pdo->prepare("SELECT * FROM complaints WHERE ticket_id = ? OR UPPER(roll_number) = ? ORDER BY id DESC");
    $stmt->execute([$query, strtoupper($query)]);
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Grievance Status - CampusVoice</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Header -->
  <header class="navbar">
    <div class="nav-container">
      <div class="brand">
        <span class="brand-icon">🎓</span>
        <div class="brand-text">
          <span class="brand-title">CampusVoice</span>
          <span class="brand-subtitle">Student Grievance & Redressal Portal</span>
        </div>
      </div>
      <nav class="nav-links">
        <a href="index.php">Submit Grievance</a>
        <a href="track.php" class="active">Track Status</a>
        <a href="admin.php">Admin Desk</a>
        <a href="health.php" target="_blank" class="badge-health">Health API</a>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="card track-header-card">
      <h2>Track Grievance Resolution Timeline</h2>
      <p>Enter your Ticket ID (e.g., <code>GRV-2026-1001</code>) or your Student Roll Number (e.g., <code>22CS104</code>):</p>
      
      <form action="track.php" method="GET" class="search-form">
        <div class="search-input-group">
          <input type="text" name="query" value="<?= htmlspecialchars($query) ?>" placeholder="Enter Ticket ID or Roll Number..." required>
          <button type="submit" class="btn btn-primary">Search Status</button>
        </div>
      </form>
    </section>

    <?php if ($query && empty($results)): ?>
      <div class="card empty-card">
        <div class="empty-icon">🔍</div>
        <h3>No Grievance Found</h3>
        <p>No records matched <strong>"<?= htmlspecialchars($query) ?>"</strong>. Please check your reference ticket ID or roll number.</p>
        <a href="index.php" class="btn btn-secondary">Submit New Grievance</a>
      </div>
    <?php endif; ?>

    <?php if (!empty($results)): ?>
      <div class="results-container">
        <h3>Found <?= count($results) ?> Record(s)</h3>
        
        <?php foreach ($results as $c): ?>
          <article class="card grievance-detail-card">
            <div class="detail-header">
              <div class="detail-title-group">
                <span class="ticket-pill"><?= htmlspecialchars($c['ticket_id']) ?></span>
                <h4><?= htmlspecialchars($c['subject']) ?></h4>
              </div>
              <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $c['status'])) ?>">
                <?= htmlspecialchars($c['status']) ?>
              </span>
            </div>

            <!-- Resolution Timeline Bar -->
            <div class="timeline-stepper">
              <?php
                $statusList = ['Pending', 'Under Review', 'In Progress', 'Resolved'];
                $currIdx = array_search($c['status'], $statusList);
                if ($currIdx === false) $currIdx = 0;
              ?>
              <?php foreach ($statusList as $idx => $st): ?>
                <div class="step-item <?= $idx <= $currIdx ? 'active' : '' ?> <?= $idx === $currIdx ? 'current' : '' ?>">
                  <div class="step-circle"><?= $idx <= $currIdx ? '✓' : ($idx + 1) ?></div>
                  <div class="step-label"><?= $st ?></div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Grievance Information Grid -->
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">Student Name</span>
                <span class="info-val"><?= htmlspecialchars($c['student_name']) ?> (<?= htmlspecialchars($c['roll_number']) ?>)</span>
              </div>
              <div class="info-item">
                <span class="info-label">Department</span>
                <span class="info-val"><?= htmlspecialchars($c['department']) ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Category</span>
                <span class="info-val"><?= htmlspecialchars($c['category']) ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Priority Level</span>
                <span class="priority-badge priority-<?= strtolower($c['priority']) ?>"><?= htmlspecialchars($c['priority']) ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Submitted On</span>
                <span class="info-val"><?= date('F d, Y - H:i', strtotime($c['created_at'])) ?></span>
              </div>
              <div class="info-item">
                <span class="info-label">Last Updated</span>
                <span class="info-val"><?= date('F d, Y - H:i', strtotime($c['updated_at'])) ?></span>
              </div>
            </div>

            <!-- Description Box -->
            <div class="description-box">
              <span class="box-title">Grievance Description:</span>
              <p><?= nl2br(htmlspecialchars($c['description'])) ?></p>
            </div>

            <!-- Official Administration Remarks -->
            <div class="remarks-box">
              <span class="box-title">🏛️ Official Administration Response / Action Taken:</span>
              <p><?= nl2br(htmlspecialchars($c['admin_remarks'])) ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <footer class="footer">
    <div class="container footer-content">
      <p><strong>CampusVoice &bull; Automated College Grievance Redressal System</strong></p>
      <p class="copyright">&copy; Academic Year 2026-2027 &bull; Capstone DevOps Practical Examination</p>
    </div>
  </footer>
</body>
</html>
