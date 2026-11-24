<?php
require_once __DIR__ . '/db.php';

// Handle filter
$filterStatus = $_GET['status'] ?? '';
$filterDept = $_GET['dept'] ?? '';

$sql = "SELECT * FROM complaints WHERE 1=1";
$params = [];

if ($filterStatus) {
    $sql .= " AND status = ?";
    $params[] = $filterStatus;
}
if ($filterDept) {
    $sql .= " AND department = ?";
    $params[] = $filterDept;
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

// Counts
$total = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
$inProgress = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('Under Review', 'In Progress')")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration Console - CampusVoice</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <header class="navbar">
    <div class="nav-container">
      <div class="brand">
        <span class="brand-icon">🏛️</span>
        <div class="brand-text">
          <span class="brand-title">CampusVoice Admin</span>
          <span class="brand-subtitle">Grievance Redressal Coordinator Desk</span>
        </div>
      </div>
      <nav class="nav-links">
        <a href="index.php">Student Portal</a>
        <a href="track.php">Track Status</a>
        <a href="admin.php" class="active">Admin Desk</a>
        <a href="health.php" target="_blank" class="badge-health">Health API</a>
      </nav>
    </div>
  </header>

  <main class="container">
    <section class="admin-top-bar">
      <div>
        <h2>Grievance Administration & Workflow Desk</h2>
        <p>Review student submissions, assign investigations, and post official resolution updates.</p>
      </div>
      <div class="quick-stats-pills">
        <span class="pill-stat total">Total: <?= $total ?></span>
        <span class="pill-stat pending">Pending: <?= $pending ?></span>
        <span class="pill-stat progress">In Progress: <?= $inProgress ?></span>
        <span class="pill-stat resolved">Resolved: <?= $resolved ?></span>
      </div>
    </section>

    <!-- Filters Strip -->
    <div class="card filter-card">
      <form method="GET" action="admin.php" class="filter-form">
        <div class="filter-group">
          <label>Status Filter:</label>
          <select name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="Pending" <?= $filterStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Under Review" <?= $filterStatus === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
            <option value="In Progress" <?= $filterStatus === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="Resolved" <?= $filterStatus === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Department:</label>
          <select name="dept" onchange="this.form.submit()">
            <option value="">All Departments</option>
            <option value="Computer Science" <?= $filterDept === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
            <option value="Information Technology" <?= $filterDept === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
            <option value="Electronics" <?= $filterDept === 'Electronics' ? 'selected' : '' ?>>Electronics</option>
            <option value="Mechanical" <?= $filterDept === 'Mechanical' ? 'selected' : '' ?>>Mechanical</option>
            <option value="Civil" <?= $filterDept === 'Civil' ? 'selected' : '' ?>>Civil</option>
            <option value="MBA" <?= $filterDept === 'MBA' ? 'selected' : '' ?>>MBA</option>
          </select>
        </div>

        <?php if ($filterStatus || $filterDept): ?>
          <a href="admin.php" class="btn btn-outline btn-sm">Clear Filters</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Grievances Table -->
    <div class="card table-card">
      <div class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Ticket ID</th>
              <th>Student</th>
              <th>Department</th>
              <th>Category</th>
              <th>Subject</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($complaints)): ?>
              <tr>
                <td colspan="8" class="text-center py-4">No grievances found matching the selected filters.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($complaints as $c): ?>
                <tr>
                  <td><code><?= htmlspecialchars($c['ticket_id']) ?></code></td>
                  <td>
                    <strong><?= htmlspecialchars($c['student_name']) ?></strong><br>
                    <small class="text-muted"><?= htmlspecialchars($c['roll_number']) ?></small>
                  </td>
                  <td><?= htmlspecialchars($c['department']) ?></td>
                  <td><span class="badge-cat"><?= htmlspecialchars($c['category']) ?></span></td>
                  <td class="col-subject" title="<?= htmlspecialchars($c['description']) ?>">
                    <?= htmlspecialchars($c['subject']) ?>
                  </td>
                  <td>
                    <span class="priority-badge priority-<?= strtolower($c['priority']) ?>">
                      <?= htmlspecialchars($c['priority']) ?>
                    </span>
                  </td>
                  <td>
                    <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $c['status'])) ?>">
                      <?= htmlspecialchars($c['status']) ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-secondary open-update-modal" 
                            data-ticket="<?= htmlspecialchars($c['ticket_id']) ?>"
                            data-student="<?= htmlspecialchars($c['student_name']) ?>"
                            data-subject="<?= htmlspecialchars($c['subject']) ?>"
                            data-status="<?= htmlspecialchars($c['status']) ?>"
                            data-remarks="<?= htmlspecialchars($c['admin_remarks']) ?>">
                      Action
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Admin Action Modal -->
  <div id="adminModal" class="modal-overlay">
    <div class="modal-box modal-lg">
      <div class="modal-header">
        <h3>Update Grievance Status & Response</h3>
        <button id="closeAdminModal" class="btn-close">&times;</button>
      </div>
      <form id="adminUpdateForm" class="modal-form">
        <input type="hidden" id="modal_ticket_id" name="ticket_id">
        
        <div class="form-group">
          <label>Ticket Reference:</label>
          <div id="modal_ticket_display" class="ticket-display-inline"></div>
        </div>

        <div class="form-group">
          <label for="modal_status">Change Resolution Status *</label>
          <select id="modal_status" name="status" required>
            <option value="Pending">Pending (Awaiting Initial Triage)</option>
            <option value="Under Review">Under Review (Assigned to Coordinator)</option>
            <option value="In Progress">In Progress (Maintenance / Action Underway)</option>
            <option value="Resolved">Resolved (Action Completed & Closed)</option>
          </select>
        </div>

        <div class="form-group">
          <label for="modal_remarks">Official Administrative Response / Remarks *</label>
          <textarea id="modal_remarks" name="admin_remarks" rows="4" placeholder="Detail action taken, work orders issued, or resolution notes visible to the student..." required></textarea>
        </div>

        <div class="modal-actions">
          <button type="submit" class="btn btn-primary" id="saveActionBtn">Save & Broadcast Update</button>
          <button type="button" id="cancelModalBtn" class="btn btn-outline">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="footer">
    <div class="container footer-content">
      <p><strong>CampusVoice &bull; Automated College Grievance Redressal System</strong></p>
      <p class="copyright">&copy; Academic Year 2026-2027 &bull; Capstone DevOps Practical Examination</p>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
