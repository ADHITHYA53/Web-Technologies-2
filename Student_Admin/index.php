<?php
require_once __DIR__ . '/db.php';
$search = trim($_GET['search'] ?? '');
$sql = "SELECT s.*, (SELECT COUNT(*) FROM student_files WHERE student_id = s.id) AS file_count FROM students s";
if ($search !== '') {
    $st = $conn->prepare($sql . " WHERE s.name LIKE ? OR s.roll_number LIKE ? OR s.email LIKE ? ORDER BY s.id ASC");
    $q = "%$search%"; $st->bind_param("sss", $q, $q, $q);
} else { $st = $conn->prepare($sql . " ORDER BY s.id ASC"); }
$st->execute();
$students = $st->get_result()->fetch_all(MYSQLI_ASSOC);
$total = (int)$conn->query("SELECT COUNT(*) AS c FROM students")->fetch_assoc()['c']; $docs = (int)($conn->query("SELECT COUNT(DISTINCT student_id) AS c FROM student_files")->fetch_assoc()['c'] ?? 0);
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Student Admin Panel | KPRIET</title><link rel="stylesheet" href="style.css"></head><body>
<?= nav(); ?>
<main class="container">
  <?= render_flash(); ?>
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-lbl">Total Students</div><div class="stat-val"><?= $total ?></div></div>
    <div class="stat-card"><div class="stat-lbl">Students With Documents</div><div class="stat-val"><?= $docs ?></div></div>
  </div>
  <div class="card">
    <div class="card-head">
      <h2>Student Records</h2>
      <form method="GET" class="search-form">
        <input type="text" name="search" class="form-ctrl" placeholder="Search name, roll, email..." value="<?= e($search) ?>">
        <button type="submit" class="btn btn-pri btn-sm">Search</button>
        <?php if ($search !== ''): ?><a href="index.php" class="btn btn-sec btn-sm">Clear</a><?php endif; ?>
      </form>
    </div>
    <div class="table-responsive tbl-wrap">
      <table class="tbl">
        <thead><tr><th class="col-id">#</th><th class="col-roll">Roll No</th><th class="col-name">Name</th><th class="col-email">Email</th><th class="col-course">Course</th><th class="col-files">Files</th><th class="col-date">Date</th><th class="col-act">Actions</th></tr></thead>
        <tbody>
          <?php if (empty($students)): ?><tr><td colspan="8" style="text-align:center;padding:1.5rem;color:#64748b;">No student records found.</td></tr>
          <?php else: foreach ($students as $idx => $s): ?>
            <tr>
              <td class="col-id">#<?= $idx + 1 ?></td>
              <td class="col-roll"><span class="badge-roll"><?= e($s['roll_number']) ?></span></td>
              <td class="col-name"><strong><?= e($s['name']) ?></strong></td>
              <td class="col-email"><?= e($s['email']) ?></td>
              <td class="col-course"><?= e($s['course']) ?></td>
              <td class="col-files"><span class="badge-cnt"><?= (int)$s['file_count'] ?> <?= (int)$s['file_count'] > 1 ? 'files' : 'file' ?></span></td>
              <td class="col-date"><?= date('d M Y', strtotime($s['created_at'])) ?></td>
              <td class="col-act">
                <div class="menu-wrap">
                  <button type="button" class="menu-btn" aria-label="Actions for <?= e($s['name']) ?>" aria-haspopup="true" aria-expanded="false" title="Actions">&#8942;</button>
                  <div class="menu-drop" role="menu">
                    <a href="view_student.php?id=<?= $s['id'] ?>" role="menuitem">View</a>
                    <a href="edit_student.php?id=<?= $s['id'] ?>" role="menuitem">Edit</a>
                    <form method="POST" action="delete_student.php" onsubmit="return confirm('Delete <?= e(addslashes($s['name'])) ?>?');"><?= csrf_field(); ?><input type="hidden" name="student_id" value="<?= $s['id'] ?>"><button type="submit" class="menu-del" role="menuitem">Delete</button></form>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>
document.addEventListener('click', e => {
  const btn = e.target.closest('.menu-btn'), wrap = btn ? btn.closest('.menu-wrap') : null;
  document.querySelectorAll('.menu-wrap.open').forEach(m => { if (m !== wrap) { m.classList.remove('open', 'up'); m.querySelector('.menu-btn')?.setAttribute('aria-expanded', 'false'); } });
  if (btn && wrap) {
    e.stopPropagation();
    const isOpen = wrap.classList.toggle('open');
    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    if (isOpen) wrap.classList.toggle('up', (window.innerHeight - wrap.getBoundingClientRect().bottom) < 130);
  }
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.menu-wrap.open').forEach(m => { m.classList.remove('open', 'up'); m.querySelector('.menu-btn')?.setAttribute('aria-expanded', 'false'); }); });
</script>
</body></html>
