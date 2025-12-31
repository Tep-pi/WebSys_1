<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'student') {
    die("Access denied. Only students can submit thesis.");
}

$error = null;
$success = null;

$studentId = $_SESSION['user_id'];
$stmtUser = $pdo->prepare("
    SELECT u.*, d.dept_name, p.prog_name
    FROM users u
    LEFT JOIN departments d ON u.user_dept_id_fk = d.dept_id
    LEFT JOIN programs p ON u.user_prog_id_fk = p.prog_id
    WHERE u.user_id = ?
");
$stmtUser->execute([$studentId]);
$student = $stmtUser->fetch();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title    = trim($_POST['title']);
    $abstract = trim($_POST['abstract']);
    $keywords = trim($_POST['keywords']);
    $adviserName = trim($_POST['adviser']);

    $targetDir = __DIR__ . "/../assets/uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $fileName = time() . "_" . basename($_FILES["thesis_file"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["thesis_file"]["tmp_name"], $targetFile)) {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO thesis 
                (thesis_author_user_id_fk, thesis_title, thesis_abstract, thesis_keywords, thesis_status, thesis_creation, thesis_dept_id_fk, thesis_prog_id_fk) 
                VALUES (?, ?, ?, ?, 'pending', NOW(), ?, ?)");
            $stmt->execute([
                $studentId,
                $title,
                $abstract,
                $keywords,
                $student['user_dept_id_fk'],
                $student['user_prog_id_fk']
            ]);

            $thesisId = $pdo->lastInsertId();
            $stmtFile = $pdo->prepare("INSERT INTO files (thesis_id, file_name, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
            $stmtFile->execute([$thesisId, $fileName, "../assets/uploads/" . $fileName]);

            $success = "Thesis submitted successfully!";
        } catch (Exception $e) {
            $error = "Error submitting thesis: " . $e->getMessage();
        }
    } else {
        $error = "Error uploading thesis file.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Thesis</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
        }

        .container { 
            max-width:960px; 
            margin:24px auto; 
            padding:0 16px; 
        }

        .card { 
            border:1px solid #ddd; 
            border-radius:8px; 
            padding:16px; 
            background:#fff; 
            margin-bottom:20px; 
        }

        h2 { 
            margin-bottom:16px; 
        }

        h3 { 
            margin-top:0; 
        }

        .btn {
            display:inline-block;
            margin-top:10px;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            text-decoration:none;
            border-radius:4px;
            font-size:14px;
            border:none;
            cursor:pointer;
        }

        .btn:hover { 
            background:#0056b3; 
        }

        form label { 
            font-weight:bold; 
        }

        form input, form textarea { 
            margin-bottom:12px; 
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Submit Thesis</h2>

    <?php if ($error): ?>
        <p style="color:red">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green">
            <?= htmlspecialchars($success) ?>
        </p>
    <?php endif; ?>

    <p><a href="index_students.php" class="btn">Back to Student Dashboard</a></p>

    <div class="card">
        <h3>Student Information</h3>
        <p><strong>Name:</strong> 
            <?= htmlspecialchars($student['user_fname'] . " " . $student['user_lname']) ?>
        </p>
        <p><strong>Email:</strong> 
            <?= htmlspecialchars($student['user_email']) ?>
        </p>
        <p><strong>Department:</strong> 
            <?= htmlspecialchars($student['dept_name']) ?>
        </p>
        <p><strong>Program:</strong> 
            <?= htmlspecialchars($student['prog_name']) ?>
        </p>

        <h3>Profile Picture</h3>
        <?php if (!empty($student['user_pfp'])): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($student['user_pfp']) ?>" alt="Profile Picture" width="120"><br>
        <?php else: ?>
            No profile picture uploaded.<br>
        <?php endif; ?><br>

        <h3>Signature</h3>
        <?php if (!empty($student['user_signature'])): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($student['user_signature']) ?>" alt="Signature" width="120"><br>
        <?php else: ?>
            No signature uploaded.<br>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Thesis Submission Form</h3>
        <form method="post" enctype="multipart/form-data">
            <label>Thesis Title:</label><br>
            <input type="text" name="title" required><br>

            <label>Abstract:</label><br>
            <textarea name="abstract" rows="5" cols="50" required></textarea><br>

            <label>Keywords:</label><br>
            <input type="text" name="keywords" required><br>

            <label>Adviser Name:</label><br>
            <input type="text" name="adviser" required><br>

            <label>Upload Thesis File:</label><br>
            <input type="file" name="thesis_file" required><br>

            <button type="submit" class="btn">Submit Thesis</button>
        </form>
    </div>
</div>
</body>
</html>
