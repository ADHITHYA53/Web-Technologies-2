<?php
require_once __DIR__ . '/upload.php';
$id = (int)($_GET['id'] ?? 0); $act = $_GET['action'] ?? 'view';
$st = $conn->prepare("SELECT original_name, stored_name, file_type FROM student_files WHERE id = ?");
$st->bind_param("i", $id); $st->execute();
if (!($f = $st->get_result()->fetch_assoc())) die('File not found in database.');
$path = realpath(UPLOAD_DIR . basename($f['stored_name']));
if (!$path || !str_starts_with($path, realpath(UPLOAD_DIR)) || !is_file($path)) die('File not found on server.');
header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $f['file_type']);
header('Content-Disposition: ' . ($act === 'download' ? 'attachment' : 'inline') . '; filename="' . basename($f['original_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path); exit;
