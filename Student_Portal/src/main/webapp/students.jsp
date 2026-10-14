<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Dashboard — KPRIET</title><link rel="stylesheet" href="${pageContext.request.contextPath}/style.css"></head>
<body>
    <nav class="navbar">
        <a href="${pageContext.request.contextPath}/students" class="brand">🎓 KPRIET <span>Student Portal</span></a>
        <div class="nav-links"><span>👤 <c:out value="${sessionScope.user}"/> (<strong>Student</strong>)</span>
        <a href="${pageContext.request.contextPath}/logout" class="btn btn-sec btn-sm">Logout</a></div>
    </nav>
    <div class="container">
        <c:if test="${param.error eq 'forbidden'}"><div class="alert alert-error">Access denied: Admin privileges required.</div></c:if>
        <c:if test="${not empty dbError}"><div class="alert alert-error"><c:out value="${dbError}"/></div></c:if>
        <div class="card">
            <div class="card-head">
                <div>Student Directory <span class="badge-cnt" style="margin-left:0.5rem;"><c:out value="${totalStudents}"/> Total Enrolled</span></div>
                <form action="${pageContext.request.contextPath}/students" method="GET" class="search-form">
                    <input type="text" name="search" class="form-ctrl" placeholder="Search my record..." value="<c:out value='${searchKeyword}'/>">
                    <button type="submit" class="btn btn-pri btn-sm">Search</button>
                    <c:if test="${not empty searchKeyword}"><a href="${pageContext.request.contextPath}/students" class="btn btn-sec btn-sm">Reset</a></c:if>
                </form>
            </div>
            <c:choose>
                <c:when test="${empty students}"><p style="text-align:center;padding:2rem;color:var(--muted);">No student record found.</p></c:when>
                <c:otherwise>
                    <div class="tbl-wrap"><table class="tbl">
                        <thead><tr><th>#</th><th>Roll Number</th><th>Name</th><th>Email</th><th>Course</th><th>Files</th><th>Action</th></tr></thead>
                        <tbody><c:forEach items="${students}" var="s" varStatus="vs"><tr>
                            <td>${vs.count}</td><td><span class="badge-roll"><c:out value="${s.rollNumber}"/></span></td>
                            <td><strong><c:out value="${s.name}"/></strong></td><td><c:out value="${s.email}"/></td><td><c:out value="${s.course}"/></td>
                            <td><span class="badge-cnt"><c:out value="${s.fileCount}"/> ${s.fileCount eq 1 ? 'file' : 'files'}</span></td>
                            <td><a href="${pageContext.request.contextPath}/student-details?id=${s.id}&sno=${vs.count}" class="btn btn-sec btn-sm">View Details →</a></td>
                        </tr></c:forEach></tbody>
                    </table></div>
                </c:otherwise>
            </c:choose>
        </div>
    </div>
</body></html>
