<?php

session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Access denied. Only admins can view this page.");
}

$faculty_sql = "
    SELECT u.email,
           CONCAT(u.first_name,' ',u.last_name) AS full_name,
           p.program_name,
           s.subject_name
    FROM subjects s
    LEFT JOIN users u ON s.adviser_id = u.user_id
    LEFT JOIN programs p ON s.program_id = p.program_id
    WHERE u.role_id = 2
    ORDER BY full_name, s.subject_name
";
$faculty_res = $conn->query($faculty_sql);

$enrolled_sql = "
    SELECT u.email,
           CONCAT(u.first_name,' ',u.last_name) AS full_name,
           p.program_name,
           s.year_level,
           'Enrolled' AS status
    FROM enrolled e
    INNER JOIN users u ON e.student_id = u.user_id
    LEFT JOIN programs p ON u.program_id = p.program_id
    LEFT JOIN subjects s ON e.subject_id = s.subject_id
    ORDER BY s.year_level, full_name
";
$enrolled_res = $conn->query($enrolled_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrollment Oversight</title>
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
    <h2>Enrollment Oversight</h2>

    <div class="card">
        <div class="table-title">Faculty</div>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Subject</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($faculty_res && $faculty_res->num_rows > 0): ?>
                <?php while ($row = $faculty_res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" class="muted">No faculty assigned.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <div class="table-title">Enrolled Students</div>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Year</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($enrolled_res && $enrolled_res->num_rows > 0): ?>
                <?php while ($row = $enrolled_res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['program_name']) ?></td>
                        <td><?= htmlspecialchars($row['year_level']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="muted">No enrolled students.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <p style="margin-top:16px;">
        <a href="index_admin.php" class="btn-link">Back to Dashboard</a>
    </p>
</div>
</body>
</html>
