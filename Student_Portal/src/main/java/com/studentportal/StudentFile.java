package com.studentportal;
import java.sql.Timestamp;

public class StudentFile {
    public int id, studentId;
    public String originalName, storedName, filePath, fileType;
    public Timestamp uploadDate;
    public StudentFile(int id, int sid, String orig, String st, String p, String t, Timestamp dt) {
        this.id = id; this.studentId = sid; this.originalName = orig; this.storedName = st; this.filePath = p; this.fileType = t; this.uploadDate = dt;
    }
    public int getId() { return id; } public int getStudentId() { return studentId; }
    public String getOriginalName() { return originalName; } public String getFileType() { return fileType; }
    public Timestamp getUploadDate() { return uploadDate; } public boolean isImage() { return fileType != null && fileType.startsWith("image/"); }
}
