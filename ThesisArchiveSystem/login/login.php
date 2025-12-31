<?php
session_start();
require_once __DIR__ . "/../db/db.php";

$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['user_password']) 
        {
            $role = $user['user_role'];
            $valid = false;

            if ($role === 'admin' && $email === 'admin') {
                $valid = true;
            } elseif ($role === 'student' && preg_match('/^SD-UR-\d+$/', $email)) {
                $valid = true;
            } elseif ($role === 'adviser' && preg_match('/^AD-UR-\d+$/', $email)) {
                $valid = true;
            }

            if ($valid) {
                $_SESSION['user_id']    = $user['user_id'];
                $_SESSION['user_fname'] = $user['user_fname'];
                $_SESSION['user_lname'] = $user['user_lname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_role']  = $user['user_role'];
                $_SESSION['user_pfp']   = $user['user_pfp'];
                $_SESSION['user_signature'] = $user['user_signature'];

                if ($role === 'student') {
                    header("Location: ../student/index_students.php");
                    exit;
                } elseif ($role === 'adviser') {
                    header("Location: ../adviser/index_adviser.php");
                    exit;
                } elseif ($role === 'admin') {
                    header("Location: ../admin/index_admin.php");
                    exit;
                }
            } else {
                $error = "Invalid email format for role.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
        }
        .container { 
            max-width:400px; 
            margin:60px auto; 
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
        input[type="text"], input[type="password"] { 
            width:100%; 
            padding:8px; 
            margin:8px 0 16px 0; 
            border:1px solid #ccc; 
            border-radius:4px; 
        }
        .btn {
            display:block;
            width:100%;
            padding:10px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            font-size:16px;
            cursor:pointer;
            text-align:center;
        }
        .btn:hover { 
            background:#0056b3; 
        }
        .error { 
            color:#b00020; 
            margin-bottom:12px; 
            text-align:center; 
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>User Login</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post">
            <label>Email:</label><br>
            <input type="text" name="email" required><br>

            <label>Password:</label><br>
            <input type="password" name="password" required><br>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</div>
</body>
</html>

