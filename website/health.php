<?php
// ==============================================================================
// Health Check Endpoint (Polled by Nagios Core Monitoring Plugin)
// URL: http://<TARGET_SERVER_IP>/health.php
// Expected Response: HTTP 200 with JSON payload {"status":"OK", ...}
// ==============================================================================

header('Content-Type: application/json; charset=utf-8');

$startTime = microtime(true);
$health = [
    'status'       => 'OK',
    'service'      => 'Student Complaint & Grievance Redressal System',
    'version'      => '2.4.0-prod',
    'environment'  => 'AWS EC2 ap-south-1 (Production)',
    'managed_by'   => 'Puppet & Ansible',
    'monitored_by' => 'Nagios Core 4.x',
    'web_server'   => $_SERVER['SERVER_SOFTWARE'] ?? 'Apache/2.4 (Ubuntu)',
    'php_version'  => PHP_VERSION,
    'database'     => 'UNKNOWN',
    'metrics'      => [],
    'response_ms'  => 0
];

try {
    require_once __DIR__ . '/db.php';
    
    // Test query execution
    $total = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
    $pending = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Pending'")->fetchColumn();
    $inProgress = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('Under Review', 'In Progress')")->fetchColumn();
    $resolved = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status = 'Resolved'")->fetchColumn();

    $health['database'] = 'CONNECTED';
    $health['metrics'] = [
        'total_complaints' => (int)$total,
        'pending'          => (int)$pending,
        'in_progress'      => (int)$inProgress,
        'resolved'         => (int)$resolved,
        'db_file_bytes'    => file_exists($dbPath) ? filesize($dbPath) : 0
    ];

    http_response_code(200);

} catch (Exception $e) {
    $health['status'] = 'CRITICAL';
    $health['database'] = 'ERROR: ' . $e->getMessage();
    http_response_code(500);
}

$health['response_ms'] = round((microtime(true) - $startTime) * 1000, 2);
echo json_encode($health, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
