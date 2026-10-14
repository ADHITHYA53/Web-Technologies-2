<%@ page language="java" contentType="text/html; charset=UTF-8" pageEncoding="UTF-8"%>
<%@ taglib prefix="c" uri="jakarta.tags.core" %>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal — Login</title>
    <link rel="stylesheet" href="${pageContext.request.contextPath}/style.css">
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="login-head">
                <h1>🎓 KPRIET <span>Student Portal</span></h1>
            </div>
            <c:if test="${not empty errorMessage}"><div class="alert alert-error"><c:out value="${errorMessage}"/></div></c:if>
            <c:if test="${param.error eq 'unauthorized'}"><div class="alert alert-error">Please log in to access this portal page.</div></c:if>
            <c:if test="${param.logout eq '1'}"><div class="alert alert-success">You have been logged out successfully.</div></c:if>
            <form action="${pageContext.request.contextPath}/login" method="POST">
                <div class="form-grp"><label for="u">Username or Roll Number</label><input type="text" id="u" name="username" class="form-ctrl" placeholder="Enter Username" required autofocus></div>
                <div class="form-grp"><label for="p">Password</label><input type="password" id="p" name="password" class="form-ctrl" placeholder="Enter password" required></div>
                <button type="submit" class="btn btn-pri btn-block">Sign In</button>
            </form>
        </div>
    </div>
</body>
</html>
