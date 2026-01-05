<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Access denied. Only admins can view this page.");
}

$subject_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$error = null;
$success = null;

if (isset($_POST['delete_subject']) && $subject_id) {
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $subject_id);
    if ($stmt->execute()) {
        header("Location: subjects.php");
        exit;
    } else {
        $error = "Delete failed: " . $conn->error;
    }
    $stmt->close();
}

if (isset($_POST['save_subject'])) {
    $name       = trim($_POST['subject_name']);
    $program_id = $_POST['program_id'];
    $adviser_id = $_POST['adviser_id'] ?: null;
    $year_level = $_POST['year_level'];
    $semester   = $_POST['semester'];
    $prereq_id  = $_POST['prerequisite_id'] ?: null;
    $time_slot  = $_POST['time_slot'];
    $term_id    = isset($_POST['term_id']) ? $_POST['term_id'] : null;

    if ($subject_id) {
        $stmt = $conn->prepare("UPDATE subjects 
            SET subject_name=?, program_id=?, adviser_id=?, year_level=?, semester=?, prerequisite_subject_id=?, time_slot=?, term_id=? 
            WHERE subject_id=?");
        $stmt->bind_param("siiiiisii", $name, $program_id, $adviser_id, $year_level, $semester, $prereq_id, $time_slot, $term_id, $subject_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects 
            (subject_name, program_id, adviser_id, year_level, semester, prerequisite_subject_id, time_slot, term_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("siiiiisi", $name, $program_id, $adviser_id, $year_level, $semester, $prereq_id, $time_slot, $term_id);
    }

    if ($stmt->execute()) {
        $success = "Subject saved successfully.";
        if (!$subject_id) {
            $subject_id = $conn->insert_id;
        }
    } else {
        $error = "Save failed: " . $conn->error;
    }
    $stmt->close();
}

$subject = null;
if ($subject_id) {
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_id=?");
    $stmt->bind_param("i", $subject_id);
    $stmt->execute();
    $subject = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$programs = $conn->query("SELECT program_id, program_name FROM programs ORDER BY program_name ASC")->fetch_all(MYSQLI_ASSOC);
$faculty = $conn->query("SELECT user_id, CONCAT(first_name,' ',last_name) AS name FROM users WHERE role_id=2 ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
$prereqs = $conn->query("SELECT subject_id, subject_name, year_level, semester FROM subjects ORDER BY year_level, semester, subject_name ASC")->fetch_all(MYSQLI_ASSOC);
$termsResult = $conn->query("SELECT term_id, term_label FROM academic_terms ORDER BY term_id ASC");
$terms = $termsResult ? $termsResult->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Subject</title>
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

        label { 
            display:block; 
            margin-top:8px; 
            font-weight:bold; }

        input, select { 
            width:100%; 
            padding:6px; 
            margin-top:4px; }

        .muted { 
            color:#678; }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $subject_id ? "Edit Subject" : "Add New Subject" ?></h2>

    <?php if ($error): ?><p style="color:#b00020"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <div class="card">
        <form method="post">
            <label>Subject Name</label>
            <input type="text" name="subject_name" value="<?= htmlspecialchars($subject['subject_name'] ?? '') ?>" required>

            <label>Program</label>
            <select name="program_id" required>
                <?php foreach ($programs as $p): ?>
                    <option value="<?= $p['program_id'] ?>" <?= ($subject && $subject['program_id']==$p['program_id'])?'selected':'' ?>>
                        <?= htmlspecialchars($p['program_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Adviser (Faculty)</label>
            <select name="adviser_id">
                <option value="">— None —</option>
                <?php foreach ($faculty as $f): ?>
                    <option value="<?= $f['user_id'] ?>" <?= ($subject && $subject['adviser_id']==$f['user_id'])?'selected':'' ?>>
                        <?= htmlspecialchars($f['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Year Level</label>
            <select name="year_level" required>
                <?php for ($y=1;$y<=4;$y++): ?>
                    <option value="<?= $y ?>" <?= ($subject && $subject['year_level']==$y)?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>

            <label>Semester</label>
            <select name="semester" required>
                <option value="1" <?= ($subject && $subject['semester']==1)?'selected':'' ?>>1st Semester</option>
                <option value="2" <?= ($subject && $subject['semester']==2)?'selected':'' ?>>2nd Semester</option>
            </select>

            <label>Prerequisite Subject</label>
            <select name="prerequisite_id">
                <option value="">— None —</option>
                <?php foreach ($prereqs as $pr): ?>
                    <option value="<?= $pr['subject_id'] ?>" <?= ($subject && $subject['prerequisite_subject_id']==$pr['subject_id'])?'selected':'' ?>>
                        <?= htmlspecialchars($pr['subject_name']) ?> (Year <?= $pr['year_level'] ?>, Sem <?= $pr['semester'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Timeline / Time Slot</label>
            <input type="text" name="time_slot" value="<?= htmlspecialchars($subject['time_slot'] ?? '') ?>">

            <label>Academic Term</label>
            <select name="term_id" required>
                <?php foreach ($terms as $t): ?>
                    <option value="<?= $t['term_id'] ?>" <?= ($subject && $subject['term_id']==$t['term_id'])?'selected':'' ?>>
                        <?= htmlspecialchars($t['term_label']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn-link" type="submit" name="save_subject">Save</button>
            <?php if ($subject_id): ?>
                <button class="btn-link" type="submit" name="delete_subject" onclick="return confirm('Delete this subject?')">Delete</button>
            <?php endif; ?>
        </form>
    </div>

    <p style="margin-top:16px;">
        <a href="subjects.php" class="btn-link">Back to Subjects</a>
        <a href="index_admin.php" class="btn-link">Dashboard</a>
    </p>
</div>
</body>
</html>
