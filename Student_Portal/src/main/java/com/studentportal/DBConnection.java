package com.studentportal;
import java.io.File;
import java.sql.*;

public class DBConnection {
    static { try { Class.forName("com.mysql.cj.jdbc.Driver"); } catch (Exception e) {} }
    public static Connection getConnection() throws SQLException {
        return DriverManager.getConnection("jdbc:mysql://localhost:3306/student_admission?useSSL=false&allowPublicKeyRetrieval=true&serverTimezone=UTC", "root", "");
    }
    public static File getUploadDir() {
        return new File(System.getProperty("studentportal.upload.dir", "D:/Program_Files_3/xampp/htdocs/Q2_Student_Admin/uploads"));
    }
}
