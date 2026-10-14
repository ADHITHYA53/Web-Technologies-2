package com.studentportal;
import jakarta.servlet.annotation.WebServlet;
import jakarta.servlet.http.*;
import java.io.*;
import java.sql.*;

@WebServlet("/file")
public class FileServlet extends HttpServlet {
    protected void doGet(HttpServletRequest req, HttpServletResponse resp) throws IOException {
        HttpSession s = req.getSession(false);
        if (s == null || s.getAttribute("user") == null) { resp.sendError(401); return; }
        try {
            int fid = Integer.parseInt(req.getParameter("id"));
            String orig = null, stored = null, type = null; int sid = 0;
            try (Connection c = DBConnection.getConnection();
                 PreparedStatement st = c.prepareStatement("SELECT sf.student_id, sf.original_name, sf.stored_name, sf.file_type FROM student_files sf WHERE sf.id=?")) {
                st.setInt(1, fid);
                try (ResultSet rs = st.executeQuery()) {
                    if (rs.next()) { sid = rs.getInt(1); orig = rs.getString(2); stored = rs.getString(3); type = rs.getString(4); }
                }
            }
            if (stored == null) { resp.sendError(404); return; }
            if ("Student".equals(s.getAttribute("role"))) {
                Integer loggedSid = (Integer) s.getAttribute("studentId");
                if (loggedSid == null || loggedSid != sid) { resp.sendError(403); return; }
            }
            File dir = DBConnection.getUploadDir(), f = new File(dir, new File(stored).getName());
            if (!f.getCanonicalPath().startsWith(dir.getCanonicalPath() + File.separator) || !f.exists()) {
                resp.sendError(404); return;
            }
            resp.setContentType(type != null ? type : "application/octet-stream");
            resp.setHeader("X-Content-Type-Options", "nosniff");
            String disp = "download".equalsIgnoreCase(req.getParameter("action")) ? "attachment" : "inline";
            resp.setHeader("Content-Disposition", disp + "; filename=\"" + orig.replace("\"", "") + "\"");
            resp.setContentLengthLong(f.length());
            try (InputStream in = new BufferedInputStream(new FileInputStream(f)); OutputStream out = resp.getOutputStream()) {
                byte[] b = new byte[8192]; int n;
                while ((n = in.read(b)) != -1) out.write(b, 0, n);
            }
        } catch (Exception e) { resp.sendError(400); }
    }
}
