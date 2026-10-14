<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — KPRIET</title><link rel="stylesheet" href="${pageContext.request.contextPath}/style.css"></head>
<body>
    <nav class="navbar">
        <a href="${pageContext.request.contextPath}/admin" class="brand">🎓 KPRIET <span>Admin Portal</span></a>
        <div class="nav-links"><span>👤 <c:out value="${sessionScope.user}"/> (<strong>Admin</strong>)</span>
        <a href="${pageContext.request.contextPath}/logout" class="btn btn-sec btn-sm">Logout</a></div>
    </nav>
    <div class="container">
        <c:if test="${param.msg eq 'updated'}"><div class="alert alert-success">Student record updated successfully.</div></c:if>
        <c:if test="${param.msg eq 'deleted'}"><div class="alert alert-success">Student record deleted successfully.</div></c:if>
        <c:if test="${not empty dbError}"><div class="alert alert-error"><c:out value="${dbError}"/></div></c:if>
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-lbl">Total Students</div><div class="stat-val"><c:out value="${totalStudents}"/></div></div>
            <div class="stat-card"><div class="stat-lbl">Students With Documents</div><div class="stat-val"><c:out value="${studentsWithDocs}"/></div></div>
        </div>
        <div class="card">
            <div class="card-head">
                <div>All Student Records (<c:out value="${students.size()}"/>)</div>
                <form action="${pageContext.request.contextPath}/admin" method="GET" class="search-form">
                    <input type="text" name="search" class="form-ctrl" placeholder="Search name, roll, course..." value="<c:out value='${searchKeyword}'/>">
                    <button type="submit" class="btn btn-pri btn-sm">Search</button>
                    <c:if test="${not empty searchKeyword}"><a href="${pageContext.request.contextPath}/admin" class="btn btn-sec btn-sm">Reset</a></c:if>
                </form>
            </div>
            <c:choose>
                <c:when test="${empty students}"><p style="text-align:center;padding:2rem;color:var(--muted);">No student records found.</p></c:when>
                <c:otherwise>
                    <div class="tbl-wrap"><table class="tbl">
                        <thead><tr><th>#</th><th>Roll Number</th><th>Name</th><th>Email</th><th>Course</th><th>Files</th><th>Actions</th></tr></thead>
                        <tbody><c:forEach items="${students}" var="s" varStatus="vs"><tr>
                            <td>${vs.count}</td><td><span class="badge-roll"><c:out value="${s.rollNumber}"/></span></td>
                            <td><strong><c:out value="${s.name}"/></strong></td><td><c:out value="${s.email}"/></td><td><c:out value="${s.course}"/></td>
                            <td><span class="badge-cnt"><c:out value="${s.fileCount}"/> ${s.fileCount eq 1 ? 'file' : 'files'}</span></td>
                            <td>
                                <a href="${pageContext.request.contextPath}/student-details?id=${s.id}&sno=${vs.count}" class="btn btn-sec btn-sm">View</a>
                                <a href="${pageContext.request.contextPath}/student-edit?id=${s.id}" class="btn btn-sec btn-sm">Edit</a>
                                <form action="${pageContext.request.contextPath}/student-delete" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="id" value="${s.id}"><button type="submit" class="btn btn-dan btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr></c:forEach></tbody>
                    </table></div>
                </c:otherwise>
            </c:choose>
        </div>
    </div>
</body></html>
