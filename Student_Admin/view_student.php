<?php
require_once __DIR__ . '/db.php';
$id = (int)($_GET['id'] ?? 0);
$st = $conn->prepare("SELECT * FROM students WHERE id = ?");
$st->bind_param("i", $id); $st->execute();
if (!($student = $st->get_result()->fetch_assoc())) { set_flash('error', 'Student record not found.'); header('Location: index.php'); exit; }
$stf = $conn->prepare("SELECT * FROM student_files WHERE student_id = ? ORDER BY id DESC");
$stf->bind_param("i", $id); $stf->execute();
$files = $stf->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?= e($student['name']) ?> | Student Profile</title><link rel="stylesheet" href="style.css"></head><body>
<?= nav(true); ?>
<main class="container">
  <?= render_flash(); ?>
  <div class="card">
    <div class="card-head">
      <div><h2><?= e($student['name']) ?></h2><span class="badge-roll"><?= e($student['roll_number']) ?></span></div>
      <div>
        <a href="edit_student.php?id=<?= $id ?>" class="btn btn-sec btn-sm">Edit</a>
        <form method="POST" action="delete_student.php" style="display:inline;" onsubmit="return confirm('Delete this student?');"><?= csrf_field(); ?><input type="hidden" name="student_id" value="<?= $id ?>"><button type="submit" class="btn btn-dan btn-sm">Delete</button></form>
      </div>
    </div>
    <div class="detail-grid">
      <div class="detail-box"><small>Full Name</small><div><?= e($student['name']) ?></div></div>
      <div class="detail-box"><small>Roll Number</small><div><?= e($student['roll_number']) ?></div></div>
      <div class="detail-box"><small>Email</small><div><a href="mailto:<?= e($student['email']) ?>"><?= e($student['email']) ?></a></div></div>
      <div class="detail-box"><small>Course</small><div><?= e($student['course']) ?></div></div>
      <div class="detail-box"><small>Registered Date</small><div><?= date('d M Y, h:i A', strtotime($student['created_at'])) ?></div></div>
    </div>
    <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid #f1f5f9;">
      <div class="card-head"><h3>Uploaded Files (<?= count($files) ?>)</h3><a href="edit_student.php?id=<?= $id ?>" class="btn btn-sec btn-sm">+ Upload Files</a></div>
      <?php if (empty($files)): ?><p style="color:#64748b;padding:1rem 0;">No documents uploaded yet.</p><?php else: ?>
        <div class="file-grid">
          <?php foreach ($files as $f): $isImg = str_starts_with($f['file_type'], 'image/'); ?>
            <div class="file-card">
              <?php if ($isImg): ?><img src="download.php?id=<?= $f['id'] ?>&action=view" style="max-height:100px;object-fit:contain;margin-bottom:0.5rem;border-radius:4px;" alt="preview"><?php else: ?><div style="font-size:2rem;text-align:center;">📄</div><?php endif; ?>
              <div><strong><?= e($f['original_name']) ?></strong><br><small style="color:#64748b;"><?= e($f['file_type']) ?></small></div>
              <div style="margin-top:0.5rem;display:flex;gap:0.4rem;"><a href="download.php?id=<?= $f['id'] ?>&action=view" class="btn btn-sec btn-sm" target="_blank">View ↗</a><a href="download.php?id=<?= $f['id'] ?>&action=download" class="btn btn-pri btn-sm">Download ↓</a></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main></body></html>
