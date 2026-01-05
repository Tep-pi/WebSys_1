<?php

session_start();
require_once __DIR__ . "/../db/db.php";


if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 3) {
    die("Access denied. Only students can view this page.");
}

$studentId = $_SESSION['user_id'] ?? null;
if (!$studentId) {
    header("Location: ../login/login.php");
    exit;
}

$stmt = $conn->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
if (!$stmt) {
    die("Prepare failed (user header): " . $conn->error);
}
$stmt->bind_param("i", $studentId);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$stmt->close();

$sql = "
    SELECT 
        e.enroll_id,
        e.status,
        e.grade,
        s.subject_id,
        s.subject_name,
        s.year_level,
        s.semester,
        s.time_slot,
        p.program_name,
        a.first_name AS adviser_first,
        a.last_name  AS adviser_last,
        a.email      AS adviser_email
    FROM enrolled e
    INNER JOIN subjects s ON e.subject_id = s.subject_id
    LEFT JOIN programs p   ON s.program_id = p.program_id
    LEFT JOIN users a      ON s.adviser_id = a.user_id
    WHERE e.student_id = ?
    ORDER BY s.year_level, s.semester, s.subject_name
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed (enrolled list): " . $conn->error);
}
$stmt->bind_param("i", $studentId);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$groups = [];
foreach ($rows as $r) {
    $key = "Y" . (int)$r['year_level'] . "-S" . (int)$r['semester'];
    if (!isset($groups[$key])) $groups[$key] = [];
    $groups[$key][] = $r;
}

function ordinal($n) {
    $n = (int)$n;
    if ($n === 1) return "1st";
    if ($n === 2) return "2nd";
    if ($n === 3) return "3rd";
    return $n . "th";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Enrollment Status</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f6f8fb; }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; }

        .muted { 
            color:#678; }

        .success { 
            color:#2e7d32; 
            font-weight:bold; }

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

        .badge { 
            display:inline-block; 
            padding:2px 8px; 
            border-radius:12px; 
            font-size:12px; 
            background:#eef; 
            border:1px solid #ccd; }
    </style>
</head>
<body>
<div class="container">
    <h2>Enrollment Status</h2>
    <?php if ($me): ?>
        <p class="muted">Signed in as <?= htmlspecialchars($me['first_name'] . ' ' . $me['last_name']) ?> (<?= htmlspecialchars($me['email']) ?>)</p>
    <?php endif; ?>

    <?php if (!empty($rows)): ?>
        <p class="success">You’re enrolled. Here are your subjects:</p>

        <?php foreach ($groups as $key => $list): 
            [$yLabel, $sLabel] = explode("-", $key);
            $year = (int)substr($yLabel, 1);
            $sem  = (int)substr($sLabel, 1);
        ?>
            <div class="card">
                <div class="table-title"><?= ordinal($year) ?> Year — <?= $sem === 1 ? "1st" : "2nd" ?> Semester</div>
                <table>
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Program</th>
                            <th>Adviser</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $r): 
                        $advName = trim(($r['adviser_first'] ?? '') . ' ' . ($r['adviser_last'] ?? ''));
                        $advDisp = $advName !== '' ? $advName : '—';
                        $time    = $r['time_slot'] ? $r['time_slot'] : '—';
                        $status  = $r['status'] ? $r['status'] : 'enrolled';
                        $grade   = ($r['grade'] !== null && $r['grade'] !== '') ? $r['grade'] : '—';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($r['subject_name']) ?></td>
                            <td><?= htmlspecialchars($r['program_name'] ?? '—') ?></td>
                            <td>
                                <?= htmlspecialchars($advDisp) ?>
                                <?php if (!empty($r['adviser_email'])): ?>
                                    <span class="muted"> (<?= htmlspecialchars($r['adviser_email']) ?>)</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($time) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($status) ?></span></td>
                            <td><?= htmlspecialchars($grade) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="card">
            <p class="muted">You haven’t enrolled in any subjects yet.</p>
            <p>
                <a href="enroll.php" class="btn-link">Go to Enroll</a>
            </p>
        </div>
    <?php endif; ?>

    <p style="margin-top:16px;">
        <a href="index_student.php" class="btn-link">Dashboard</a>
    </p>
</div>
</body>
</html>
