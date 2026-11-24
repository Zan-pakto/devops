<?php
// ==============================================================================
// Database Connection & Initialization: SQLite3 PDO
// ==============================================================================

$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0775, true);
}

$dbPath = $dataDir . '/complaints.db';

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create Complaints table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS complaints (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ticket_id TEXT UNIQUE NOT NULL,
        student_name TEXT NOT NULL,
        roll_number TEXT NOT NULL,
        email TEXT NOT NULL,
        department TEXT NOT NULL,
        category TEXT NOT NULL,
        priority TEXT NOT NULL,
        subject TEXT NOT NULL,
        description TEXT NOT NULL,
        status TEXT NOT NULL DEFAULT 'Pending',
        admin_remarks TEXT DEFAULT 'Complaint registered. Awaiting initial review by department coordinator.',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed initial sample grievances if table is empty
    $count = $pdo->query("SELECT COUNT(*) FROM complaints")->fetchColumn();
    if ($count == 0) {
        $stmt = $pdo->prepare("INSERT INTO complaints 
            (ticket_id, student_name, roll_number, email, department, category, priority, subject, description, status, admin_remarks, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $seeds = [
            [
                'GRV-2026-1001', 'Rahul Sharma', '22CS104', 'rahul.s@college.edu', 'Computer Science',
                'Infrastructure & Labs', 'High', 'Lab 3 Workstation OS Kernel Crash',
                'Systems in Lab 3 Row B constantly reboot during cloud computing practical lab sessions. Requires OS reinstallation and driver updates.',
                'Resolved', 'All 12 machines re-imaged with Ubuntu 22.04 LTS via Puppet configuration management. Verified operational.',
                date('Y-m-d H:i:s', strtotime('-4 days')), date('Y-m-d H:i:s', strtotime('-1 days'))
            ],
            [
                'GRV-2026-1002', 'Priya Patel', '22IT089', 'priya.p@college.edu', 'Information Technology',
                'Hostel & Mess', 'Medium', 'Hostel Block B Wi-Fi Access Point Frequent Disconnections',
                'Wi-Fi signal drops completely after 9:00 PM on the 2nd floor of Block B. Affects evening study hours and project research.',
                'In Progress', 'Network team inspected router firmware. Upgrading wireless access points to dual-band PoE units tomorrow.',
                date('Y-m-d H:i:s', strtotime('-2 days')), date('Y-m-d H:i:s', strtotime('-5 hours'))
            ],
            [
                'GRV-2026-1003', 'Ananya Verma', '23EC045', 'ananya.v@college.edu', 'Electronics',
                'Academic', 'Low', 'Digital Signal Processing Reference Books in Library',
                'Requested additional copies of Oppenheim Discrete-Time Signal Processing in the central departmental library section.',
                'Under Review', 'Library committee approved procurement. Purchase order dispatched to publisher.',
                date('Y-m-d H:i:s', strtotime('-1 days')), date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'GRV-2026-1004', 'Aditya Singh', '22ME012', 'aditya.s@college.edu', 'Mechanical',
                'Canteen', 'Low', 'Water Dispenser Cooling Filter Replacement',
                'The central water cooler on the ground floor engineering block has low cooling efficiency and requires filter maintenance.',
                'Pending', 'Complaint registered. Dispatched to campus estate maintenance division.',
                date('Y-m-d H:i:s', strtotime('-3 hours')), date('Y-m-d H:i:s', strtotime('-3 hours'))
            ]
        ];

        foreach ($seeds as $s) {
            $stmt->execute($s);
        }
    }

} catch (PDOException $e) {
    die("Database Connection Error: " . $e->getMessage());
}
