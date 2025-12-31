<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'student') {
    die("Access denied. Only students can view this page.");
}

$error = null;
$success = null;

if (isset($_POST['upload_profile'])) {
    $targetDir = "../assets/uploads/";
    $fileName = basename($_FILES["profile_pic"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE users SET user_pfp = ? WHERE user_id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        $_SESSION['user_pfp'] = $fileName;
        $success = "Profile picture updated successfully!";
    } else {
        $error = "Error uploading profile picture.";
    }
}

if (isset($_POST['upload_signature'])) {
    $targetDir = "../assets/uploads/";
    $fileName = basename($_FILES["signature_pic"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["signature_pic"]["tmp_name"], $targetFile)) {
        $stmt = $pdo->prepare("UPDATE users SET user_signature = ? WHERE user_id = ?");
        $stmt->execute([$fileName, $_SESSION['user_id']]);
        $_SESSION['user_signature'] = $fileName;
        $success = "Signature updated successfully!";
    } else {
        $error = "Error uploading signature.";
    }
}

$stmt = $pdo->prepare(
    "SELECT u.user_fname, u.user_lname, u.user_email, u.user_role, d.dept_name, p.prog_name, u.user_pfp, u.user_signature
    FROM users u
    LEFT JOIN departments d ON u.user_dept_id_fk = d.dept_id
    LEFT JOIN programs p ON u.user_prog_id_fk = p.prog_id
    WHERE u.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
        }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        .card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 16px; 
            background: #fff; 
        }

        .row { 
            display: flex; 
            gap: 24px; 
            align-items: flex-start; 
        }

        .col { 
            flex: 1; 
        }

        .label { 
            font-weight: bold; 
            width: 160px; 
            display: inline-block; 
        }

        .avatar { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            background: #f7f7f7; 
        }

        .muted { 
            color: #678; 
        }

        form { 
            margin-top: 12px; 
        }

        .btn-link {
            display:inline-block;
            margin:6px 8px 0 0;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            text-decoration:none;
            border-radius:4px;
            font-size:14px;
        }

        .btn-link:hover { 
            background:#0056b3; 
        }

    </style>
</head>
<body>
<div class="container">
    <h2>Student Profile</h2>

    <?php if ($error): ?>
        <p style="color:#b00020">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:#2e7d32">
            <?= htmlspecialchars($success) ?>
        </p>
    <?php endif; ?>

    <div class="card">
        <div class="row">

            <div class="col">
                <p><span class="label">Name:</span>
                    <?= htmlspecialchars($student['user_fname'] . " " . $student['user_lname']) ?>
                </p>
                <p><span class="label">Email:</span> 
                    <?= htmlspecialchars($student['user_email']) ?>
                </p>
                <p><span class="label">Role:</span> 
                    <?= htmlspecialchars($student['user_role']) ?>
                </p>
                <p><span class="label">Department:</span> 
                    <?= htmlspecialchars($student['dept_name']) ?>
                </p>
                <p><span class="label">Program:</span> 
                    <?= htmlspecialchars($student['prog_name']) ?>
                </p>
            </div>

            <div class="col">
                <p class="muted">Profile picture</p>
                <?php if (!empty($student['user_pfp'])): ?>
                    <img class="avatar" src="../assets/uploads/<?= htmlspecialchars($student['user_pfp']) ?>" alt="Profile Picture">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No profile picture uploaded.</p>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="profile_pic" accept="image/*" required>
                    <button type="submit" name="upload_profile">Upload Profile Picture</button>
                </form>

                <p class="muted" style="margin-top:16px;">Signature</p>
                <?php if (!empty($student['user_signature'])): ?>
                    <img class="avatar" src="../assets/uploads/<?= htmlspecialchars($student['user_signature']) ?>" alt="Signature">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No signature uploaded.</p>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="signature_pic" accept="image/*" required>
                    <button type="submit" name="upload_signature">Upload Signature</button>
                </form>
            </div>
        </div>
    </div>

    <p style="margin-top:16px;">
        <a class="btn-link" href="index_students.php">Back to Student Dashboard</a>
        <a class="btn-link" href="../login/logout.php">Logout</a>
    </p>
</div>
</body>
</html>
