<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'student') {
    die("Access denied. Only students can view this page.");
}

$fname     = $_SESSION['user_fname'];
$lname     = $_SESSION['user_lname'];
$role      = $_SESSION['user_role'];
$pfp       = $_SESSION['user_pfp'];
$signature = $_SESSION['user_signature'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
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
            margin-top:20px; 
        }

        .row { 
            display:flex; 
            gap:24px; 
            align-items:flex-start; 
        }

        .col { 
            flex:1; 
        }

        .avatar { 
            width:140px; 
            height:140px; 
            object-fit:cover; 
            border:1px solid #ccc; 
            border-radius:4px; 
            background:#f7f7f7; 
        }

        .muted { 
            color:#678; 
        }

        .nav-buttons { 
            margin-top:20px; 
        }

        .nav-buttons a {
            display:inline-block;
            margin:6px 8px 0 0;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            text-decoration:none;
            border-radius:4px;
            font-size:14px;
        }

        .nav-buttons a:hover { 
            background:#0056b3; 
        }

    </style>
</head>

<body>
<div class="container">
    <h2>Welcome, <?= htmlspecialchars($fname . " " . $lname) ?></h2>

    <p>Role: <?= htmlspecialchars(ucfirst($role)) ?></p>

    <h3>Profile</h3>

    <div class="card">
        <div class="row">

            <div class="col">
                <p class="muted">Profile picture</p>

                <?php if (!empty($pfp)): ?>
                    <img class="avatar" src="../assets/uploads/<?= htmlspecialchars($pfp) ?>" alt="Profile Picture">

                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No profile picture uploaded.</p>
                <?php endif; ?>
            </div>

            <div class="col">
                <p class="muted">Signature</p>

                <?php if (!empty($signature)): ?>
                    <img class="avatar" src="../assets/uploads/<?= htmlspecialchars($signature) ?>" alt="Signature">
                    
                <?php else: ?>
                    <div class="avatar"></div>
                    <p class="muted">No signature uploaded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <h3>Navigation</h3>
    <div class="nav-buttons">
        <a href="student_profile.php">My Profile</a>
        <a href="student_submit.php">Submit Thesis</a>
        <a href="student_view_status.php">View Thesis Status</a>
    </div>
</div>
</body>
</html>
