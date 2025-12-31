<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied. Only admins can view logs.");
}

$sqlAdviser = "
SELECT 
    l.activity_id,
    l.user_id,
    l.action,
    l.activity_time,
    u.user_fname,
    u.user_lname,
    u.user_email
FROM activity_logs l
LEFT JOIN users u ON l.user_id = u.user_id
WHERE u.user_role = 'adviser'
ORDER BY l.activity_time DESC
";
$stmtAdviser = $pdo->query($sqlAdviser);
$adviserLogs = $stmtAdviser->fetchAll(PDO::FETCH_ASSOC);

$sqlStudent = "
SELECT 
    t.thesis_id,
    t.thesis_title,
    t.thesis_creation,
    u.user_fname,
    u.user_lname,
    u.user_email,
    a.approval_status,
    a.approval_date
FROM thesis t
LEFT JOIN users u ON t.thesis_author_user_id_fk = u.user_id
LEFT JOIN approvals a ON a.approval_thesis_id_fk = t.thesis_id
WHERE u.user_role = 'student'
ORDER BY t.thesis_creation DESC, a.approval_date DESC
";

$stmtStudent = $pdo->query($sqlStudent);
$studentTheses = $stmtStudent->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Logs</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
        }

        .container { 
            max-width: 1200px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        h2 { 
            margin-bottom: 20px; 
        }

        h3 { 
            margin-top: 30px; 
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 30px; 
        }

        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
            vertical-align: top; 
        }

        th { 
            background: #f7f7f7; 
        }

        .muted { 
            color: #678; 
        }

        .status-approved { 
            color: green; 
            font-weight: bold; 
        }

        .status-rejected { 
            color: red; 
            font-weight: bold; 
        }

        .status-pending { 
            color: #678; 
            font-weight: bold; 
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
    <h2>User Activity Logs</h2>

    <p style="margin-top:20px;">
        <a href="index_admin.php" class="btn-link">Back to Admin Dashboard</a>
    </p>

    <h3>Adviser Logs</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Adviser</th>
            <th>Email</th>
            <th>Action</th>
            <th>Time</th>
        </tr>

        <?php foreach ($adviserLogs as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['activity_id']) ?></td>
                <td><?= htmlspecialchars($row['user_fname'] . " " . $row['user_lname']) ?></td>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td><?= htmlspecialchars($row['action']) ?></td>
                <td><?= htmlspecialchars(date('M d, Y H:i', strtotime($row['activity_time']))) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($adviserLogs)): ?>
            <tr><td colspan="5" class="muted">No adviser logs found.</td></tr>
        <?php endif; ?>
    </table>


    <h3>Student Thesis Submissions</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Email</th>
            <th>Title</th>
            <th>Events</th>
        </tr>
        <?php foreach ($studentTheses as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['thesis_id']) ?></td>
                <td><?= htmlspecialchars($row['user_fname'] . " " . $row['user_lname']) ?></td>
                <td><?= htmlspecialchars($row['user_email']) ?></td>
                <td><?= htmlspecialchars($row['thesis_title']) ?></td>
                <td>
                    <div><strong>Submitted</strong> — <?= htmlspecialchars(date('M d, Y H:i', strtotime($row['thesis_creation']))) ?></div>
                    <?php if (!empty($row['approval_status'])): ?>
                        <div><strong><?= ucfirst(htmlspecialchars($row['approval_status'])) ?>
                        </strong> — <?= htmlspecialchars(date('M d, Y H:i', strtotime($row['approval_date']))) ?>  
                        </div>
                    <?php endif; ?>
                </td>
            </tr>

        <?php endforeach; ?>
        <?php if (empty($studentTheses)): ?>
            <tr><td colspan="5" class="muted">No student thesis submissions found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
