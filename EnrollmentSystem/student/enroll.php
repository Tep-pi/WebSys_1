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


$stmt = $conn->prepare("SELECT program_id, first_name, last_name, email FROM users WHERE user_id = ?");
if (!$stmt) {
    die("Prepare failed (users): " . $conn->error);
}
$stmt->bind_param("i", $studentId);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$me || !$me['program_id']) {
    die("Your profile is incomplete. Please set your program in your profile first.");
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_subjects'])) {
    $selected = array_map('intval', $_POST['selected_subjects']);

    if (empty($selected)) {
        $error = "Please select at least one subject to enroll.";
    } else {
        $insert = $conn->prepare("INSERT INTO enrolled (student_id, subject_id, status) VALUES (?, ?, 'enrolled')");
        if (!$insert) {
            die("Prepare failed (insert enrolled): " . $conn->error);
        }
        $check  = $conn->prepare("SELECT 1 FROM enrolled WHERE student_id = ? AND subject_id = ? LIMIT 1");
        if (!$check) {
            die("Prepare failed (check enrolled): " . $conn->error);
        }

        foreach ($selected as $sid) {
            $check->bind_param("ii", $studentId, $sid);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();

            if (!$exists) {
                $insert->bind_param("ii", $studentId, $sid);
                $insert->execute();
            }
        }
        $insert->close();
        $check->close();

        header("Location: enrolled.php");
        exit;
    }
}

$sql = "
    SELECT s.subject_id,
           s.subject_name,
           s.year_level,
           s.semester,
           s.time_slot,
           s.prerequisite_subject_id,
           p.program_name,
           ps.subject_name AS prerequisite_name
    FROM subjects s
    LEFT JOIN programs p ON s.program_id = p.program_id
    LEFT JOIN subjects ps ON s.prerequisite_subject_id = ps.subject_id
    WHERE s.program_id = ?
    ORDER BY s.year_level, s.semester, s.subject_name
";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed (subjects): " . $conn->error);
}
$stmt->bind_param("i", $me['program_id']);
$stmt->execute();
$res = $stmt->get_result();
$subjects = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();


$enrolledMap = [];
$enrolledStmt = $conn->prepare("SELECT subject_id, grade, status FROM enrolled WHERE student_id = ?");
if (!$enrolledStmt) {
    die("Prepare failed (enrolled list): " . $conn->error);
}
$enrolledStmt->bind_param("i", $studentId);
$enrolledStmt->execute();
$enrolledRes = $enrolledStmt->get_result();
while ($row = $enrolledRes->fetch_assoc()) {
    $enrolledMap[(int)$row['subject_id']] = ['grade' => $row['grade'], 'status' => $row['status']];
}
$enrolledStmt->close();


function prereq_ok($prereqId, $enrolledMap) {
    if (!$prereqId) return true;
    if (!isset($enrolledMap[$prereqId])) return false;

    $g = $enrolledMap[$prereqId]['grade'] ?? null;
    if ($g === null || $g === '') return false;

    $val = trim($g);


    if (preg_match('/^\s*(\d+)\s*\/\s*(\d+)\s*$/', $val, $m)) {
        $num = (int)$m[1];
        $den = (int)$m[2];
        if ($den == 5) {
            return $num <= 3;
        }
        return ($den > 0) ? (($num / $den) * 100 >= 75) : false;
    }

    if (is_numeric($val)) {
        $num = (float)$val;
        if ($num >= 1 && $num <= 5) {
            return $num <= 3;
        }
        return $num >= 75;
    }

    return false;
}

$groups = [];
foreach ($subjects as $s) {
    $key = "Y" . (int)$s['year_level'] . "-S" . (int)$s['semester'];
    if (!isset($groups[$key])) $groups[$key] = [];
    $groups[$key][] = $s;
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
    <title>Enroll in Subjects</title>
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

        .status-ok { 
            color:#2e7d32; 
            font-weight:bold; }

        .status-no { 
            color:#b00020; 
            font-weight:bold; }

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
            margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Enroll in Subjects</h2>
    <p class="muted">Program ID: <?= htmlspecialchars($me['program_id']) ?> • Signed in as <?= htmlspecialchars($me['first_name']." ".$me['last_name']) ?> (<?= htmlspecialchars($me['email']) ?>)</p>

    <?php if ($error): ?><p class="status-no"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="post">
        <?php if (!empty($groups)): ?>
            <?php foreach ($groups as $key => $rows): 
                [$yLabel, $sLabel] = explode("-", $key);
                $year = (int)substr($yLabel, 1);
                $sem  = (int)substr($sLabel, 1);
            ?>
                <div class="card">
                    <div class="table-title"><?= ordinal($year) ?> Year — <?= $sem === 1 ? "1st" : "2nd" ?> Semester</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Subject</th>
                                <th>Prerequisite</th>
                                <th>Eligibility</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row): 
                            $sid = (int)$row['subject_id'];
                            $already = isset($enrolledMap[$sid]);
                            $eligible = prereq_ok((int)$row['prerequisite_subject_id'], $enrolledMap);
                            $prereqText = $row['prerequisite_name'] ? $row['prerequisite_name'] : "None";
                            $timeText = $row['time_slot'] ? $row['time_slot'] : "—";
                        ?>
                            <tr>
                                <td>
                                    <?php if ($already): ?>
                                        <input type="checkbox" disabled>
                                    <?php elseif ($eligible): ?>
                                        <input type="checkbox" name="selected_subjects[]" value="<?= $sid ?>">
                                    <?php else: ?>
                                        <input type="checkbox" disabled title="Prerequisite not satisfied">
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                <td><?= htmlspecialchars($prereqText) ?></td>
                                <td>
                                    <?php if ($eligible): ?>
                                        <span class="status-ok">Eligible</span>
                                    <?php else: ?>
                                        <span class="status-no">Not eligible</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($timeText) ?></td>
                                <td>
                                    <?php if ($already): ?>
                                        <span class="muted">Already enrolled</span>
                                    <?php else: ?>
                                        <span class="muted">Not yet enrolled</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="muted">No subjects available for your program.</p>
        <?php endif; ?>

        <div class="actions">
            <button type="submit" class="btn-link">Submit</button>
            <a href="index_student.php" class="btn-link">Dashboard</a>
        </div>
    </form>

    <p class="muted" style="margin-top:8px;">
        Note: Subjects with no prerequisite are freely available.
        If a subject has a prerequisite, you must have a passing grade in the prerequisite subject.
    </p>
</div>
</body>
</html>
