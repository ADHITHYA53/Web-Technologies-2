# KPRIET Student Admission Portal (Q3)

A Java Web Application developed using **Java Servlets (Jakarta EE 10)**, **JSP (JavaServer Pages)**, **JDBC**, and **MySQL**. This application completes **Question 3** of the Web Technology assignment by integrating directly with the existing **Question 2 (PHP)** database and upload storage.

---

## 1. Project Purpose & Features

- **Centralized MySQL Integration**: Retrieves student profiles and file metadata from the existing `student_admission` MySQL database without duplicating data or creating secondary schemas.
- **Role-Aware Authentication & Session Management**: Restricts student lists and document access to authenticated sessions via Jakarta `HttpSession` and `AuthFilter`.
- **Dynamic Student Directory**: JSP page utilizing Jakarta JSTL and Expression Language (EL) to render responsive student tables, metrics, search filters, and profile badges.
- **Comprehensive Student Details**: Detailed profile view showcasing student records and associated documents uploaded during admission.
- **Secure File Streaming (`FileServlet`)**:
  - Serves files directly from the Q2 PHP upload storage (`D:\Program_Files_3\xampp\htdocs\Q2_Student_Admin\uploads`).
  - Verifies student-file ownership against MySQL.
  - Path traversal protection (canonical path boundary checking).
- **Responsive Q2-Matching Theme**: Tailored college portal theme using Primary Orange (`#FF6600`), Warm Background (`#FFF9F5`), Subtle Orange Accent (`#FFF3E8`), Border (`#FFD2B5`), White (`#FFFFFF`), and Navy Text (`#172B45`).

---

## 2. Required Software & Environment

| Component | Verified Specification |
| :--- | :--- |
| **Operating System** | Windows 11 (64-bit) |
| **Java Development Kit** | Eclipse Temurin JDK 21.0.12 (`C:\Program Files\Eclipse Adoptium\jdk-21.0.12.101-hotspot`) |
| **Servlet Container** | Apache Tomcat 10.1.61 (`C:\Tomcat\apache-tomcat-10.1.61-windows-x64\apache-tomcat-10.1.61`) |
| **Build Tool** | Apache Maven 3.10.0 (`C:\Maven\apache-maven-3.10.0-bin\apache-maven-3.10.0`) |
| **Database Server** | MySQL (XAMPP), Port 3306 |
| **IDE** | Visual Studio Code |

---

## 3. Database Configuration

The application connects to the pre-existing Q2 MySQL database:

- **Database Name**: `student_admission`
- **Host / Port**: `localhost:3306`
- **Username**: `root`
- **Password**: *(empty by default)*
- **Driver**: `com.mysql.cj.jdbc.Driver` (MySQL Connector/J 9.4.0)

### Existing Database Tables

1. **`students`**
   - `id` (INT, Primary Key, Auto Increment)
   - `name` (VARCHAR(100))
   - `roll_number` (VARCHAR(20), Unique)
   - `email` (VARCHAR(100))
   - `course` (VARCHAR(100))
   - `created_at` (TIMESTAMP)

2. **`student_files`**
   - `id` (INT, Primary Key, Auto Increment)
   - `student_id` (INT, Foreign Key referencing `students.id`)
   - `original_name` (VARCHAR(255))
   - `stored_name` (VARCHAR(255), Unique)
   - `file_path` (VARCHAR(500))
   - `file_type` (VARCHAR(100))
   - `upload_date` (TIMESTAMP)

> [!NOTE]
> Database configuration is centralized in `com.studentportal.DBConnection`. You can override credentials without changing code by setting the environment variables `DB_URL`, `DB_USER`, or `DB_PASSWORD`.

---

## 4. Shared Upload Storage Configuration

The application shares uploaded files directly with the Q2 PHP project located at:

`D:\Program_Files_3\xampp\htdocs\Q2_Student_Admin\uploads`

In `DBConnection.java`, `getUploadDirectory()` resolves this path. It can also be overridden via the system property `studentportal.upload.dir` or environment variable `UPLOAD_DIR`.

---

## 5. Project Architecture

The application adopts the classical **Model-View-Controller (MVC)** design pattern for Java web applications:

```
Student_Portal/
├── pom.xml                                   # Maven project descriptor (Jakarta EE 10, MySQL, JSTL)
├── src/main/java/com/studentportal/
│   ├── DBConnection.java                    # Centralized JDBC connection & file path manager
│   ├── Student.java                         # Model POJO for Student entity
│   ├── StudentFile.java                     # Model POJO for StudentFile entity
│   ├── AuthFilter.java                      # Security filter protecting routes and preventing back-button caching
│   ├── LoginServlet.java                    # Handles authentication, session setup, and demo logic
│   ├── LogoutServlet.java                   # Invalidates HttpSession and cleans up security context
│   ├── StudentServlet.java                  # Controller for listing students, searching, and viewing details
│   └── FileServlet.java                     # Secure file streaming controller with path-traversal protection
├── src/main/webapp/
│   ├── WEB-INF/
│   │   └── web.xml                          # Servlet 6.0 deployment descriptor
│   ├── login.jsp                            # Login interface with demo credentials guide
│   ├── students.jsp                         # Student directory with search, KPIs, and responsive table
│   ├── student-details.jsp                  # Detailed profile with attached document previews and download links
│   └── style.css                            # Responsive custom styling (Q2 Light Orange & White theme)
└── README.md                                # Project documentation
```

---

## 6. How to Build and Deploy

Open **Windows PowerShell** in the project directory (`d:\WT_Assignment _1_24CS008\Student_Portal`):

### Step 1: Set Environment Variables
```powershell
$env:JAVA_HOME = "C:\Program Files\Eclipse Adoptium\jdk-21.0.12.101-hotspot"
$env:CATALINA_HOME = "C:\Tomcat\apache-tomcat-10.1.61-windows-x64\apache-tomcat-10.1.61"
$env:Path = "$env:JAVA_HOME\bin;C:\Maven\apache-maven-3.10.0-bin\apache-maven-3.10.0\bin;" + $env:Path
```

### Step 2: Build the WAR File
```powershell
mvn clean package
```
This produces `target/Student_Portal.war` packaged with MySQL Connector/J and Jakarta JSTL 3.0.

### Step 3: Deploy to Tomcat
```powershell
Copy-Item ".\target\Student_Portal.war" -Destination "$env:CATALINA_HOME\webapps\Student_Portal.war" -Force
```

### Step 4: Start Apache Tomcat
```powershell
& "$env:CATALINA_HOME\bin\startup.bat"
```
*(Or to run with live terminal output: `& "$env:CATALINA_HOME\bin\catalina.bat" run`)*

### Step 5: Stop Apache Tomcat
```powershell
& "$env:CATALINA_HOME\bin\shutdown.bat"
```

---

## 7. How to Access and Log In

### Application URL
Open your web browser and navigate to:
[http://localhost:8080/Student_Portal/](http://localhost:8080/Student_Portal/)

### Authentication Credentials
The shared Q2 `student_admission` database does not contain a dedicated user or authentication table. To satisfy the assignment requirements safely and transparently without altering the shared database schema, the application supports two login mechanisms:

1. **Administrator Demo Login**:
   - **Username**: `admin`
   - **Password**: `admin123`
   *(Role: Administrator, full access to student records and files)*

2. **Registered Student Direct Login**:
   - **Username**: Student Roll Number (e.g., `24CS008` or `24CE008`)
   - **Password**: Student Roll Number or registered Email
   *(Role: Student, verified directly against the live `students` MySQL table)*

> [!IMPORTANT]
> **Academic Demonstration Notice**: Demo authentication is implemented exclusively for evaluation of this college assignment. In a production environment, a dedicated table with Argon2/BCrypt salted password hashes and multi-factor authentication (MFA) would be required.

---

## 8. How Each Required Feature Was Tested & Verified

All tests were performed against the live Tomcat 10.1 and XAMPP MySQL instances:

1. **Authentication & Session Protection (`AuthFilter`)**:
   - Attempting to access protected URLs directly (e.g. `/Student_Portal/students` or `/Student_Portal/file?id=28`) without an active session returned **HTTP 302** redirecting to `/Student_Portal/login?error=unauthorized`.
   - Direct access to static assets (`/Student_Portal/style.css`) returned **HTTP 200** with `Content-Type: text/css`.

2. **Session Creation & Fixation Protection (`LoginServlet`)**:
   - Submitting valid credentials invalidated any prior session and generated a new authenticated `HttpSession` cookie (`STUDENT_PORTAL_JSESSIONID`).
   - Browser redirected to `/Student_Portal/students`.

3. **Dynamic Student Directory (`students.jsp`)**:
   - All 5 existing students from MySQL (`Alex Morgan`, `Priya Sharma`, `Karthik Raja`, `Adhi`, `Akash`) were retrieved through `StudentServlet` using JDBC prepared statements and rendered dynamically in a responsive table.
   - Search filter verified: querying `?search=Adhi` returned exactly 1 matching record.

4. **Student Details & Associated Files (`student-details.jsp`)**:
   - Navigating to `/student-details?id=23` retrieved Adhi's full profile and attached document (`24CS008 - suit.JPG`).
   - Image preview rendered inline using `<img src="/Student_Portal/file?id=45&action=view">`.
   - Invalid student ID handling: requesting non-existent ID `/student-details?id=9999` rendered a graceful error banner rather than a crash or stack trace.

5. **Secure File Streaming (`FileServlet`)**:
   - View mode: `/file?id=45&action=view` returned **HTTP 200** with `Content-Type: image/jpeg`, `Content-Disposition: inline; filename="24CS008 - suit.JPG"`, `X-Content-Type-Options: nosniff`.
   - Download mode: `/file?id=45&action=download` returned **HTTP 200** with `Content-Disposition: attachment; filename="24CS008 - suit.JPG"`.
   - Missing file ID: `/file?id=9999` returned **HTTP 404**.
   - Malformed file ID: `/file?id=invalid_id` returned **HTTP 400**.
   - Path traversal prevention: verified canonical path enforcement restricts access strictly to the shared Q2 `uploads/` directory.

6. **Logout & Session Invalidation (`LogoutServlet`)**:
   - Invoking `/logout` cleared session attributes and invalidated the session.
   - Redirected to `/login?logout=1`.
   - Subsequent requests with the expired session cookie were rejected and redirected to `/login?error=unauthorized`.
