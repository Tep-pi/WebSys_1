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

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_grade'])) {
    $studentId = (int)$_POST['student_id'];
    $subjectId = (int)$_POST['subject_id'];
    $grade     = trim($_POST['grade']);

    $stmt = $conn->prepare("UPDATE enrolled SET grade=? WHERE student_id=? AND subject_id=?");
    $stmt->bind_param("sii", $grade, $studentId, $subjectId);
    if ($stmt->execute()) {
        $success = "Grade updated successfully.";
    } else {
        $error = "Failed to update grade: " . $conn->error;
    }
    $stmt->close();
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
    <title>Submit Student Grades</title>
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

        input[type="text"] { width:80px; padding:6px; }

        button { padding:6px 12px; margin-left:6px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Submit Student Grades</h2>

    <?php if ($error): ?><p style="color:#b00020"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <?php if (!empty($subjects)): ?>
        <?php foreach ($subjects as $sub): ?>
            <div class="card">
                <div class="table-title">
                    <?= htmlspecialchars($sub['subject_name']) ?> — <?= htmlspecialchars($sub['program_name']) ?>
                    (<?= htmlspecialchars($sub['time_slot'] ?? 'No time set') ?>)
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Grade</th>
                            <th>Subject</th>
                            <th>Program</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $enrolled_sql = "
                        SELECT e.student_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS full_name, e.grade
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
                            <form method="post">
                                <td>
                                    <input type="text" name="grade" value="<?= htmlspecialchars($row['grade'] ?? '') ?>">
                                </td>
                                <td><?= htmlspecialchars($sub['subject_name']) ?></td>
                                <td><?= htmlspecialchars($sub['program_name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td>
                                    <input type="hidden" name="student_id" value="<?= (int)$row['student_id'] ?>">
                                    <input type="hidden" name="subject_id" value="<?= (int)$sub['subject_id'] ?>">
                                    <button type="submit" name="save_grade">Save</button>
                                </td>
                            </form>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr><td colspan="6" class="muted">No students enrolled in this subject.</td></tr>
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
