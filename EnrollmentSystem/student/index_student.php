<?php

session_start();
require_once __DIR__ . "/../db/db.php";


if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied. Only students can view this page.");
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../login/login.php");
    exit;
}


$me = null;
$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f6f8fb; }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; }

        .header { 
            margin-bottom: 16px; }

        .muted { 
            color:#678; }

        .row { 
            display: flex; 
            gap: 16px; 
            margin-bottom: 16px; }

        .card { 
            flex: 1; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 16px; 
            background: #fff; }

        .card h3 { 
            margin: 0 0 8px 0; 
            font-size: 18px; }

        .card p { 
            margin: 0 0 12px 0; }

        .btn-link { 
            display:inline-block; 
            margin:6px 8px 0 0; 
            padding:8px 14px; 
            background:#007bff; 
            color:#fff; 
            text-decoration:none; 
            border-radius:4px; 
            font-size:14px; }

        .btn-link:hover { 
            background:#0056b3; }

        .actions { 
            margin-top: 8px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>Student Dashboard</h2>
        <?php if ($me): ?>
            <p class="muted">Signed in as <?= htmlspecialchars($me['first_name'] . ' ' . $me['last_name']) ?> (<?= htmlspecialchars($me['email']) ?>)</p>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="card">
            <h3>Profile</h3>
            <p>View and update your profile information, including profile picture and signature.</p>
            <div class="actions">
                <a class="btn-link" href="student_profile.php">Open profile</a>
            </div>
        </div>

        <div class="card">
            <h3>Enroll</h3>
            <p>Enroll in available subjects after prerequisite checking.</p>
            <div class="actions">
                <a class="btn-link" href="enroll.php">Enroll subjects</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h3>Prerequisite Checker</h3>
            <p>Check if you are eligible to enroll in subjects based on your grades.</p>
            <div class="actions">
                <a class="btn-link" href="prereq_checker.php">Check prerequisites</a>
            </div>
        </div>

        <div class="card">
            <h3>Enrollment Status</h3>
            <p>View your current enrollment status and subjects you are enrolled in.</p>
            <div class="actions">
                <a class="btn-link" href="enrolled.php">View enrollment</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
