<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %><%@ taglib prefix="fmt" uri="jakarta.tags.fmt" %>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Details — KPRIET</title><link rel="stylesheet" href="${pageContext.request.contextPath}/style.css"></head>
<body>
    <nav class="navbar">
        <a href="${pageContext.request.contextPath}/${sessionScope.role eq 'Admin' ? 'admin' : 'students'}" class="brand">🎓 KPRIET <span>Portal</span></a>
        <div class="nav-links">
            <a href="${pageContext.request.contextPath}/${sessionScope.role eq 'Admin' ? 'admin' : 'students'}" class="btn btn-sec btn-sm">← Back</a>
            <a href="${pageContext.request.contextPath}/logout" class="btn btn-sec btn-sm">Logout</a>
        </div>
    </nav>
    <div class="container">
        <c:choose>
            <c:when test="${not empty errorMessage or empty student}">
                <div class="card" style="text-align:center;padding:2rem;">
                    <div class="alert alert-error"><c:out value="${errorMessage}"/></div>
                    <a href="${pageContext.request.contextPath}/${sessionScope.role eq 'Admin' ? 'admin' : 'students'}" class="btn btn-pri">Return to Dashboard</a>
                </div>
            </c:when>
            <c:otherwise>
                <div class="card">
                    <div class="card-head"><div>Student Profile<c:if test="${not empty param.sno}"> <span style="font-size:0.9rem;color:var(--muted);">(#${param.sno})</span></c:if>: <span style="color:var(--pri)"><c:out value="${student.name}"/></span></div><span class="badge-roll"><c:out value="${student.rollNumber}"/></span></div>
                    <div class="detail-grid">
                        <div class="detail-box"><small><c:out value="${not empty param.sno ? 'Serial Number' : 'Database ID'}"/></small>#<c:out value="${not empty param.sno ? param.sno : student.id}"/><c:if test="${not empty param.sno}"><span style="font-size:0.75rem;color:var(--muted);display:block;">(Database ID: #${student.id})</span></c:if></div>
                        <div class="detail-box"><small>Email Address</small><c:out value="${student.email}"/></div>
                        <div class="detail-box"><small>Enrolled Course</small><c:out value="${student.course}"/></div>
                        <div class="detail-box"><small>Admission Date</small><fmt:formatDate value="${student.createdAt}" pattern="dd MMM yyyy, hh:mm a"/></div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-head">Attached Documents (<c:out value="${files.size()}"/> ${files.size() eq 1 ? 'file' : 'files'})</div>
                    <c:choose>
                        <c:when test="${empty files}"><p style="color:var(--muted);padding:1rem 0;">No documents uploaded for this student.</p></c:when>
                        <c:otherwise>
                            <c:forEach items="${files}" var="f">
                                <div class="file-item">
                                    <div><strong><c:out value="${f.originalName}"/></strong>
                                    <div style="font-size:0.75rem;color:var(--muted);">Type: <code><c:out value="${f.fileType}"/></code> &bull; Uploaded: <fmt:formatDate value="${f.uploadDate}" pattern="dd MMM yyyy, hh:mm a"/></div></div>
                                    <div style="display:flex;gap:0.4rem;">
                                        <a href="${pageContext.request.contextPath}/file?id=${f.id}&action=view" target="_blank" class="btn btn-sec btn-sm">Preview ↗</a>
                                        <a href="${pageContext.request.contextPath}/file?id=${f.id}&action=download" class="btn btn-pri btn-sm">Download ⬇</a>
                                    </div>
                                    <c:if test="${f.image}"><div style="width:100%;"><img src="${pageContext.request.contextPath}/file?id=${f.id}&action=view" class="file-preview" alt="Preview"></div></c:if>
                                </div>
                            </c:forEach>
                        </c:otherwise>
                    </c:choose>
                </div>
            </c:otherwise>
        </c:choose>
    </div>
</body></html>