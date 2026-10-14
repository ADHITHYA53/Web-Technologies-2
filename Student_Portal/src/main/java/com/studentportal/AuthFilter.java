package com.studentportal;
import jakarta.servlet.*;
import jakarta.servlet.annotation.WebFilter;
import jakarta.servlet.http.*;
import java.io.IOException;

@WebFilter("/*")
public class AuthFilter implements Filter {
    public void doFilter(ServletRequest q, ServletResponse r, FilterChain c) throws IOException, ServletException {
        HttpServletRequest req = (HttpServletRequest) q;
        HttpServletResponse resp = (HttpServletResponse) r;
        String uri = req.getRequestURI(), ctx = req.getContextPath();
        boolean pub = uri.endsWith("/login") || uri.endsWith("/login.jsp") || uri.endsWith("/logout") || uri.endsWith(".css");
        HttpSession s = req.getSession(false);
        String role = (s != null) ? (String) s.getAttribute("role") : null;

        if (uri.equals(ctx + "/") || uri.equals(ctx)) {
            resp.sendRedirect(ctx + ("Admin".equals(role) ? "/admin" : (role != null ? "/students" : "/login")));
        } else if (pub || role != null) {
            if ((uri.contains("/admin") || uri.contains("/student-edit") || uri.contains("/student-delete")) && !"Admin".equals(role)) {
                resp.sendRedirect(ctx + "/students?error=forbidden");
            } else {
                if (!pub) resp.setHeader("Cache-Control", "no-cache, no-store, must-revalidate");
                c.doFilter(q, r);
            }
        } else resp.sendRedirect(ctx + "/login?error=unauthorized");
    }
}
