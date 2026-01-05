<?php

session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Access denied. Only admins can view this page.");
}

function getSubjectsByYearSem(mysqli $conn, int $year, int $sem) {
    $sql = "
        SELECT s.subject_id,
               s.subject_name,
               s.year_level,
               s.semester,
               p.program_name,
               CONCAT(u.first_name, ' ', u.last_name) AS adviser_name,
               ps.subject_name AS prerequisite_name
        FROM subjects s
        LEFT JOIN programs p ON s.program_id = p.program_id
        LEFT JOIN users u ON s.adviser_id = u.user_id
        LEFT JOIN subjects ps ON s.prerequisite_subject_id = ps.subject_id
        WHERE s.year_level = ? AND s.semester = ?
        ORDER BY p.program_name, s.subject_name
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $year, $sem);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        $rows[] = $row;
    }
    $stmt->close();
    return $rows;
}

$grid = [];
for ($y = 1; $y <= 4; $y++) {
    for ($s = 1; $s <= 2; $s++) {
        $grid["$y-$s"] = getSubjectsByYearSem($conn, $y, $s);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subjects</title>
    <style>
        body { 
            font-family: Arial, sans-serif; }

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

        .table-title { 
            font-weight: bold; 
            margin-bottom: 8px; }

        .muted { 
            color: #678; }
    </style>
</head>
<body>
<div class="container">
    <h2>Subjects</h2>

    <?php for ($y = 1; $y <= 4; $y++): ?>
        <?php for ($s = 1; $s <= 2; $s++): ?>
            <div class="card">
                <div class="table-title">
                    <?= $y ?><?= ($y==1?'st':($y==2?'nd':($y==3?'rd':'th'))) ?> Year — <?= $s ?><?= ($s==1?'st':'nd') ?> Semester
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Adviser</th>
                            <th>Program</th>
                            <th>Subject</th>
                            <th>Prerequisite</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($grid["$y-$s"])): ?>
                        <?php foreach ($grid["$y-$s"] as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['adviser_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['program_name'] ?? '—') ?></td>
                                <td>
                                    <a href="subject_edit.php?id=<?= (int)$row['subject_id'] ?>">
                                        <?= htmlspecialchars($row['subject_name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['prerequisite_name'] ?? '—') ?></td>
                                <td>
                                    <a class="btn-link" href="subject_edit.php?id=<?= (int)$row['subject_id'] ?>">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="muted">No subjects found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endfor; ?>
    <?php endfor; ?>

    <p style="margin-top:16px;">
        <a href="index_admin.php" class="btn-link">Back to Dashboard</a>
        <a href="subject_edit.php" class="btn-link">Edit</a>
    </p>
</div>
</body>
</html>
