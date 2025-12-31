<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied. Only admins can register users.");
}

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fname    = trim($_POST['fname']);
    $lname    = trim($_POST['lname']);
    $role     = $_POST['role'];
    $dept     = $_POST['department'];
    $prog     = $_POST['program'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_role = ?");
        $stmt->execute([$role]);
        $count = $stmt->fetchColumn();
        $nextId = str_pad($count + 1, 3, '0', STR_PAD_LEFT);


        $prefix = $role === 'student' ? 'SD-UR-' : 'AD-UR-';
        $email = $prefix . $nextId;


        $stmt = $pdo->prepare("INSERT INTO users 
            (user_fname, user_lname, user_email, user_password, user_role, user_dept_id_fk, user_prog_id_fk) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fname, $lname, $email, $password, $role, $dept, $prog]);

        $success = "User registered successfully! Assigned login email: $email";
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Register User</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
        }

        .container { 
            max-width:600px; 
            margin:40px auto; 
            padding:0 16px; 
        }

        .card {
            border:1px solid #ddd;
            border-radius:8px;
            padding:24px;
            background:#fff;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        h2 { 
            margin-top:0; 
            margin-bottom:16px; 
            text-align:center; 
        }

        label { 
            font-weight:bold; 
        }

        input, select {
            width:100%;
            padding:8px;
            margin:6px 0 14px 0;
            border:1px solid #ccc;
            border-radius:4px;
        }

        button {
            display:block;
            width:100%;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:15px;
        }

        button:hover { 
            background:#0056b3; 
        }

        .btn-link {
            display:inline-block;
            margin-top:16px;
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
    <div class="card">
        <h2>Register New User</h2>

        <?php if ($error): ?>
            <p style="color:#b00020"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>First Name:</label>
            <input type="text" name="fname" required>

            <label>Last Name:</label>
            <input type="text" name="lname" required>

            <label>Role:</label>
            <select name="role" required>
                <option value="student">Student</option>
                <option value="adviser">Adviser</option>
            </select>

            <label>Department:</label>
            <select name="department" required>
                <?php
                $stmt = $pdo->query("SELECT dept_id, dept_name FROM departments");
                while ($row = $stmt->fetch()) {
                    echo "<option value='{$row['dept_id']}'>" . htmlspecialchars($row['dept_name']) . "</option>";
                }
                ?>
            </select>

            <label>Program:</label>
            <select name="program" required>
                <?php
                $stmt = $pdo->query("SELECT prog_id, prog_name FROM programs");
                while ($row = $stmt->fetch()) {
                    echo "<option value='{$row['prog_id']}'>" . htmlspecialchars($row['prog_name']) . "</option>";
                }
                ?>
            </select>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Register User</button>
        </form>

        <p style="text-align:center;">
            <a href="index_admin.php" class="btn-link">Back to Admin Dashboard</a>
        </p>
    </div>
</div>
</body>
</html>

