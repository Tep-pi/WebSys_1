<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['student','adviser'], true)) {
    die("Access denied.");
}

$uploadsDir = "../assets/uploads/";
$thesisId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($thesisId <= 0) {
    die("Invalid thesis ID.");
}

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
    author.user_id   AS student_id,
    author.user_fname AS student_fname,
    author.user_lname AS student_lname,
    author.user_email AS student_email,
    author.user_pfp   AS student_pfp,
    author.user_signature AS student_signature,
    d.dept_name,
    p.prog_name,
    adv.user_id      AS adviser_user_id,
    adv.user_fname   AS adviser_fname,
    adv.user_lname   AS adviser_lname,
    adv.user_pfp     AS adviser_pfp,
    adv.user_signature AS adviser_signature,
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
WHERE t.thesis_id = ?
LIMIT 1
";
$tStmt = $pdo->prepare($thesisSql);
$tStmt->execute([$thesisId]);
$thesis = $tStmt->fetch();

if (!$thesis) {
    die("Thesis not found.");
}

$apprStmt = $pdo->prepare("
    SELECT approval_status, approval_comments, approval_date
    FROM approvals
    WHERE approval_thesis_id_fk = ?
    ORDER BY approval_date DESC, approval_id DESC
    LIMIT 1
");
$apprStmt->execute([$thesisId]);
$latestApproval = $apprStmt->fetch();

$bannerStatus = $latestApproval ? strtolower($latestApproval['approval_status']) : strtolower($thesis['thesis_status'] ?? 'pending');
$titleLine = $bannerStatus === 'approved' ? 'Thesis Approved!' : ($bannerStatus === 'rejected' ? 'Thesis Rejected.' : 'Thesis Pending Review');
$bannerClass = in_array($bannerStatus, ['approved','rejected']) ? $bannerStatus : 'pending';
$comments = $latestApproval['approval_comments'] ?? '—';


$showAdviserIdentity = ($bannerStatus === 'approved');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thesis Check</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
        }

        .container { 
            max-width: 980px; 
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

        .section { 
            margin-bottom: 18px; 
        }

        .row { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 18px; 
        }

        .box { 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 14px; 
            background: #fff; 
        }

        .label { 
            font-weight: bold; 
            width: 180px; 
            display: inline-block; 
        }

        .avatar { 
            width: 140px; 
            height: 140px; 
            object-fit: cover; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
            background: #f7f7f7; 
        }

        .muted { 
            color: #678; 
        }

        dt { 
            font-weight: bold; 
        }

        dd { 
            margin: 0 0 8px 0; 
        }

        .download a { 
            text-decoration: none; 
        }
    </style>
</head>

<body>
<div class="container">
    <div class="banner <?= htmlspecialchars($bannerClass) ?>">
        <h3><?= htmlspecialchars($titleLine) ?></h3>

        <p><span class="label">Comment from adviser:</span> 
            <?= htmlspecialchars($comments) ?>
        </p>
    </div>

    <div class="section row">
        <div class="box">
            <h3>Student profile</h3>
            <p><span class="label">Name:</span> 
                <?= htmlspecialchars(trim(($thesis['student_fname'] ?? '') . ' ' . ($thesis['student_lname'] ?? ''))) ?>
            </p>
            <p><span class="label">Email:</span> 
                <?= htmlspecialchars($thesis['student_email'] ?? '—') ?>
            </p>
            <p><span class="label">Department:</span> 
                <?= htmlspecialchars($thesis['dept_name'] ?? '—') ?>
            </p>
            <p><span class="label">Program:</span> 
                <?= htmlspecialchars($thesis['prog_name'] ?? '—') ?>
            </p>

            <div style="display:flex; gap:16px; margin-top:12px;">
                <div>
                    <p class="muted">Profile picture</p>
                    <?php if (!empty($thesis['student_pfp'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($thesis['student_pfp']) ?>" alt="Student profile picture">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No profile picture.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <p class="muted">Signature</p>
                    <?php if (!empty($thesis['student_signature'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($thesis['student_signature']) ?>" alt="Student signature">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No signature.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="box">
            <h3>Adviser</h3>
            <?php if ($showAdviserIdentity): ?>
                <p><span class="label">Name:</span> 
                    <?= htmlspecialchars(trim(($thesis['adviser_fname'] ?? '') . ' ' . ($thesis['adviser_lname'] ?? ''))) ?: '—' ?>
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
                
            <?php else: ?>
                <p class="muted">Adviser identity hidden for rejected status.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="section box">
        <h3>Thesis details</h3>
        <dl>
            <dt>Title</dt>
            <dd><?= htmlspecialchars($thesis['thesis_title'] ?? '—') ?></dd>

            <dt>Abstract</dt>
            <dd><?= nl2br(htmlspecialchars($thesis['thesis_abstract'] ?? '—')) ?></dd>

            <dt>Keywords</dt>
            <dd><?= htmlspecialchars($thesis['thesis_keywords'] ?? '—') ?></dd>

            <dt>Submitted</dt>
            <dd><?= htmlspecialchars($thesis['thesis_creation'] ? date('M d, Y', strtotime($thesis['thesis_creation'])) : '—') ?></dd>

            <dt>Latest file</dt>
            <dd class="download">
                <?php if (!empty($thesis['file_path'])): ?>
                    <a href="<?= htmlspecialchars($thesis['file_path']) ?>" download>Download thesis</a>
                    <span class="muted"> (<?= htmlspecialchars($thesis['file_name']) ?>)</span>
                <?php else: ?>
                    <span class="muted">No file uploaded.</span>
                <?php endif; ?>
            </dd>
        </dl>
    </div>

    <p>
        <?php if ($_SESSION['user_role'] === 'adviser'): ?>
            <a href="thesis_submissions.php">Back to submissions</a>
        <?php else: ?>
            <a href="../student/index_students.php">Back to Dashboard</a>
        <?php endif; ?>
    </p>
</div>
</body>
</html>
