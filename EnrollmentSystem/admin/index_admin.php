<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION["role_id"]) || $_SESSION["role_id"] != 1) {
    header("Location: ../login/login.php");
    exit;
}

$name = $_SESSION["name"] ?? "Administrator";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Enrollment System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        header {
            background: #007bff;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        .dashboard {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin: 2rem auto;
            max-width: 1000px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin: 1rem;
            padding: 2rem;
            width: 250px;
            text-align: center;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card a {
            display: inline-block;
            padding: 0.75rem 1.25rem;
            margin-top: 0.5rem;
            border-radius: 6px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .card a:hover {
            background: #0056b3;
        }
        footer {
            text-align: center;
            margin-top: 2rem;
        }
        footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($name); ?></h1>
        <p>Administrator Dashboard</p>
    </header>

    <div class="dashboard">
        <div class="card">
            <a href="admin_profile.php">Profile</a>
        </div>
        <div class="card">
            <a href="admin_register.php">Register User</a>
        </div>
        <div class="card">
            <a href="subjects.php">Program Subjects</a>
        </div>
        <div class="card">
            <a href="enrollment_oversee.php">Check Enrolled Students</a>
        </div>
    </div>

</body>
</html>
