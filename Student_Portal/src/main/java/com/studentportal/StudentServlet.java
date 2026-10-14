package com.studentportal;
import jakarta.servlet.ServletException;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.IOException;
import java.sql.*;
import java.util.*;

@WebServlet(urlPatterns = {"/students", "/admin", "/student-details", "/student-edit", "/student-delete"})
public class StudentServlet extends HttpServlet {
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws ServletException, IOException {
        String p = req.getServletPath();
        try (Connection c = DBConnection.getConnection()) {
            if ("/admin".equals(p)) {
                String q = req.getParameter("search"); boolean hasQ = q != null && !q.isBlank();
                String sql = "SELECT s.id, s.name, s.roll_number, s.email, s.course, s.created_at, COUNT(f.id) FROM students s LEFT JOIN student_files f ON s.id=f.student_id " +
                             (hasQ ? "WHERE LOWER(s.name) LIKE ? OR LOWER(s.roll_number) LIKE ? OR LOWER(s.course) LIKE ? " : "") +
                             "GROUP BY s.id, s.name, s.roll_number, s.email, s.course, s.created_at ORDER BY s.id ASC";
                String t = hasQ ? "%" + q.trim().toLowerCase() + "%" : null;
                req.setAttribute("students", hasQ ? fetch(c, sql, t, t, t) : fetch(c, sql));
                req.setAttribute("totalStudents", count(c, "SELECT COUNT(*) FROM students"));
                req.setAttribute("studentsWithDocs", count(c, "SELECT COUNT(DISTINCT student_id) FROM student_files"));
                req.setAttribute("searchKeyword", q != null ? q.trim() : "");
                req.getRequestDispatcher("/admin.jsp").forward(req, resp);
            } else if ("/student-details".equals(p)) {
                int id = Integer.parseInt(req.getParameter("id"));
                HttpSession s = req.getSession(false);
                if ("Student".equals(s.getAttribute("role")) && !Objects.equals(s.getAttribute("studentId"), id)) {
                    req.setAttribute("errorMessage", "Access denied: you can only view your own profile.");
                } else {
                    Student st = findStudent(c, id);
                    if (st != null) {
                        try (PreparedStatement ps = c.prepareStatement("SELECT id, student_id, original_name, stored_name, file_path, file_type, upload_date FROM student_files WHERE student_id=? ORDER BY upload_date DESC")) {
                            ps.setInt(1, id);
                            try (ResultSet rs = ps.executeQuery()) {
                                while (rs.next()) st.files.add(new StudentFile(rs.getInt(1), rs.getInt(2), rs.getString(3), rs.getString(4), rs.getString(5), rs.getString(6), rs.getTimestamp(7)));
                            }
                        }
                        st.fileCount = st.files.size(); req.setAttribute("student", st); req.setAttribute("files", st.files);
                    } else req.setAttribute("errorMessage", "Student not found.");
                }
                req.getRequestDispatcher("/student-details.jsp").forward(req, resp);
            } else if ("/student-edit".equals(p)) {
                req.setAttribute("student", findStudent(c, Integer.parseInt(req.getParameter("id"))));
                req.getRequestDispatcher("/student-edit.jsp").forward(req, resp);
            } else {
                Integer sid = (Integer) req.getSession().getAttribute("studentId");
                String q = req.getParameter("search"); boolean hasQ = q != null && !q.isBlank();
                String sql = "SELECT s.id, s.name, s.roll_number, s.email, s.course, s.created_at, COUNT(f.id) FROM students s LEFT JOIN student_files f ON s.id=f.student_id WHERE s.id=? " +
                             (hasQ ? "AND (LOWER(s.name) LIKE ? OR LOWER(s.roll_number) LIKE ? OR LOWER(s.course) LIKE ?) " : "") +
                             "GROUP BY s.id, s.name, s.roll_number, s.email, s.course, s.created_at";
                String t = hasQ ? "%" + q.trim().toLowerCase() + "%" : null;
                req.setAttribute("students", sid != null ? (hasQ ? fetch(c, sql, sid, t, t, t) : fetch(c, sql, sid)) : Collections.emptyList());
                req.setAttribute("totalStudents", count(c, "SELECT COUNT(*) FROM students"));
                req.setAttribute("searchKeyword", q != null ? q.trim() : "");
                req.getRequestDispatcher("/students.jsp").forward(req, resp);
            }
        } catch (Exception e) { req.setAttribute("dbError", e.getMessage()); req.getRequestDispatcher("/admin".equals(p) ? "/admin.jsp" : "/students.jsp").forward(req, resp); }
    }

    protected void doPost(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        String p = req.getServletPath();
        try (Connection c = DBConnection.getConnection()) {
            if ("/student-delete".equals(p)) {
                try (PreparedStatement st = c.prepareStatement("DELETE FROM students WHERE id=?")) {
                    st.setInt(1, Integer.parseInt(req.getParameter("id"))); st.executeUpdate();
                }
            } else if ("/student-edit".equals(p)) {
                try (PreparedStatement st = c.prepareStatement("UPDATE students SET name=?, roll_number=?, email=?, course=? WHERE id=?")) {
                    st.setString(1, req.getParameter("name")); st.setString(2, req.getParameter("rollNumber"));
                    st.setString(3, req.getParameter("email")); st.setString(4, req.getParameter("course"));
                    st.setInt(5, Integer.parseInt(req.getParameter("id"))); st.executeUpdate();
                }
            }
        } catch (Exception e) { e.printStackTrace(); }
        resp.sendRedirect(req.getContextPath() + "/admin");
    }

    private Student findStudent(Connection c, int id) throws SQLException {
        try (PreparedStatement st = c.prepareStatement("SELECT id, name, roll_number, email, course, created_at FROM students WHERE id=?")) {
            st.setInt(1, id);
            try (ResultSet rs = st.executeQuery()) {
                return rs.next() ? new Student(rs.getInt(1), rs.getString(2), rs.getString(3), rs.getString(4), rs.getString(5), rs.getTimestamp(6)) : null;
            }
        }
    }

    private List<Student> fetch(Connection c, String sql, Object... p) throws SQLException {
        List<Student> list = new ArrayList<>();
        try (PreparedStatement st = c.prepareStatement(sql)) {
            for (int i = 0; i < p.length; i++) st.setObject(i + 1, p[i]);
            try (ResultSet rs = st.executeQuery()) {
                while (rs.next()) {
                    Student s = new Student(rs.getInt(1), rs.getString(2), rs.getString(3), rs.getString(4), rs.getString(5), rs.getTimestamp(6));
                    s.fileCount = rs.getInt(7); list.add(s);
                }
            }
        }
        return list;
    }

    private int count(Connection c, String sql) {
        try (Statement st = c.createStatement(); ResultSet rs = st.executeQuery(sql)) { return rs.next() ? rs.getInt(1) : 0; }
        catch (SQLException e) { return 0; }
    }
}
