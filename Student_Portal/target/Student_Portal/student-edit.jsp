<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Student — Admin Portal</title><link rel="stylesheet" href="${pageContext.request.contextPath}/style.css"></head>
<body>
    <nav class="navbar"><a href="${pageContext.request.contextPath}/admin" class="brand">🎓 KPRIET <span>Admin Portal</span></a>
    <div class="nav-links"><a href="${pageContext.request.contextPath}/admin" class="btn btn-sec btn-sm">← Back to Dashboard</a></div></nav>
    <div class="container" style="max-width:550px;"><div class="card">
        <div class="card-head"><div>Edit Student: <span style="color:var(--pri)"><c:out value="${student.name}"/></span></div></div>
        <form action="${pageContext.request.contextPath}/student-edit" method="POST">
            <input type="hidden" name="id" value="${student.id}">
            <div class="form-grp"><label>Full Name</label><input type="text" name="name" class="form-ctrl" value="<c:out value='${student.name}'/>" required></div>
            <div class="form-grp"><label>Roll Number</label><input type="text" name="rollNumber" class="form-ctrl" value="<c:out value='${student.rollNumber}'/>" required></div>
            <div class="form-grp"><label>Email Address</label><input type="email" name="email" class="form-ctrl" value="<c:out value='${student.email}'/>" required></div>
            <div class="form-grp"><label>Enrolled Course</label><input type="text" name="course" class="form-ctrl" value="<c:out value='${student.course}'/>" required></div>
            <div style="display:flex;gap:0.5rem;margin-top:1rem;"><button type="submit" class="btn btn-pri">Save Changes</button>
            <a href="${pageContext.request.contextPath}/admin" class="btn btn-sec">Cancel</a></div>
        </form>
    </div></div>
</body></html>
