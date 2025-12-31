<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied. Only admins can view this page.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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

        h2 { 
            margin-bottom:8px; 
        }

        .grid { 
            display:grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap:24px; 
            margin-top:20px; 
        }

        .card { 
            border:1px solid #ddd; 
            border-radius:8px; 
            padding:32px; 
            background:#fff; 
            text-align:center; 
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        .card a {
            display:block;
            font-size:18px;
            font-weight:bold;
            color:#007bff;
            text-decoration:none;
        }

        .card a:hover { 
            color:#0056b3; 
        }

        .btn-link {
            display:inline-block;
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
    <h2>Welcome, <?= htmlspecialchars($_SESSION['user_fname'] . " " . $_SESSION['user_lname']) ?></h2>
    
    <p>Role: <?= htmlspecialchars($_SESSION['user_role']) ?></p>

    <h3>Admin Dashboard</h3>
    <div class="grid">
        <div class="card">
            <a href="admin_register.php">Register New User</a>
        </div>
        <div class="card">
            <a href="admin_users.php">Manage Users</a>
        </div>

        <div class="card">
            <a href="admin_logs.php">Activity Logs</a>
        </div>
        <div class="card">
            <a href="admin_archives.php">View Archives</a>
        </div>
    </div>

    <p style="margin-top:24px; text-align:center;">
        <a href="../login/logout.php" class="btn-link">Logout</a>
    </p>
</div>
</body>
</html>
