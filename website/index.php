<?php
require_once __DIR__ . '/db.php';

// Fetch summary metrics
$total = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
$inProgress = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('Under Review', 'In Progress')")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();

// Fetch recent 5 complaints
$recent = $pdo->query("SELECT ticket_id, category, priority, subject, status, created_at FROM complaints ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Grievance & Complaint Redressal Portal</title>
  <link rel="stylesheet" href="style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <!-- Header / Navigation Bar -->
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
        <a href="index.php" class="active">Submit Grievance</a>
        <a href="track.php">Track Status</a>
        <a href="admin.php">Admin Desk</a>
        <a href="health.php" target="_blank" class="badge-health">Health API</a>
      </nav>
    </div>
  </header>

  <!-- DevOps Infrastructure Banner -->
  <div class="devops-bar">
    <div class="container devops-flex">
      <div class="devops-left">
        <span class="live-pulse"></span>
        <span><strong>Cloud Infrastructure Status:</strong> All systems operational on AWS (ap-south-1)</span>
      </div>
      <div class="devops-tags">
        <span class="tag tf">Terraform IaC</span>
        <span class="tag puppet">Puppet Apache+PHP</span>
        <span class="tag ansible">Ansible App Deploy</span>
        <span class="tag nagios">Nagios Core Monitoring</span>
      </div>
    </div>
  </div>

  <main class="container">
    <!-- Hero & Analytics Counters -->
    <section class="metrics-grid">
      <div class="metric-card">
        <span class="metric-icon">📋</span>
        <div class="metric-info">
          <span class="metric-num"><?= $total ?></span>
          <span class="metric-label">Total Grievances</span>
        </div>
      </div>
      <div class="metric-card pending">
        <span class="metric-icon">⏳</span>
        <div class="metric-info">
          <span class="metric-num"><?= $pending ?></span>
          <span class="metric-label">Pending Action</span>
        </div>
      </div>
      <div class="metric-card progress">
        <span class="metric-icon">⚙️</span>
        <div class="metric-info">
          <span class="metric-num"><?= $inProgress ?></span>
          <span class="metric-label">Under Investigation</span>
        </div>
      </div>
      <div class="metric-card resolved">
        <span class="metric-icon">✅</span>
        <div class="metric-info">
          <span class="metric-num"><?= $resolved ?></span>
          <span class="metric-label">Resolved & Closed</span>
        </div>
      </div>
    </section>

    <!-- Main Content: Form + Quick Track -->
    <div class="layout-grid">
      <!-- Left Column: Grievance Submission Form -->
      <section class="card form-card">
        <div class="card-header">
          <h2>Lodge a New Grievance</h2>
          <p>Submit your academic, facility, or campus concerns directly to the administration.</p>
        </div>

        <form id="grievanceForm" class="grievance-form">
          <div class="form-row">
            <div class="form-group">
              <label for="student_name">Full Name *</label>
              <input type="text" id="student_name" name="student_name" placeholder="e.g. Aryan Kumar" required>
            </div>
            <div class="form-group">
              <label for="roll_number">Roll / Registration Number *</label>
              <input type="text" id="roll_number" name="roll_number" placeholder="e.g. 22CS085" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="email">Student College Email *</label>
              <input type="email" id="email" name="email" placeholder="student@college.edu" required>
            </div>
            <div class="form-group">
              <label for="department">Department *</label>
              <select id="department" name="department" required>
                <option value="">Select Department...</option>
                <option value="Computer Science">Computer Science & Engineering</option>
                <option value="Information Technology">Information Technology</option>
                <option value="Electronics">Electronics & Communication</option>
                <option value="Mechanical">Mechanical Engineering</option>
                <option value="Civil">Civil Engineering</option>
                <option value="MBA">Management Studies</option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="category">Grievance Category *</label>
              <select id="category" name="category" required>
                <option value="">Select Category...</option>
                <option value="Academic">Academic / Faculty</option>
                <option value="Hostel & Mess">Hostel, Room & Mess</option>
                <option value="Infrastructure & Labs">Infrastructure & Computer Labs</option>
                <option value="Examination">Examinations & Grading</option>
                <option value="Library">Library & Digital Resources</option>
                <option value="Transport & Canteen">Campus Amenities & Transport</option>
              </select>
            </div>
            <div class="form-group">
              <label for="priority">Urgency / Priority *</label>
              <select id="priority" name="priority" required>
                <option value="Low">Low (General suggestion)</option>
                <option value="Medium" selected>Medium (Standard grievance)</option>
                <option value="High">High (Requires prompt action)</option>
                <option value="Urgent">Urgent (Safety / Critical issue)</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="subject">Subject / Brief Title *</label>
            <input type="text" id="subject" name="subject" placeholder="e.g. Faulty Projector in Lecture Hall 204" required>
          </div>

          <div class="form-group">
            <label for="description">Detailed Description *</label>
            <textarea id="description" name="description" rows="5" placeholder="Describe the grievance clearly, including dates, room numbers, and specific impacts..." required></textarea>
          </div>

          <button type="submit" class="btn btn-primary" id="submitBtn">
            <span>Submit Grievance</span>
            <span class="btn-arrow">→</span>
          </button>
        </form>
      </section>

      <!-- Right Column: Quick Tracker & Live Recent Submissions -->
      <aside class="sidebar-cards">
        <!-- Quick Tracker Box -->
        <div class="card track-card">
          <div class="card-header">
            <h3>Track Your Grievance</h3>
            <p>Enter your Ticket ID or Roll Number</p>
          </div>
          <form action="track.php" method="GET" class="quick-track-form">
            <div class="track-input-group">
              <input type="text" name="query" placeholder="e.g. GRV-2026-1001 or 22CS104" required>
              <button type="submit" class="btn btn-secondary">Track</button>
            </div>
          </form>
        </div>

        <!-- Recent Public Grievance Feed -->
        <div class="card feed-card">
          <div class="card-header">
            <h3>Recent Grievances</h3>
            <span class="badge-subtle">Live Updates</span>
          </div>
          <div class="feed-list">
            <?php foreach ($recent as $r): ?>
              <div class="feed-item">
                <div class="feed-top">
                  <span class="ticket-tag"><?= htmlspecialchars($r['ticket_id']) ?></span>
                  <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $r['status'])) ?>">
                    <?= htmlspecialchars($r['status']) ?>
                  </span>
                </div>
                <div class="feed-title"><?= htmlspecialchars($r['subject']) ?></div>
                <div class="feed-meta">
                  <span><?= htmlspecialchars($r['category']) ?></span> &bull; 
                  <span><?= date('M d, H:i', strtotime($r['created_at'])) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <!-- Success Modal for Submitted Ticket -->
  <div id="successModal" class="modal-overlay">
    <div class="modal-box">
      <div class="modal-icon">🎉</div>
      <h3>Grievance Lodged Successfully!</h3>
      <p>Your grievance has been assigned a unique reference ticket ID. Please save it for tracking:</p>
      <div class="ticket-display">
        <span id="newTicketId">GRV-2026-XXXX</span>
        <button id="copyTicketBtn" class="btn-copy" title="Copy to clipboard">📋</button>
      </div>
      <div class="modal-actions">
        <a id="trackLink" href="#" class="btn btn-primary">Track This Ticket</a>
        <button id="closeModalBtn" class="btn btn-outline">Submit Another</button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer">
    <div class="container footer-content">
      <p><strong>CampusVoice &bull; College Student Grievance Redressal System</strong></p>
      <p class="devops-credit">
        Engineered & Automated with <strong>Terraform</strong> (AWS Cloud Provisioning), 
        <strong>Puppet</strong> (Apache & PHP Runtime Enforcement), 
        <strong>Ansible</strong> (App Orchestration & Database Setup), and 
        <strong>Nagios Core</strong> (Continuous Telemetry & SLA Monitoring).
      </p>
      <p class="copyright">&copy; Academic Year 2026-2027 &bull; Capstone DevOps Practical Examination</p>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
