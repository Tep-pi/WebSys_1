<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied. Only admins can manage users.");
}

$error = null;
$success = null;


if (isset($_POST['new_department'])) {
    $newDept = trim($_POST['new_department']);
    if (!empty($newDept)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO departments (dept_name) VALUES (?)");
            $stmt->execute([$newDept]);
            $success = "Department added successfully!";
        } catch (Exception $e) {
            $error = "Error adding department: " . $e->getMessage();
        }
    }
}


if (isset($_POST['new_program']) && isset($_POST['dept_id'])) {
    $newProg = trim($_POST['new_program']);
    $deptId  = $_POST['dept_id'];
    if (!empty($newProg)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO programs (prog_name, dept_id) VALUES (?, ?)");
            $stmt->execute([$newProg, $deptId]);
            $success = "Program added successfully!";
        } catch (Exception $e) {
            $error = "Error adding program: " . $e->getMessage();
        }
    }
}


function fetchUsersByRole($pdo, $role) {
    $stmt = $pdo->prepare(
        "SELECT u.user_id, u.user_fname, u.user_lname, u.user_email, u.user_role, d.dept_name, p.prog_name
        FROM users u
        LEFT JOIN departments d ON u.user_dept_id_fk = d.dept_id
        LEFT JOIN programs p ON u.user_prog_id_fk = p.prog_id
        WHERE u.user_role = ?
        ORDER BY u.user_id");
    $stmt->execute([$role]);
    return $stmt->fetchAll();
}

$students = fetchUsersByRole($pdo, 'student');
$advisers = fetchUsersByRole($pdo, 'adviser');
$admins   = fetchUsersByRole($pdo, 'admin');


$deptStmt = $pdo->query("SELECT * FROM departments");
$departments = $deptStmt->fetchAll();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Manage Users</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
        }

        .container { 
            max-width: 1000px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        h2 { 
            margin-bottom: 16px; 
        }

        .card { 
            border:1px solid #ddd; 
            border-radius:8px; 
            padding:20px; 
            background:#fff; 
            margin-bottom:24px; 
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        .card h3 { 
            margin-top:0; 
            margin-bottom:12px; 
        }

        table { 
            width:100%; 
            border-collapse:collapse; 
            margin-top:12px; 
        }

        th, td { 
            border:1px solid #ddd; 
            padding:8px; 
            text-align:left; 
        }

        th { 
            background:#f7f7f7; 
        }

        form { 
            margin-top:12px; 
        }

        input, select { 
            padding:8px; 
            border:1px solid #ccc; 
            border-radius:4px; 
            width:100%; 
            max-width:300px; 
        }

        button {
            display:inline-block;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:14px;
        }

        button:hover { 
            background:#0056b3; 
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

        .grid { 
            display:grid; 
            grid-template-columns: 1fr 1fr; 
            gap:24px; 
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Manage Users</h2>

    <p style="margin-top:20px;">
        <a href="index_admin.php" class="btn-link">Back to Admin Dashboard</a>
    </p>

    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <div class="card">
        <h3>Admins</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php foreach ($admins as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['user_fname'] . " " . $user['user_lname']) ?></td>
                    <td><?= htmlspecialchars($user['user_email']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>


    <div class="card">
        <h3>Advisers</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Program</th>
            </tr>
            <?php foreach ($advisers as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['user_fname'] . " " . $user['user_lname']) ?></td>
                    <td><?= htmlspecialchars($user['user_email']) ?></td>
                    <td><?= htmlspecialchars($user['dept_name']) ?></td>
                    <td><?= htmlspecialchars($user['prog_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>


    <div class="card">
        <h3>Students</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Program</th>
            </tr>
            <?php foreach ($students as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['user_fname'] . " " . $user['user_lname']) ?></td>
                    <td><?= htmlspecialchars($user['user_email']) ?></td>
                    <td><?= htmlspecialchars($user['dept_name']) ?></td>
                    <td><?= htmlspecialchars($user['prog_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>


    <div class="card">
        <h3>Add Department & Program</h3>
        <div class="grid">
            <div>
                <form method="post">
                    <label>Department Name:</label><br>
                    <input type="text" name="new_department" required><br><br>
                    <button type="submit">Add Department</button>
                </form>
            </div>
            <div>
                <form method="post">
                    <label>Program Name:</label><br>
                    <input type="text" name="new_program" required><br><br>

                    <label>Assign to Department:</label><br>
                    <select name="dept_id" required>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['dept_id'] ?>"><?= htmlspecialchars($dept['dept_name']) ?></option>
                        <?php endforeach; ?>
                    </select><br><br>

                    <button type="submit">Add Program</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
