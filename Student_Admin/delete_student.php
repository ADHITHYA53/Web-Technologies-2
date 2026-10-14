<?php
require_once __DIR__ . '/upload.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) { set_flash('error', 'Invalid security token or request.'); header('Location: index.php'); exit; }
$id = (int)($_POST['student_id'] ?? 0);
if ($id > 0) {
    delete_files($id, $conn);
    $st = $conn->prepare("DELETE FROM students WHERE id = ?");
    $st->bind_param("i", $id); $st->execute();
    set_flash('success', 'Student record and associated files deleted successfully.');
}
header('Location: index.php'); exit;
