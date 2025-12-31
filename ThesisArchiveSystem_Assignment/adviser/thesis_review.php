<?php
session_start();
require_once __DIR__ . "/../db/db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'adviser') {
    die("Access denied. Only advisers can review theses.");
}

$adviserId = $_SESSION['user_id'];
$uploadsDir = "../assets/uploads/";
$error = null;
$success = null;

$thesisId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($thesisId <= 0) {
    die("Invalid thesis ID.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['review_submit'])) {
    $status   = isset($_POST['status']) ? strtolower(trim($_POST['status'])) : '';
    $comments = trim($_POST['comments'] ?? '');

    if (!in_array($status, ['approved', 'rejected'], true)) {
        $error = "Please choose Approved or Rejected.";
    } else {
        try {
            $pdo->beginTransaction();

            $assignStmt = $pdo->prepare("UPDATE thesis SET thesis_adviser_user_id_fk = COALESCE(thesis_adviser_user_id_fk, ?) WHERE thesis_id = ?");
            $assignStmt->execute([$adviserId, $thesisId]);

            $apprStmt = $pdo->prepare("
                INSERT INTO approvals (approval_thesis_id_fk, approval_status, approval_comments, approval_date)
                VALUES (?, ?, ?, NOW())
            ");
            $apprStmt->execute([$thesisId, $status, $comments]);

            $updThesis = $pdo->prepare("UPDATE thesis SET thesis_status = ? WHERE thesis_id = ?");
            $updThesis->execute([$status, $thesisId]);

            $logStmt = $pdo->prepare("
                INSERT INTO review_logs (thesis_id, adviser_user_id_fk, action, comments, log_date)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $logStmt->execute([$thesisId, $adviserId, strtoupper($status), $comments]);

            // Activity log heree
            $actStmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, action, activity_time)
                VALUES (?, ?, NOW())
            ");
            $actStmt->execute([$adviserId, "Thesis #{$thesisId} {$status}: {$comments}"]);

            $pdo->commit();

            header("Location: thesis_check.php?id=" . urlencode($thesisId));
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Failed to save review: " . $e->getMessage();
        }
    }
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

$bannerStatus = $latestApproval ? strtolower($latestApproval['approval_status']) : null;
$titleLine = $bannerStatus === 'approved' ? 'Thesis Approved!' : ($bannerStatus === 'rejected' ? 'Thesis Rejected.' : 'Pending Review');
$bannerClass = $bannerStatus ?: 'pending';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Thesis Review</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            background:#f4f6f9; 
            margin:0; 
            padding:0; 
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

        textarea { 
            width: 100%; 
            min-height: 120px; 
        }

        select, input[type="text"] { 
            padding: 8px; 
        }

        .actions { 
            margin-top: 12px; 
            display: flex; 
            gap: 10px; 
        }

        .download a { 
            text-decoration: none; 
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
    </style>
</head>
<body>
<div class="container">
    <h2>Thesis Review</h2>

    <?php if ($error): ?>
        <p style="color:#b00020"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:#2e7d32"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <div class="banner <?= htmlspecialchars($bannerClass) ?>">
        <h3><?= htmlspecialchars($titleLine) ?></h3>

        <p><span class="label">Comment from adviser:</span> 
            <?= htmlspecialchars($latestApproval['approval_comments'] ?? '—') ?>
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
            <p><span class="label">You are signed in as:</span> 
                <?= htmlspecialchars($_SESSION['user_fname'] . " " . $_SESSION['user_lname']) ?>
            </p>

            <div style="display:flex; gap:16px; margin-top:12px;">

                <div>
                    <p class="muted">Profile picture</p>
                    <?php if (!empty($_SESSION['user_pfp'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($_SESSION['user_pfp']) ?>" alt="Adviser profile picture">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No profile picture.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <p class="muted">Signature</p>
                    <?php if (!empty($_SESSION['user_signature'])): ?>
                        <img class="avatar" src="<?= $uploadsDir . htmlspecialchars($_SESSION['user_signature']) ?>" alt="Adviser signature">
                    <?php else: ?>
                        <div class="avatar"></div><p class="muted">No signature.</p>
                    <?php endif; ?>
                </div>
            </div>

            <p style="margin-top:8px;">
                <a href="adviser_profile.php" class="btn">Update my profile/signature</a>
            </p>

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
                    <a href="<?= htmlspecialchars($thesis['file_path']) ?>" download class="btn">Download thesis</a>
                    <span class="muted"> (<?= htmlspecialchars($thesis['file_name']) ?>)</span>
                <?php else: ?>
                    <span class="muted">No file uploaded.</span>
                <?php endif; ?>
            </dd>
        </dl>
    </div>

    <div class="section box">
        <h3>Decision</h3>
        <form method="post">
            <label class="label" for="status">Set status</label>
            <select name="status" id="status" required>
                <option value="">Select…</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>

            <div style="margin-top:12px;">
                <label class="label" for="comments">Comments</label>
                <textarea name="comments" id="comments" placeholder="Write your feedback…"></textarea>
            </div>

            <div class="actions">
                <button type="submit" name="review_submit" class="btn">Submit</button>
                <a href="thesis_submissions.php" class="btn">Back to submissions</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
