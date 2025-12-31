<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'adviser') {
    die("Access denied. Only advisers can view this page.");
}

$adviserId = $_SESSION['user_id'];
$error = null;
$success = null;
$uploadsDir = "../assets/uploads/";

if (isset($_POST['upload_pfp']) && isset($_FILES['user_pfp'])) {
    $file = $_FILES['user_pfp'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $safeName = time() . "_pfp_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($file['name']));
        $target = $uploadsDir . $safeName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $stmt = $pdo->prepare("UPDATE users SET user_pfp = ? WHERE user_id = ?");
            $stmt->execute([$safeName, $adviserId]);
            $_SESSION['user_pfp'] = $safeName;
            $success = "Profile picture updated.";
        } else {
            $error = "Failed to save profile picture.";
        }
    } else {
        $error = "Upload error (profile picture).";
    }
}

if (isset($_POST['upload_signature']) && isset($_FILES['user_signature'])) {
    $file = $_FILES['user_signature'];

    if ($file['error'] === UPLOAD_ERR_OK) {
        $safeName = time() . "_sig_" . preg_replace("/[^A-Za-z0-9._-]/", "_", basename($file['name']));
        $target = $uploadsDir . $safeName;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $stmt = $pdo->prepare("UPDATE users SET user_signature = ? WHERE user_id = ?");
            $stmt->execute([$safeName, $adviserId]);
            $_SESSION['user_signature'] = $safeName;
            $success = "Signature updated.";
        } else {
            $error = "Failed to save signature.";
        }
    } else {
        $error = "Upload error (signature).";
    }
}

$stmt = $pdo->prepare("
    SELECT u.user_fname, u.user_lname, u.user_email, u.user_role,
           u.user_pfp, u.user_signature,
           d.dept_name, p.prog_name
    FROM users u
    LEFT JOIN departments d ON u.user_dept_id_fk = d.dept_id
    LEFT JOIN programs p ON u.user_prog_id_fk = p.prog_id
    WHERE u.user_id = ?
");

$stmt->execute([$adviserId]);
$u = $stmt->fetch();

$name      = $u ? ($u['user_fname'] . " " . $u['user_lname']) : ($_SESSION['user_fname'] . " " . $_SESSION['user_lname']);
$email     = $u ? $u['user_email'] : $_SESSION['user_email'];
$role      = $u ? $u['user_role'] : $_SESSION['user_role'];
$pfp       = $u ? $u['user_pfp'] : ($_SESSION['user_pfp'] ?? null);
$signature = $u ? $u['user_signature'] : ($_SESSION['user_signature'] ?? null);
$deptName  = $u['dept_name'] ?? '—';
$progName  = $u['prog_name'] ?? '—';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adviser Profile</title>
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
            padding:8px 14px;
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
    <h2>Adviser Profile</h2>

    <?php if ($error): ?>
        <p style="color:#b00020"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <div class="card">
        <div class="row">

            <div class="col">
                <p><span class="label">Role:</span> 
                    <?= htmlspecialchars(ucfirst($role)) ?>
                </p>
                <p><span class="label">Name:</span> 
                    <?= htmlspecialchars($name) ?>
                </p>
                <p><span class="label">Email:</span>   
                    <?= htmlspecialchars($email) ?>
                </p>
                <p><span class="label">Department:</span> 
                    <?= htmlspecialchars($deptName) ?>
                </p>
                <p><span class="label">Program:</span> 
                    <?= htmlspecialchars($progName) ?>
                </p>
            </div>
            
            <div class="col">
                <p class="muted">Profile picture</p>
                <?php if (!empty($pfp)): ?>
                    <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($pfp) ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No profile picture uploaded.</p>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="user_pfp" accept="image/*" required>
                    <button type="submit" name="upload_pfp">Upload profile picture</button>
                </form>

                <p class="muted" style="margin-top:16px;">Signature</p>
                <?php if (!empty($signature)): ?>
                    <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($signature) ?>" alt="Signature">
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
        <a href="index_adviser.php" class="btn-link">Back to Dashboard</a>
        <a href="../login/logout.php" class="btn-link">Logout</a>
    </p>
</div>
</body>
</html>
