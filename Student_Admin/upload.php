<?php
require_once __DIR__ . '/db.php';
define('UPLOAD_DIR', __DIR__ . '/uploads/');
function handle_uploads($files, $sid, $conn, &$errors) {
    $mimes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'pdf' => 'application/pdf'];
    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0755, true);
    $items = []; $cnt = 0; $names = (array)($files['name'] ?? []);
    foreach ($names as $i => $n) {
        $err = is_array($files['error'] ?? null) ? $files['error'][$i] : ($files['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err !== UPLOAD_ERR_NO_FILE && !empty($n))
            $items[] = ['name' => $n, 'tmp' => is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'], 'err' => $err, 'size' => is_array($files['size']) ? $files['size'][$i] : $files['size']];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    foreach ($items as $f) {
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $detMime = is_file($f['tmp']) ? finfo_file($finfo, $f['tmp']) : '';
        if ($f['err'] !== UPLOAD_ERR_OK || $f['size'] > 5242880 || !isset($mimes[$ext]) || ($detMime !== $mimes[$ext] && !($ext === 'jpg' && $detMime === 'image/jpeg'))) {
            $errors[] = "Invalid upload '{$f['name']}'. Only JPG, PNG, PDF up to 5MB allowed."; continue;
        }
        $stored = 'doc_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $dest = UPLOAD_DIR . $stored;
        if (is_uploaded_file($f['tmp']) ? move_uploaded_file($f['tmp'], $dest) : copy($f['tmp'], $dest)) {
            $st = $conn->prepare("INSERT INTO student_files (student_id, original_name, stored_name, file_path, file_type, upload_date) VALUES (?, ?, ?, ?, ?, NOW())");
            $rel = 'uploads/' . $stored;
            $st->bind_param("issss", $sid, $f['name'], $stored, $rel, $detMime);
            if ($st->execute()) $cnt++;
        }
    }
    finfo_close($finfo);
    return $cnt;
}
function delete_files($sid, $conn) {
    $st = $conn->prepare("SELECT stored_name FROM student_files WHERE student_id = ?");
    $st->bind_param("i", $sid); $st->execute();
    $res = $st->get_result();
    while ($r = $res->fetch_assoc()) @unlink(UPLOAD_DIR . basename($r['stored_name']));
}
function delete_one_file($fid, $sid, $conn) {
    $st = $conn->prepare("SELECT stored_name FROM student_files WHERE id = ? AND student_id = ?");
    $st->bind_param("ii", $fid, $sid); $st->execute();
    if ($r = $st->get_result()->fetch_assoc()) @unlink(UPLOAD_DIR . basename($r['stored_name']));
    $del = $conn->prepare("DELETE FROM student_files WHERE id = ? AND student_id = ?");
    $del->bind_param("ii", $fid, $sid); $del->execute();
}
function handle_student_uploads($f, $s, $c, &$e) { return handle_uploads($f, $s, $c, $e); } function delete_student_physical_files($s, $c) { delete_files($s, $c); }
