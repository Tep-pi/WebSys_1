<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied. Only admins can view archives.");
}

$uploadsDir = "../assets/uploads/";

$statusFilter = $_GET['status'] ?? '';
$deptFilter   = $_GET['dept'] ?? '';
$progFilter   = $_GET['prog'] ?? '';
$keyword      = $_GET['keyword'] ?? '';

$latestFilesJoin = "
LEFT JOIN (
    SELECT f1.*
    FROM files f1
    INNER JOIN (
        SELECT thesis_id, MAX(uploaded_at) AS latest_uploaded
        FROM files
        GROUP BY thesis_id
    ) f2 ON f1.thesis_id = f2.thesis_id AND f1.uploaded_at = f2.latest_uploaded
) lf ON lf.thesis_id = t.thesis_id
";

$sql = "
SELECT
    t.thesis_id,
    t.thesis_title,
    t.thesis_abstract,
    t.thesis_keywords,
    t.thesis_creation,
    t.thesis_status,
    author.user_fname AS student_fname,
    author.user_lname AS student_lname,
    adv.user_fname AS adviser_fname,
    adv.user_lname AS adviser_lname,
    d.dept_name,
    p.prog_name,
    lf.file_name,
    lf.file_path
FROM thesis t
LEFT JOIN users author ON t.thesis_author_user_id_fk = author.user_id
LEFT JOIN users adv ON t.thesis_adviser_user_id_fk = adv.user_id
LEFT JOIN departments d ON t.thesis_dept_id_fk = d.dept_id
LEFT JOIN programs p ON t.thesis_prog_id_fk = p.prog_id
$latestFilesJoin
WHERE 1=1
";

$params = [];
if ($statusFilter !== '') {
    $sql .= " AND t.thesis_status = ? ";
    $params[] = $statusFilter;
}
if ($deptFilter !== '') {
    $sql .= " AND d.dept_name LIKE ? ";
    $params[] = "%" . $deptFilter . "%";
}
if ($progFilter !== '') {
    $sql .= " AND p.prog_name LIKE ? ";
    $params[] = "%" . $progFilter . "%";
}
if ($keyword !== '') {
    $sql .= " AND (t.thesis_title LIKE ? OR t.thesis_keywords LIKE ?) ";
    $params[] = "%" . $keyword . "%";
    $params[] = "%" . $keyword . "%";
}

$sql .= " ORDER BY t.thesis_creation DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$theses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Admin Archives</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; }

        .container { 
            max-width: 1200px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        h2 { 
            margin-bottom: 20px; 
        }

        .card {
            border:1px solid #ddd;
            border-radius:8px;
            padding:16px;
            background:#fff;
            margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,0.1);
        }

        form.filter label { 
            margin-right: 8px; 
            font-weight:bold; 
        }

        form.filter input, form.filter select {
            margin-right: 16px;
            padding:8px;
            border:1px solid #ccc;
            border-radius:4px;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
        }

        th { 
            background: #f7f7f7; 
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

        .muted { 
            color: #678; 
        }

        button, .btn-link {
            display:inline-block;
            padding:10px 16px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:14px;
            text-decoration:none;
        }

        button:hover, .btn-link:hover { 
            background:#0056b3; 
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Thesis Archives (View Only)</h2>

    <p style="margin-top:20px;">
        <a href="index_admin.php" class="btn-link">Back to Admin Dashboard</a>
    </p>

    <div class="card">
        <form method="get" class="filter">
            <label>Status:</label>
            <select name="status">
                <option value="">All</option>
                <option value="pending" <?= $statusFilter==='pending'?'selected':'' ?>>Pending</option>
                <option value="approved" <?= $statusFilter==='approved'?'selected':'' ?>>Approved</option>
                <option value="rejected" <?= $statusFilter==='rejected'?'selected':'' ?>>Rejected</option>
            </select>
            
            <label>Keyword:</label>
            <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>"><br><br>

            <label>Department:</label>
            <input type="text" name="dept" value="<?= htmlspecialchars($deptFilter) ?>">

            <label>Program:</label>
            <input type="text" name="prog" value="<?= htmlspecialchars($progFilter) ?>"><br><br>

            <button type="submit">Filter</button>
            <a href="admin_archives.php" class="btn-link">Reset</a>
        </form>
    </div>

    <table>
        <tr>
            <th>Title</th>
            <th>Author</th>
            <th>Adviser</th>
            <th>Department</th>
            <th>Program</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>File</th>
        </tr>
        <?php foreach ($theses as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['thesis_title']) ?></td>
                <td><?= htmlspecialchars($row['student_fname'] . " " . $row['student_lname']) ?></td>
                <td><?= htmlspecialchars(trim(($row['adviser_fname'] ?? '') . " " . ($row['adviser_lname'] ?? ''))) ?: '—' ?></td>
                <td><?= htmlspecialchars($row['dept_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars($row['prog_name'] ?? '—') ?></td>
                <td class="status-<?= htmlspecialchars(strtolower($row['thesis_status'])) ?>">
                    <?= ucfirst(htmlspecialchars($row['thesis_status'])) ?>
                </td>
                <td><?= htmlspecialchars(date('M d, Y', strtotime($row['thesis_creation']))) ?></td>
                <td>
                    <?php if (!empty($row['file_path'])): ?>
                        <a href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
                        <span class="muted">(<?= htmlspecialchars($row['file_name']) ?>)</span>
                    <?php else: ?>
                        <span class="muted">No file</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
