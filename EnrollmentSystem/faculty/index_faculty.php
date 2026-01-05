<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    die("Access denied. Only faculty can view this page.");
}

$user_id = $_SESSION['user_id'] ?? null;

$me = null;
if ($user_id) {
    $stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $me = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Dashboard</title>
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
        <h2>Faculty Dashboard</h2>
        <?php if ($me): ?>
            <p class="muted">Signed in as <?= htmlspecialchars($me['first_name'] . ' ' . $me['last_name']) ?> (<?= htmlspecialchars($me['email']) ?>)</p>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="card">
            <h3>Profile</h3>
            <p>View and update your profile information, including profile picture and signature.</p>
            <div class="actions">
                <a class="btn-link" href="faculty_profile.php">Open profile</a>
            </div>
        </div>

        <div class="card">
            <h3>Subject list</h3>
            <p>See the subjects you handle and their assigned schedule.</p>
            <div class="actions">
                <a class="btn-link" href="subject_list.php">View subjects</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="card">
            <h3>Class list</h3>
            <p>Browse enrolled students for each of your assigned subjects.</p>
            <div class="actions">
                <a class="btn-link" href="class_list.php">Open class lists</a>
            </div>
        </div>

        <div class="card">
            <h3>Submit grades</h3>
            <p>Record grades for your students per subject and program.</p>
            <div class="actions">
                <a class="btn-link" href="submit_student_grade.php">Submit grades</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
