<?php

session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    die("Access denied. Only admins can view this page.");
}

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_user'])) {
    $fname    = trim($_POST['first_name']);
    $lname    = trim($_POST['last_name']);
    $roleCode = $_POST['role']; 
    $program  = $_POST['program'] ?? null;
    $password = trim($_POST['password']);

    if ($roleCode === 'student') {
        $res = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role_id=3");
        $num = (int)$res->fetch_assoc()['cnt'] + 1;
        $email = "SD-UR-" . str_pad($num, 4, "0", STR_PAD_LEFT);
        $role_id = 3;
    } elseif ($roleCode === 'faculty') {
        $res = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role_id=2");
        $num = (int)$res->fetch_assoc()['cnt'] + 1;
        $email = "AD-UR-" . str_pad($num, 4, "0", STR_PAD_LEFT);
        $role_id = 2;
    } else {
        $email = "admin";
        $role_id = 1;
        $program = null;
    }

    // Handle uploads
    $pfpPath = null;
    $sigPath = null;
    $uploadsDirAbs = realpath(__DIR__ . "/../") . "/assets/uploads/admin/";
    $uploadsDirRel = "assets/uploads/admin/";
    if (!is_dir($uploadsDirAbs)) { @mkdir($uploadsDirAbs, 0777, true); }

    if (!empty($_FILES['profile_image']['name'])) {
        $safeName = time() . "_pfp_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($_FILES['profile_image']['name']));
        $target = $uploadsDirAbs . $safeName;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
            $pfpPath = $uploadsDirRel . $safeName;
        }
    }
    if (!empty($_FILES['signature_image']['name'])) {
        $safeName = time() . "_sig_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($_FILES['signature_image']['name']));
        $target = $uploadsDirAbs . $safeName;
        if (move_uploaded_file($_FILES['signature_image']['tmp_name'], $target)) {
            $sigPath = $uploadsDirRel . $safeName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO users (role_id, program_id, first_name, last_name, email, password_hash, profile_image_path, signature_image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssss", $role_id, $program, $fname, $lname, $email, $password, $pfpPath, $sigPath);

    if ($stmt->execute()) {
        $success = "User registered successfully. Email: " . $email;
    } else {
        $error = "Registration failed: " . htmlspecialchars($conn->error);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>admin_register.php</title>
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

        .row { 
            display: flex; 
            gap: 24px; 
            align-items: flex-start; }

        .col { 
            flex: 1; }

        .label { 
            font-weight: bold; 
            width: 160px; 
            display: inline-block; }

        .avatar { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            background: #f7f7f7; }

        .muted { 
            color: #678; }

        form { 
            margin-top: 12px; }

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
    </style>
</head>
<body>
<div class="container">
    <h2>Register User</h2>

    <?php if ($error): ?>
        <p style="color:#b00020"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <div class="card">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col">
                    <label class="label">First Name</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="col">
                    <label class="label">Last Name</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label class="label">Role</label>
                    <select name="role" required>
                        <option value="student">Student</option>
                        <option value="faculty">Adviser/Faculty</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="col">
                    <label class="label">Program</label>
                    <select name="program">
                        <?php
                        $progRes = $conn->query("SELECT program_id, program_name FROM programs ORDER BY program_name ASC");
                        while ($p = $progRes->fetch_assoc()) {
                            echo "<option value='{$p['program_id']}'>" . htmlspecialchars($p['program_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label class="label">Password</label>
                    <input type="password" name="password" required>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <p class="muted">Profile picture</p>
                    <input type="file" name="profile_image" accept="image/*">
                </div>
                <div class="col">
                    <p class="muted">Signature</p>
                    <input type="file" name="signature_image" accept="image/*">
                </div>
            </div>

            <button class="btn-link" type="submit" name="register_user">Register</button>
        </form>
    </div>

    <p style="margin-top:16px;">
        <a href="index_admin.php" class="btn-link">Dashboard</a>
    </p>
</div>
</body>
</html>
