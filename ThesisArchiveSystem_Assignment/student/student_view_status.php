<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'student') {
    die("Access denied. Only students can view thesis status.");
}

$studentId = $_SESSION['user_id'];
$uploadsDir = "../assets/uploads/";

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

$thesisSql = "
SELECT
    t.thesis_id,
    t.thesis_title,
    t.thesis_abstract,
    t.thesis_keywords,
    t.thesis_creation,
    t.thesis_status,
    d.dept_name,
    p.prog_name,
    adv.user_fname AS adviser_fname,
    adv.user_lname AS adviser_lname,
    adv.user_pfp AS adviser_pfp,
    adv.user_signature AS adviser_signature,
    lf.file_name,
    lf.file_path
FROM thesis t
LEFT JOIN departments d ON t.thesis_dept_id_fk = d.dept_id
LEFT JOIN programs p ON t.thesis_prog_id_fk = p.prog_id
LEFT JOIN users adv ON t.thesis_adviser_user_id_fk = adv.user_id
$latestFilesJoin
WHERE t.thesis_author_user_id_fk = ?
ORDER BY t.thesis_creation DESC
LIMIT 1
";
$tStmt = $pdo->prepare($thesisSql);
$tStmt->execute([$studentId]);
$thesis = $tStmt->fetch();

if (!$thesis) {
    die("No thesis submission found.");
}

$apprStmt = $pdo->prepare("
    SELECT approval_status, approval_comments, approval_date
    FROM approvals
    WHERE approval_thesis_id_fk = ?
    ORDER BY approval_date DESC, approval_id DESC
    LIMIT 1
");

$apprStmt->execute([$thesis['thesis_id']]);
$latestApproval = $apprStmt->fetch();

$status = strtolower($latestApproval['approval_status'] ?? $thesis['thesis_status']);
$titleLine = $status === 'approved' ? 'Thesis Approved!' : ($status === 'rejected' ? 'Thesis Rejected.' : 'Thesis Pending Review');
$bannerClass = in_array($status, ['approved','rejected']) ? $status : 'pending';
$comments = $latestApproval['approval_comments'] ?? '—';
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Thesis Status</title>
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

        .banner { 
            padding: 16px; 
            border-radius: 8px; 
            margin-bottom: 16px; 
        }

        .banner.approved { 
            background: #e8f7ea; 
            border: 1px solid #bfe6c7; 
        }

        .banner.rejected { 
            background: #fdecec; 
            border: 1px solid #f6b8b8; 
        }

        .banner.pending  { 
            background: #eef4ff; 
            border: 1px solid #c8d7ff; 
        }

        .label { 
            font-weight: bold; 
            width: 160px; 
            display: inline-block; 
        }

        .muted { 
            color: #678; 
        }

        .avatar { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            background: #f7f7f7; 
        }

        dl dt { 
            font-weight: bold; 
        }

        dl dd { 
            margin: 0 0 8px 0; 
        }

        .cards-row { 
            display:flex; 
            gap:24px; 
            margin-top:20px; 
        }

        .card { 
            flex:1; 
            border:1px solid #ddd; 
            border-radius:8px; 
            padding:16px; 
            background:#fff; 
        }

        .btn-link {
            display:inline-block;
            margin-top:20px;
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
    <div class="banner <?= htmlspecialchars($bannerClass) ?>">
        <h3><?= htmlspecialchars($titleLine) ?></h3>
        <p><span class="label">Comment from adviser:</span> <?= htmlspecialchars($comments) ?></p>
    </div>

    <div class="cards-row">
        <div class="card">
            <h3>Thesis Details</h3>
            <dl>
                <dt>Title</dt>
                <dd><?= htmlspecialchars($thesis['thesis_title'] ?? '—') ?></dd>

                <dt>Abstract</dt>
                <dd><?= nl2br(htmlspecialchars($thesis['thesis_abstract'] ?? '—')) ?></dd>

                <dt>Keywords</dt>
                <dd><?= htmlspecialchars($thesis['thesis_keywords'] ?? '—') ?></dd>

                <dt>Department</dt>
                <dd><?= htmlspecialchars($thesis['dept_name'] ?? '—') ?></dd>

                <dt>Program</dt>
                <dd><?= htmlspecialchars($thesis['prog_name'] ?? '—') ?></dd>

                <dt>Submitted</dt>
                <dd><?= htmlspecialchars($thesis['thesis_creation'] ? date('M d, Y', strtotime($thesis['thesis_creation'])) : '—') ?></dd>

                <dt>Latest File</dt>
                <dd>
                    <?php if (!empty($thesis['file_path'])): ?>
                        <a class="btn-link" href="<?= htmlspecialchars($thesis['file_path']) ?>" download>Download Thesis</a>
                        <span class="muted">(<?= htmlspecialchars($thesis['file_name']) ?>)</span>
                    <?php else: ?>
                        <span class="muted">No file uploaded.</span>
                    <?php endif; ?>
                </dd>
            </dl>
        </div>

        <?php if ($status === 'approved'): ?>
        <div class="card">
            <h3>Adviser</h3>

            <p><span class="label">Name:</span> 
                <?= htmlspecialchars(trim(($thesis['adviser_fname'] ?? '') . ' ' . ($thesis['adviser_lname'] ?? ''))) ?>
            </p>

            <div style="display:flex; gap:16px; margin-top:12px;">
                <div>
                    <p class="muted">Profile picture</p>
                    <?php if (!empty($thesis['adviser_pfp'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($thesis['adviser_pfp']) ?>" alt="Adviser profile picture">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No profile picture.</p>
                    <?php endif; ?>
                </div>
                
                <div>
                    <p class="muted">Signature</p>
                    <?php if (!empty($thesis['adviser_signature'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($thesis['adviser_signature']) ?>" alt="Adviser signature">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No signature.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <a href="index_students.php" class="btn-link">Back to Student Dashboard</a>
</div>
</body>
</html>

