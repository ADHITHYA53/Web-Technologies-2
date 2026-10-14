package com.studentportal;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;
import java.sql.*;

@WebServlet("/login")
public class LoginServlet extends HttpServlet {
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        HttpSession s = req.getSession(false);
        if (s != null && s.getAttribute("user") != null) {
            resp.sendRedirect(req.getContextPath() + ("Admin".equals(s.getAttribute("role")) ? "/admin" : "/students"));
            return;
        }
        req.getRequestDispatcher("/login.jsp").forward(req, resp);
    }

    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String u = req.getParameter("username"), p = req.getParameter("password");
        String name = null, role = null; Integer sid = null;
        if ("admin".equalsIgnoreCase(u != null ? u.trim() : "") && "admin123".equals(p)) {
            name = "Administrator"; role = "Admin";
        } else if (u != null && p != null && !u.isBlank() && !p.isBlank()) {
            try (Connection c = DBConnection.getConnection();
                 PreparedStatement st = c.prepareStatement("SELECT id, name FROM students WHERE UPPER(roll_number)=UPPER(?) AND (LOWER(email)=LOWER(?) OR UPPER(roll_number)=UPPER(?))")) {
                st.setString(1, u.trim()); st.setString(2, p.trim()); st.setString(3, p.trim());
                try (ResultSet rs = st.executeQuery()) { if (rs.next()) { sid = rs.getInt(1); name = rs.getString(2); role = "Student"; } }
            } catch (SQLException e) { req.setAttribute("errorMessage", "DB Error: " + e.getMessage()); }
        }

        if (name != null) {
            HttpSession old = req.getSession(false);
            if (old != null) old.invalidate();
            HttpSession s = req.getSession(true);
            s.setAttribute("user", name); s.setAttribute("role", role); s.setAttribute("studentId", sid);
            resp.sendRedirect(req.getContextPath() + ("Admin".equals(role) ? "/admin" : "/students"));
        } else {
            req.setAttribute("errorMessage", req.getAttribute("errorMessage") != null ? req.getAttribute("errorMessage") : "Invalid credentials.");
            req.getRequestDispatcher("/login.jsp").forward(req, resp);
        }
    }
}
