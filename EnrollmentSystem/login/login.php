<?php

session_start();
require_once __DIR__ . "/../db/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $stmt = $conn->prepare("SELECT user_id, role_id, first_name, last_name, password_hash FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if ($password === $user["password_hash"]) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["role_id"] = $user["role_id"];
            $_SESSION["name"] = $user["first_name"] . " " . $user["last_name"];

            switch ($user["role_id"]) {
                case 1:
                    header("Location: ../admin/index_admin.php");
                    break;
                case 2:
                    header("Location: ../faculty/index_faculty.php");
                    break;
                case 3:
                    header("Location: ../student/index_student.php");
                    break;
                default:
                    header("Location: login.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enrollment System - Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-card {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 350px;
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .login-card input[type="text"],
        .login-card input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .login-card button {
            width: 100%;
            padding: 0.75rem;
            margin-top: 1rem;
            border: none;
            border-radius: 6px;
            background: #007bff;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
        }
        .login-card button:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
