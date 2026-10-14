<?php
require_once __DIR__ . '/upload.php';
$id = (int)($_GET['id'] ?? $_POST['student_id'] ?? 0);
$st = $conn->prepare("SELECT * FROM students WHERE id = ?");
$st->bind_param("i", $id); $st->execute();
if (!($student = $st->get_result()->fetch_assoc())) { set_flash('error', 'Student record not found.'); header('Location: index.php'); exit; }
$errors = [];
$name = trim($_POST['name'] ?? $student['name']); $roll = trim($_POST['roll_number'] ?? $student['roll_number']); $email = trim($_POST['email'] ?? $student['email']); $course = trim($_POST['course'] ?? $student['course']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { $errors[] = "Security token mismatch."; }
    elseif (($_POST['action'] ?? '') === 'delete_file') {
        delete_one_file((int)$_POST['file_id'], $id, $conn); header("Location: edit_student.php?id=" . $id); exit;
    } else {
        $errors = array_merge($errors, validate_student($name, $roll, $email, $course));
        if (empty($errors)) {
            $dup = $conn->prepare("SELECT id FROM students WHERE roll_number = ? AND id != ?");
            $dup->bind_param("si", $roll, $id); $dup->execute();
            if ($dup->get_result()->num_rows > 0) $errors[] = "Roll number '{$roll}' is used by another student.";
            else {
                $up = $conn->prepare("UPDATE students SET name = ?, roll_number = ?, email = ?, course = ? WHERE id = ?");
                $up->bind_param("ssssi", $name, $roll, $email, $course, $id);
                if ($up->execute()) {
                    handle_uploads($_FILES['files'] ?? [], $id, $conn, $errors);
                    set_flash('success', 'Student updated successfully.');
                    header("Location: view_student.php?id=" . $id); exit;
                } else { $errors[] = "Database update failed."; }
            }
        }
    }
}
$files = $conn->query("SELECT * FROM student_files WHERE student_id = {$id}")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Edit Student | KPRIET</title><link rel="stylesheet" href="style.css"></head><body>
<?= nav(true); ?>
<main class="container" style="max-width:760px;">
  <?= render_flash(); ?>
  <?php if (!empty($errors)): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)); ?></div><?php endif; ?>
  <div class="card">
    <div class="card-head"><h2>Edit Student</h2><span class="badge-roll"><?= e($student['roll_number']) ?></span></div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrf_field(); ?><input type="hidden" name="student_id" value="<?= $id ?>">
      <div class="form-grid">
        <?= student_fields($name, $roll, $email, $course, $COURSES); ?>
        <div class="form-grp full"><label>Add More Files (JPG, PNG, PDF)</label><input type="file" name="files[]" class="form-ctrl" accept=".jpg,.jpeg,.png,.pdf" multiple></div>
      </div>
      <div class="form-actions"><a href="view_student.php?id=<?= $id ?>" class="btn btn-sec">Cancel</a><button type="submit" class="btn btn-pri">Update Record</button></div>
    </form>
  </div>
  <div class="card">
    <div class="card-head"><h3>Attached Files (<?= count($files) ?>)</h3></div>
    <?php if (empty($files)): ?><p style="color:#64748b;">No documents attached.</p><?php else: ?>
      <div class="file-grid">
        <?php foreach ($files as $f): ?>
          <div class="file-card">
            <div><strong><?= e($f['original_name']) ?></strong><br><small style="color:#64748b;"><?= e($f['file_type']) ?></small></div>
            <div style="margin-top:0.5rem;display:flex;gap:0.4rem;">
              <a href="download.php?id=<?= $f['id'] ?>&action=view" class="btn btn-sec btn-sm" target="_blank">View</a>
              <a href="download.php?id=<?= $f['id'] ?>&action=download" class="btn btn-sec btn-sm">Download</a>
              <form method="POST" style="display:inline;"><?= csrf_field(); ?><input type="hidden" name="action" value="delete_file"><input type="hidden" name="file_id" value="<?= $f['id'] ?>"><input type="hidden" name="student_id" value="<?= $id ?>"><button type="submit" class="btn btn-dan btn-sm" onclick="return confirm('Remove file?');">Delete</button></form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main></body></html>
