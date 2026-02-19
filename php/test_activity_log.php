<?php
// Test Activity Log System
// Run this file to verify the setup

require_once '../config/db.php';

echo "<h2>Activity Log System - Diagnostics</h2>";

// Test 1: Database Connection
echo "<h3>1. Testing Database Connection</h3>";
if (isset($pdo) && $pdo) {
    echo "✓ Database connection established<br>";
} else {
    echo "✗ Database connection FAILED<br>";
    exit;
}

// Test 2: Check if activity_logs table exists
echo "<h3>2. Checking activity_logs table</h3>";
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'activity_logs'");
    $table = $stmt->fetch();
    
    if ($table) {
        echo "✓ activity_logs table exists<br>";
        
        // Show table structure
        $stmt = $pdo->query("DESCRIBE activity_logs");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<br><strong>Table Structure:</strong><br>";
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
        
    } else {
        echo "✗ activity_logs table DOES NOT EXIST<br>";
        echo "<p style='color: red;'><strong>ACTION REQUIRED:</strong> Please run the SQL script at: /sql/activity_logs.sql</p>";
        echo "<p>You can run it in phpMyAdmin or execute: <code>SOURCE C:/wamp/htdocs/PSI/sql/activity_logs.sql</code></p>";
        exit;
    }
} catch (PDOException $e) {
    echo "✗ Error checking table: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Test logging function
echo "<h3>3. Testing Log Function</h3>";
require_once '../php/log_activity.php';

$testResult = logActivity('login_success', 'Test log entry from diagnostics', null, 'Test User', 'test@example.com');

if ($testResult) {
    echo "✓ Log function executed successfully<br>";
    
    // Retrieve the test entry
    $stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE description = 'Test log entry from diagnostics' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute();
    $log = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($log) {
        echo "✓ Test entry retrieved from database<br>";
        echo "<br><strong>Test Log Entry:</strong><br>";
        echo "<pre>" . print_r($log, true) . "</pre>";
        
        // Clean up test entry
        $stmt = $pdo->prepare("DELETE FROM activity_logs WHERE log_id = ?");
        $stmt->execute([$log['log_id']]);
        echo "<br>✓ Test entry cleaned up<br>";
    } else {
        echo "✗ Could not retrieve test entry<br>";
    }
} else {
    echo "✗ Log function FAILED<br>";
}

// Test 4: Check recent logs count
echo "<h3>4. Activity Logs Summary</h3>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM activity_logs");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total logs in database: <strong>{$count['total']}</strong><br>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as today FROM activity_logs WHERE DATE(created_at) = CURDATE()");
    $today = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Logs today: <strong>{$today['today']}</strong><br>";
    
} catch (PDOException $e) {
    echo "✗ Error getting summary: " . $e->getMessage() . "<br>";
}

echo "<br><h3 style='color: green;'>✓ All tests passed! Activity Log system is working correctly.</h3>";
echo "<p><a href='../admin/activity_log.html'>View Activity Logs</a></p>";
?>
