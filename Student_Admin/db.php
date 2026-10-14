<?php
session_status() === PHP_SESSION_NONE && session_start();
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli('127.0.0.1', 'root', '', 'student_admission');
if ($conn->connect_error) die("Database connection failed. Ensure MySQL is running.");
$conn->set_charset('utf8mb4');
$COURSES = ['Computer Science and Engineering (CSE)', 'Information Technology (IT)', 'Electronics & Communication Engineering (ECE)', 'Electrical & Electronics Engineering (EEE)', 'Mechanical Engineering', 'Civil Engineering', 'Artificial Intelligence & Data Science (AI & DS)'];
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function csrf_token() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(16)); }
function csrf_field() { return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">'; }
function verify_csrf() { return hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf_token'] ?? ''); }
function set_flash($t, $m) { $_SESSION['flash'] = ['t' => $t, 'm' => $m]; }
function render_flash() {
    if (!isset($_SESSION['flash'])) return '';
    $f = $_SESSION['flash']; unset($_SESSION['flash']);
    return "<div class='alert alert-{$f['t']}'>" . e($f['m']) . "</div>";
}
function validate_student($n, $r, $em, $c) {
    $err = [];
    if ($n === '' || !preg_match("/^[a-zA-Z\s.'-]+$/", $n)) $err[] = "Enter a valid full name.";
    if ($r === '' || !preg_match('/^[a-zA-Z0-9_-]{2,20}$/', $r)) $err[] = "Enter a valid roll number (2-20 chars).";
    if (!filter_var($em, FILTER_VALIDATE_EMAIL)) $err[] = "Enter a valid email address.";
    if ($c === '') $err[] = "Please select a course.";
    return $err;
}
function nav($back = false) {
    $btn = $back ? "<a href='index.php' class='btn btn-sec btn-sm'>← Dashboard</a>" : "<a href='add_student.php' class='btn btn-pri btn-sm'>+ Add Student</a><a href='/Student_Registration/index.html' class='btn btn-sec btn-sm' target='_blank'>Registration Form ↗</a>";
    return "<nav class='navbar'><a href='index.php' class='brand'>🎓 <span>KPRIET</span> Student Admin</a><div class='nav-links'>$btn</div></nav>";
}
function student_fields($n, $r, $em, $co, $courses) {
    $opts = implode('', array_map(fn($c) => "<option value='" . e($c) . "'" . ($co === $c ? ' selected' : '') . ">" . e($c) . "</option>", $courses));
    return "<div class='form-grp'><label>Full Name *</label><input type='text' name='name' class='form-ctrl' value='" . e($n) . "' required></div>" .
           "<div class='form-grp'><label>Roll Number *</label><input type='text' name='roll_number' class='form-ctrl' value='" . e($r) . "' required></div>" .
           "<div class='form-grp'><label>Email *</label><input type='email' name='email' class='form-ctrl' value='" . e($em) . "' required></div>" .
           "<div class='form-grp'><label>Course *</label><select name='course' class='form-ctrl' required><option value=''>-- Select Course --</option>$opts</select></div>";
}
