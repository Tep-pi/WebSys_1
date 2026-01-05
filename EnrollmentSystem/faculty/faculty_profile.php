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

$stmt = $conn->prepare("
    SELECT first_name, last_name, email, profile_image_path, signature_image_path, program_id
    FROM users
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
$stmt->close();

$error = null;
$success = null;

$uploadsDirAbs = realpath(__DIR__ . "/../") . "/assets/uploads/faculty/";
$uploadsDirRel = "assets/uploads/faculty/";
if (!is_dir($uploadsDirAbs)) {
    @mkdir($uploadsDirAbs, 0777, true);
}

function saveImage($fileKey, $prefix, $uploadsDirAbs, $uploadsDirRel) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return [false, "Upload error."];
    }
    $original = basename($_FILES[$fileKey]['name']);
    $safeOriginal = preg_replace("/[^A-Za-z0-9._-]/", "_", $original);
    $ext = pathinfo($safeOriginal, PATHINFO_EXTENSION);
    $name = time() . "_" . $prefix . "_" . bin2hex(random_bytes(4)) . ($ext ? "." . strtolower($ext) : "");
    $destAbs = $uploadsDirAbs . $name;
    $destRel = $uploadsDirRel . $name;
    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $destAbs)) {
        return [true, $destRel];
    }
    return [false, "Failed to save file."];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['upload_pfp'])) {
        [$ok, $result] = saveImage('user_pfp', 'pfp', $uploadsDirAbs, $uploadsDirRel);
        if ($ok) {
            $stmt = $conn->prepare("UPDATE users SET profile_image_path = ? WHERE user_id = ?");
            $stmt->bind_param("si", $result, $userId);
            if ($stmt->execute()) {
                $success = "Profile picture updated.";
                $u['profile_image_path'] = $result;
            } else {
                $error = "Failed to update profile picture.";
            }
            $stmt->close();
        } else {
            $error = $result;
        }
    }

    if (isset($_POST['upload_signature'])) {
        [$ok, $result] = saveImage('user_signature', 'sig', $uploadsDirAbs, $uploadsDirRel);
        if ($ok) {
            $stmt = $conn->prepare("UPDATE users SET signature_image_path = ? WHERE user_id = ?");
            $stmt->bind_param("si", $result, $userId);
            if ($stmt->execute()) {
                $success = "Signature updated.";
                $u['signature_image_path'] = $result;
            } else {
                $error = "Failed to update signature.";
            }
            $stmt->close();
        } else {
            $error = $result;
        }
    }
}

$name = ($u['first_name'] ?? 'Faculty') . " " . ($u['last_name'] ?? 'Member');
$email = $u['email'] ?? 'faculty';
$pfpRel = $u['profile_image_path'] ?? null;
$sigRel = $u['signature_image_path'] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Profile</title>
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
            background: #fff; }

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
            font-size:14px;
        }
        .btn-link:hover { 
            background:#0056b3; }
    </style>
</head>
<body>
<div class="container">
    <h2>Faculty Profile</h2>

    <?php if ($error): ?>
        <p style="color:#b00020"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <div class="card">
        <div class="row">
            <div class="col">
                <p><span class="label">Role:</span> Faculty</p>
                <p><span class="label">Name:</span> <?= htmlspecialchars($name) ?></p>
                <p><span class="label">Email:</span> <?= htmlspecialchars($email) ?></p>
            </div>

            <div class="col">
                <p class="muted">Profile picture</p>
                <?php if (!empty($pfpRel)): ?>
                    <img class="avatar" src="../<?= htmlspecialchars($pfpRel) ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No profile picture uploaded.</p>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="user_pfp" accept="image/*" required>
                    <button type="submit" name="upload_pfp">Upload profile picture</button>
                </form>

                <p class="muted" style="margin-top:16px;">Signature</p>
                <?php if (!empty($sigRel)): ?>
                    <img class="avatar" src="../<?= htmlspecialchars($sigRel) ?>" alt="Signature">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No signature uploaded.</p>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="user_signature" accept="image/*" required>
                    <button type="submit" name="upload_signature">Upload signature</button>
                </form>
            </div>
        </div>
    </div>

    <p style="margin-top:16px;">
        <a href="index_faculty.php" class="btn-link">Dashboard</a>
        <a href="../login/logout.php" class="btn-link">Logout</a>
    </p>
</div>
</body>
</html>
