<?php

session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    die("Access denied. Only faculty can view this page.");
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: ../login/login.php");
    exit;
}

$subjects_sql = "
    SELECT s.subject_id, s.subject_name, s.time_slot, p.program_name
    FROM subjects s
    LEFT JOIN programs p ON s.program_id = p.program_id
    WHERE s.adviser_id = ?
    ORDER BY s.year_level, s.semester, s.subject_name
";
$stmt = $conn->prepare($subjects_sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$subjects_res = $stmt->get_result();
$subjects = $subjects_res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Class List</title>
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
            background: #fff; 
            margin-bottom: 24px; }

        .table-title { 
            font-weight: bold; 
            margin-bottom: 8px; }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 12px; }

        th, td { 
            border: 1px solid #ddd; 
            padding: 8px 10px; 
            text-align: left; 
            font-size: 14px; }

        th { 
            background: #f7f7f7; }

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

        .muted { 
            color:#678; }
    </style>
</head>
<body>
<div class="container">
    <h2>Class List</h2>

    <?php if (!empty($subjects)): ?>
        <?php foreach ($subjects as $sub): ?>
            <div class="card">
                <div class="table-title">
                    Subject: <?= htmlspecialchars($sub['subject_name']) ?> — <?= htmlspecialchars($sub['program_name']) ?><br>
                    Time: <?= htmlspecialchars($sub['time_slot'] ?? 'No time set') ?>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $enrolled_sql = "
                        SELECT u.user_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS full_name
                        FROM enrolled e
                        INNER JOIN users u ON e.student_id = u.user_id
                        WHERE e.subject_id = ?
                        ORDER BY full_name
                    ";
                    $stmt = $conn->prepare($enrolled_sql);
                    $stmt->bind_param("i", $sub['subject_id']);
                    $stmt->execute();
                    $enrolled_res = $stmt->get_result();
                    if ($enrolled_res->num_rows > 0):
                        while ($row = $enrolled_res->fetch_assoc()):
                    ?>
                        <tr>
                            <td>
                                <a href="class_student_profile.php?id=<?= (int)$row['user_id'] ?>">
                                    <?= htmlspecialchars($row['full_name']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr><td colspan="2" class="muted">No students enrolled in this subject.</td></tr>
                    <?php endif;
                    $stmt->close();
                    ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="muted">No subjects assigned to you.</p>
    <?php endif; ?>

    <p style="margin-top:16px;">
        <a href="index_faculty.php" class="btn-link">Back to Dashboard</a>
    </p>
</div>
</body>
</html>
