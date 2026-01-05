<?php

session_start();
require_once __DIR__ . "/../db/db.php";


if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    die("Access denied. Only faculty can view this page.");
}

$studentId = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$studentId) {
    die("No student selected.");
}

$stmt = $conn->prepare("
    SELECT u.first_name, u.last_name, u.email, u.profile_image_path, u.signature_image_path, p.program_name
    FROM users u
    LEFT JOIN programs p ON u.program_id = p.program_id
    WHERE u.user_id = ? AND u.role_id = 3
    LIMIT 1
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

$name = $student['first_name'] . " " . $student['last_name'];
$email = $student['email'];
$program = $student['program_name'] ?? '—';
$pfpRel = $student['profile_image_path'] ?? null;
$sigRel = $student['signature_image_path'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Profile (View Only)</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f6f8fb; }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; }

        .card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 16px; 
            background: #fff; }

        .row { 
            display: flex; 
            gap: 24px; 
            align-items: flex-start; }

        .col { 
            flex: 1; }

        .label { 
            font-weight: bold; 
            width: 160px; 
            display: inline-block; }

        .avatar { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            background: #f7f7f7; }

        .muted { 
            color: #678; }

        .btn-link {
            display:inline-block;
            margin:6px 8px 0 0;
            padding:8px 14px;
            background:#007bff;
            color:#fff;
            text-decoration:none;
            border-radius:4px;
            font-size:14px;
        }

        .btn-link:hover { 
            background:#0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Profile (View Only)</h2>

    <div class="card">
        <div class="row">

            <div class="col">
                <p><span class="label">Name:</span> <?= htmlspecialchars($name) ?></p>
                <p><span class="label">Email:</span> <?= htmlspecialchars($email) ?></p>
                <p><span class="label">Program:</span> <?= htmlspecialchars($program) ?></p>
            </div>

            <div class="col">
                <p class="muted">Profile picture</p>
                <?php if (!empty($pfpRel)): ?>
                    <img class="avatar" src="../<?= htmlspecialchars($pfpRel) ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No profile picture uploaded.</p>
                <?php endif; ?>

                <p class="muted" style="margin-top:16px;">Signature</p>
                <?php if (!empty($sigRel)): ?>
                    <img class="avatar" src="../<?= htmlspecialchars($sigRel) ?>" alt="Signature">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No signature uploaded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <p style="margin-top:16px;">
        <a href="class_list.php" class="btn-link">Back to Class List</a>
        <a href="index_faculty.php" class="btn-link">Dashboard</a>
    </p>
</div>
</body>
</html>
