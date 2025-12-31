<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'adviser') {
    die("Access denied. Only advisers can view this page.");
}

$title    = isset($_GET['title']) ? trim($_GET['title']) : '';
$author   = isset($_GET['author']) ? trim($_GET['author']) : '';
$year     = isset($_GET['year']) ? trim($_GET['year']) : '';
$adviser  = isset($_GET['adviser']) ? trim($_GET['adviser']) : '';
$keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';

$where = [];
$params = [];

//search filters dito
if ($title !== '') {
    $where[] = "t.thesis_title LIKE ?";
    $params[] = "%$title%";
}

if ($author !== '') {
    $where[] = "(author.user_fname LIKE ? OR author.user_lname LIKE ? OR CONCAT(author.user_fname, ' ', author.user_lname) LIKE ?)";
    $params[] = "%$author%";
    $params[] = "%$author%";
    $params[] = "%$author%";
}

if ($year !== '') {
    $where[] = "YEAR(t.thesis_creation) = ?";
    $params[] = $year;
}

if ($adviser !== '') {
    $where[] = "(adv.user_fname LIKE ? OR adv.user_lname LIKE ? OR CONCAT(adv.user_fname, ' ', adv.user_lname) LIKE ?)";
    $params[] = "%$adviser%";
    $params[] = "%$adviser%";
    $params[] = "%$adviser%";
}

if ($keywords !== '') {
    $where[] = "t.thesis_keywords LIKE ?";
    $params[] = "%$keywords%";
}

$whereSql = count($where) ? ("WHERE " . implode(" AND ", $where)) : "";


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
    t.thesis_keywords,
    t.thesis_creation,
    t.thesis_status,
    author.user_id   AS student_id,
    author.user_fname AS author_fname,
    author.user_lname AS author_lname,
    d.dept_name,
    p.prog_name,
    adv.user_fname   AS adviser_fname,
    adv.user_lname   AS adviser_lname,
    lf.file_id,
    lf.file_name,
    lf.file_path,
    lf.uploaded_at
FROM thesis t
LEFT JOIN users author ON t.thesis_author_user_id_fk = author.user_id
LEFT JOIN users adv    ON t.thesis_adviser_user_id_fk = adv.user_id
LEFT JOIN departments d ON t.thesis_dept_id_fk = d.dept_id
LEFT JOIN programs p    ON t.thesis_prog_id_fk = p.prog_id
$latestFilesJoin
$whereSql
ORDER BY t.thesis_creation DESC, t.thesis_id DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();


function renderTable($rows) {
    ob_start(); ?>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author (Student)</th>
                <th>Department</th>
                <th>Program</th>
                <th>Year</th>
                <th>Adviser</th>
                <th>Keywords</th>
                <th>Status</th>
                <th>File</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="10" style="text-align:center">No submissions found.</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['thesis_title']) ?></td>
                    <td><?= htmlspecialchars(trim(($r['author_fname'] ?? '') . ' ' . ($r['author_lname'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($r['dept_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['prog_name'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['thesis_creation'] ? date('Y', strtotime($r['thesis_creation'])) : '—') ?></td>
                    <td><?= htmlspecialchars(trim(($r['adviser_fname'] ?? '') . ' ' . ($r['adviser_lname'] ?? ''))) ?></td>
                    <td><?= htmlspecialchars($r['thesis_keywords'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['thesis_status'] ?? 'pending') ?></td>
                    <td>
                        <?php if (!empty($r['file_path'])): ?>
                            <a href="<?= htmlspecialchars($r['file_path']) ?>" download>Download</a>
                        <?php else: ?>
                            No file
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="thesis_review.php?id=<?= urlencode($r['thesis_id']) ?>">Review</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <?php return ob_get_clean();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thesis Submissions</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
        }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        .card { 
            border:1px solid #ddd; 
            border-radius:8px; 
            padding:16px; 
            background:#fff; 
            margin-bottom:20px; 
        }

        .filters label { 
            display: inline-block; 
            width: 120px; 
            font-weight:bold; 
        }

        .filters input { 
            margin-bottom: 8px; 
            padding:6px; 
            border:1px solid #ccc; 
            border-radius:4px; 
        }

        .actions { 
            margin-top: 16px; 
        }

        .btn {
            display:inline-block;
            padding:8px 14px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:14px;
            text-decoration:none;
        }

        .btn:hover { 
            background:#0056b3; 
        }

        table { 
            width:100%; 
            border-collapse:collapse; 
        }

        th, td { 
            border:1px solid #ddd; 
            padding:8px; 
        }

        th { 
            background:#f7f7f7; 
        }

        .muted { 
            color:#678; 
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Thesis Submissions</h2>
    <p>Welcome, <?= htmlspecialchars($_SESSION['user_fname'] . " " . $_SESSION['user_lname']) ?> (<?= htmlspecialchars($_SESSION['user_email']) ?>)</p>

    <p><a href="index_adviser.php" class="btn">Back to Adviser Dashboard</a></p>

    <div class="card">
        <h3>Search Filters</h3>
        <div class="filters">
            <form id="filterForm" method="get">
                <div>
                    <label>Title:</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($title) ?>">
                </div>
                <div>
                    <label>Author:</label>
                    <input type="text" name="author" value="<?= htmlspecialchars($author) ?>">
                </div>
                <div>
                    <label>Year:</label>
                    <input type="number" name="year" value="<?= htmlspecialchars($year) ?>" min="1900" max="<?= date('Y') ?>">
                </div>
                <div>
                    <label>Adviser:</label>
                    <input type="text" name="adviser" value="<?= htmlspecialchars($adviser) ?>">
                </div>
                <div>
                    <label>Keywords:</label>
                    <input type="text" name="keywords" value="<?= htmlspecialchars($keywords) ?>">
                </div>

                <div class="actions">
                    <button type="submit" class="btn">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div id="results">
        <?= renderTable($rows) ?>
    </div>
</div>
</body>
</html>
