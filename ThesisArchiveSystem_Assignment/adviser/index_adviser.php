<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'adviser') {
    die("Access denied. Only advisers can view this page.");
}

$adviserId   = $_SESSION['user_id'];
$adviserName = $_SESSION['user_fname'] . " " . $_SESSION['user_lname'];
$error = null;

$countSql = "
    SELECT thesis_status, COUNT(*) AS total
    FROM thesis
    WHERE thesis_adviser_user_id_fk = ?
       OR (thesis_adviser_user_id_fk IS NULL AND ? = ?)
    GROUP BY thesis_status
";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute([$adviserId, $adviserName, $adviserName]);
$countsRaw = $countStmt->fetchAll(PDO::FETCH_ASSOC);

$counts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
foreach ($countsRaw as $row) {
    $status = $row['thesis_status'] ?? 'pending';
    if (isset($counts[$status])) {
        $counts[$status] = (int)$row['total'];
    }
}

$searchResults = [];
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $term = "%" . $_GET['search'] . "%";
    $searchSql = "
        SELECT t.thesis_id, t.thesis_title, t.thesis_keywords, t.thesis_creation,
               author.user_fname, author.user_lname, d.dept_name, p.prog_name, f.file_name, f.file_path
        FROM thesis t
        LEFT JOIN users author ON t.thesis_author_user_id_fk = author.user_id
        LEFT JOIN departments d ON t.thesis_dept_id_fk = d.dept_id
        LEFT JOIN programs p ON t.thesis_prog_id_fk = p.prog_id
        LEFT JOIN (
            SELECT f1.*
            FROM files f1
            INNER JOIN (
                SELECT thesis_id, MAX(uploaded_at) AS latest_uploaded
                FROM files
                GROUP BY thesis_id
            ) f2 ON f1.thesis_id = f2.thesis_id AND f1.uploaded_at = f2.latest_uploaded
        ) f ON f.thesis_id = t.thesis_id
        WHERE t.thesis_status = 'approved'
          AND (t.thesis_title LIKE ? OR t.thesis_keywords LIKE ?)
          AND (t.thesis_adviser_user_id_fk = ? OR ? = ?)
        ORDER BY t.thesis_creation DESC
    ";
    $stmtSearch = $pdo->prepare($searchSql);
    $stmtSearch->execute([$term, $term, $adviserId, $adviserName, $adviserName]);
    $searchResults = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);
} else {
    $searchSql = "
        SELECT t.thesis_id, t.thesis_title, t.thesis_keywords, t.thesis_creation,
               author.user_fname, author.user_lname, d.dept_name, p.prog_name, f.file_name, f.file_path
        FROM thesis t
        LEFT JOIN users author ON t.thesis_author_user_id_fk = author.user_id
        LEFT JOIN departments d ON t.thesis_dept_id_fk = d.dept_id
        LEFT JOIN programs p ON t.thesis_prog_id_fk = p.prog_id
        LEFT JOIN (
            SELECT f1.*
            FROM files f1
            INNER JOIN (
                SELECT thesis_id, MAX(uploaded_at) AS latest_uploaded
                FROM files
                GROUP BY thesis_id
            ) f2 ON f1.thesis_id = f2.thesis_id AND f1.uploaded_at = f2.latest_uploaded
        ) f ON f.thesis_id = t.thesis_id
        WHERE t.thesis_status = 'approved'
          AND (t.thesis_adviser_user_id_fk = ? OR ? = ?)
        ORDER BY t.thesis_creation DESC
    ";
    $stmtSearch = $pdo->prepare($searchSql);
    $stmtSearch->execute([$adviserId, $adviserName, $adviserName]);
    $searchResults = $stmtSearch->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adviser Dashboard</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin:0; 
            padding:0;
        }

        .container { 
            max-width: 960px; 
            margin: 24px auto; 
            padding: 0 16px; 
        }

        .grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 12px; 
            margin-bottom: 20px; 
        }

        .card { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 16px; 
            background: #fff; 
        }

        .card.pending { 
            background:#fffbe6; 
            border:1px solid #ffe58f; 
        }

        .card.approved { 
            background:#e6ffed; 
            border:1px solid #b7eb8f; 
        }

        .card.rejected { 
            background:#fff1f0; 
            border:1px solid #ffa39e; 
        }

        .search-box { 
            margin: 20px 0; 
        }

        .results { 
            margin-top: 20px; 
        }

        .results table { 
            width: 100%; 
            border-collapse: collapse; 
        }

        .results th, .results td { 
            border: 1px solid #ddd; 
            padding: 8px; 
        }

        .results th { 
            background: #f7f7f7; 
        }

        .muted { 
            color: #678; 
        }

        .btn-link {
            display:inline-block;
            margin:6px 8px 0 0;
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

        .btn {
            padding:8px 14px;
            background:#007bff;
            color:#fff;
            border:none;
            border-radius:4px;
            cursor:pointer;
            font-size:14px;
        }

        .btn:hover { 
            background:#0056b3; 
        }

        input[type="text"] {
            padding:6px;
            border:1px solid #ccc;
            border-radius:4px;
            margin-right:8px;
        }
    </style>
</head>

<body>
<div class="container">
    <h2>Adviser Dashboard</h2>

    <div class="grid">
        <div class="card pending">
            <h3>Pending</h3>
            <p class="muted">Awaiting your review</p>
            <p style="font-size:24px;">
                <?= htmlspecialchars($counts['pending']) ?>
            </p>
        </div>

        <div class="card approved">
            <h3>Approved</h3>
            <p class="muted">Completed with your sign-off</p>
            <p style="font-size:24px;">
                <?= htmlspecialchars($counts['approved']) ?>
            </p>
        </div>

        <div class="card rejected">
            <h3>Rejected</h3>
            <p class="muted">Returned for revision</p>
            <p style="font-size:24px;">
                <?= htmlspecialchars($counts['rejected']) ?>
            </p>
        </div>
    </div>

    <div class="links">
        <a href="adviser_profile.php" class="btn-link">My Profile</a>
        <a href="thesis_submissions.php" class="btn-link">Thesis Submissions</a>
    </div>

    <div class="search-box">
        <form method="get">
            <label for="search">Search Approved Titles:</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </div>


    <?php if (!empty($searchResults)): ?>
        <div class="results">
            <h3>Approved Theses</h3>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Department</th>
                    <th>Program</th>
                    <th>Submitted</th>
                    <th>File</th>
                </tr>

                <?php foreach ($searchResults as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['thesis_title']) ?></td>
                        <td><?= htmlspecialchars($row['user_fname'] . " " . $row['user_lname']) ?></td>
                        <td><?= htmlspecialchars($row['dept_name']) ?></td>
                        <td><?= htmlspecialchars($row['prog_name']) ?></td>
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
        
    <?php else: ?>
        <p class="muted">No approved theses found.</p>
    <?php endif; ?>

</div>
</body>
</html>

