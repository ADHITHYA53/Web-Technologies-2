package com.studentportal;
import java.sql.Timestamp;
import java.util.*;

public class Student {
    public int id, fileCount;
    public String name, rollNumber, email, course;
    public Timestamp createdAt;
    public List<StudentFile> files = new ArrayList<>();
    public Student(int id, String n, String r, String e, String c, Timestamp dt) {
        this.id = id; this.name = n; this.rollNumber = r; this.email = e; this.course = c; this.createdAt = dt;
    }
    public int getId() { return id; } public String getName() { return name; }
    public String getRollNumber() { return rollNumber; } public String getEmail() { return email; }
    public String getCourse() { return course; } public Timestamp getCreatedAt() { return createdAt; }
    public int getFileCount() { return fileCount; } public List<StudentFile> getFiles() { return files; }
}
