<?php
require_once __DIR__ . '/upload.php';
$errors = [];
$name = trim($_POST['name'] ?? ''); $roll = trim($_POST['roll_number'] ?? ''); $email = trim($_POST['email'] ?? ''); $course = trim($_POST['course'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) $errors[] = "Security token mismatch.";
    $errors = array_merge($errors, validate_student($name, $roll, $email, $course));
    if (empty($errors)) {
        $dup = $conn->prepare("SELECT id FROM students WHERE roll_number = ?");
        $dup->bind_param("s", $roll); $dup->execute();
        if ($dup->get_result()->num_rows > 0) $errors[] = "Roll number '{$roll}' already exists. Duplicate roll numbers are not allowed.";
        else {
            $ins = $conn->prepare("INSERT INTO students (name, roll_number, email, course, created_at) VALUES (?, ?, ?, ?, NOW())");
            $ins->bind_param("ssss", $name, $roll, $email, $course);
            if ($ins->execute()) {
                $sid = $ins->insert_id;
                handle_uploads($_FILES['files'] ?? [], $sid, $conn, $errors);
                set_flash('success', "Student '{$name}' registered successfully.");
                header("Location: view_student.php?id=" . $sid); exit;
            } else { $errors[] = "Failed to save student record."; }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Add Student | KPRIET</title><link rel="stylesheet" href="style.css"></head><body>
<?= nav(true); ?>
<main class="container" style="max-width:700px;">
  <?php if (!empty($errors)): ?><div class="alert alert-error"><?= implode('<br>', array_map('e', $errors)); ?></div><?php endif; ?>
  <div class="card">
    <div class="card-head"><h2>Add Student Record</h2></div>
    <form method="POST" enctype="multipart/form-data">
      <?= csrf_field(); ?>
      <div class="form-grid">
        <?= student_fields($name, $roll, $email, $course, $COURSES); ?>
        <div class="form-grp full"><label>Upload Documents (JPG, PNG, PDF, max 5MB)</label><input type="file" name="files[]" class="form-ctrl" accept=".jpg,.jpeg,.png,.pdf" multiple></div>
      </div>
      <div class="form-actions"><a href="index.php" class="btn btn-sec">Cancel</a><button type="submit" class="btn btn-pri">Save Student</button></div>
    </form>
  </div>
</main></body></html>
