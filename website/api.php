<?php
// ==============================================================================
// REST API Endpoint for Grievance Redressal Operations
// ==============================================================================

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

$action = $_REQUEST['action'] ?? '';

try {
    switch ($action) {
        case 'submit':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method. POST required.');
            }

            $name = trim($_POST['student_name'] ?? '');
            $roll = strtoupper(trim($_POST['roll_number'] ?? ''));
            $email = trim($_POST['email'] ?? '');
            $dept = trim($_POST['department'] ?? '');
            $cat = trim($_POST['category'] ?? '');
            $priority = trim($_POST['priority'] ?? 'Medium');
            $subject = trim($_POST['subject'] ?? '');
            $desc = trim($_POST['description'] ?? '');

            if (!$name || !$roll || !$email || !$dept || !$cat || !$subject || !$desc) {
                throw new Exception('All required fields must be completed.');
            }

            // Generate unique ticket ID: GRV-YEAR-RANDOM
            $ticketId = 'GRV-' . date('Y') . '-' . rand(1100, 9999);

            $stmt = $pdo->prepare("INSERT INTO complaints 
                (ticket_id, student_name, roll_number, email, department, category, priority, subject, description, status, admin_remarks, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending', 'Complaint logged into automated queue. Department assigned.', datetime('now'), datetime('now'))");
            
            $stmt->execute([$ticketId, $name, $roll, $email, $dept, $cat, $priority, $subject, $desc]);

            echo json_encode([
                'success' => true,
                'message' => 'Grievance submitted successfully!',
                'ticket_id' => $ticketId,
                'status' => 'Pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            break;

        case 'track':
            $query = trim($_GET['query'] ?? '');
            if (!$query) {
                throw new Exception('Please provide a Ticket ID or Roll Number to track.');
            }

            $stmt = $pdo->prepare("SELECT * FROM complaints WHERE ticket_id = ? OR UPPER(roll_number) = ? ORDER BY id DESC");
            $stmt->execute([$query, strtoupper($query)]);
            $results = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'count' => count($results),
                'complaints' => $results
            ]);
            break;

        case 'update_status':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method.');
            }

            $ticketId = trim($_POST['ticket_id'] ?? '');
            $status = trim($_POST['status'] ?? '');
            $remarks = trim($_POST['admin_remarks'] ?? '');

            if (!$ticketId || !$status) {
                throw new Exception('Ticket ID and status are required.');
            }

            $stmt = $pdo->prepare("UPDATE complaints SET status = ?, admin_remarks = ?, updated_at = datetime('now') WHERE ticket_id = ?");
            $stmt->execute([$status, $remarks, $ticketId]);

            echo json_encode([
                'success' => true,
                'message' => "Ticket {$ticketId} updated to {$status}."
            ]);
            break;

        case 'get_stats':
            $total = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
            $pending = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
            $inProgress = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('Under Review', 'In Progress')")->fetchColumn();
            $resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();

            echo json_encode([
                'success' => true,
                'total' => (int)$total,
                'pending' => (int)$pending,
                'in_progress' => (int)$inProgress,
                'resolved' => (int)$resolved
            ]);
            break;

        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid API action. Available: submit, track, update_status, get_stats'
            ]);
            break;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
