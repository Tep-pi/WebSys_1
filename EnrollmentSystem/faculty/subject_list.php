<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    die("Access denied. Only faculty can view this page.");
}

$facultyId = $_SESSION['user_id'] ?? null;
if (!$facultyId) {
    header("Location: ../login/login.php");
    exit;
}

$sql = "
    SELECT s.subject_id,
           s.subject_name,
           s.year_level,
           s.semester,
           s.time_slot,
           p.program_name,
           ps.subject_name AS prerequisite_name,
           t.term_label
    FROM subjects s
    LEFT JOIN programs p ON s.program_id = p.program_id
    LEFT JOIN subjects ps ON s.prerequisite_subject_id = ps.subject_id
    LEFT JOIN academic_terms t ON s.term_id = t.term_id
    WHERE s.adviser_id = ?
    ORDER BY s.year_level, s.semester, s.subject_name
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $facultyId);
$stmt->execute();
$res = $stmt->get_result();
$subjects = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Subjects</title>
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
            background:#fff; 
            margin-bottom: 24px; }

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
    <h2>My Subjects</h2>

    <?php if (!empty($subjects)): ?>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Program</th>
                        <th>Subject</th>
                        <th>Year/Semester</th>
                        <th>Prerequisite</th>
                        <th>Time</th>
                        <th>Term</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($subjects as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['program_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                        <td>
                            <?= $row['year_level'] ?><?= ($row['year_level']==1?'st':($row['year_level']==2?'nd':($row['year_level']==3?'rd':'th'))) ?> Year —
                            <?= $row['semester']=='1'?'1st':'2nd' ?> Semester
                        </td>
                        <td><?= htmlspecialchars($row['prerequisite_name'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['time_slot'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($row['term_label'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="muted">No subjects assigned to you.</p>
    <?php endif; ?>

    <p style="margin-top:16px;">
        <a href="index_faculty.php" class="btn-link">Back to Dashboard</a>
    </p>
</div>
</body>
</html>
