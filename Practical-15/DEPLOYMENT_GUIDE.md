# StudentHub - Production & Localhost Deployment Guide
## Web Development Frameworks (ITUE203) - Practical 15
**Institution:** Faculty of Technology & Engineering (FTE), CHARUSAT  
**Target Environment:** Apache 2.4.x + PHP 8.1+ + MySQL 8.0+ / MariaDB (XAMPP / WAMP / LAMP)

---

## 🚀 1. Quick Localhost Deployment via XAMPP

### Step 1: Copy Project to Apache Document Root
Copy the `studenthub/` directory into your Apache `htdocs` directory:
```bash
# Windows XAMPP path
C:\xampp\htdocs\studenthub
```

### Step 2: Initialize Database
1. Launch **XAMPP Control Panel** and start both **Apache** and **MySQL**.
2. Open **phpMyAdmin** at `http://localhost/phpmyadmin/`.
3. Create a new database named:
   ```sql
   CREATE DATABASE `studenthub_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
4. Navigate to the **Import** tab and import the master SQL dump:
   [`sql/studenthub_complete.sql`](./sql/studenthub_complete.sql)
5. Verify that all 6 tables (`users`, `students`, `events`, `registrations`, `auth_tokens`, `audit_logs`) are created and seeded.

### Step 3: Verify Configuration
Inspect [`config/config.php`](./studenthub/config/config.php) to ensure database connection settings match your local environment:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'studenthub_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // default password in XAMPP is empty
define('DB_PORT', '3306');
```

### Step 4: Access Application in Web Browser
Open your browser and navigate to:
```text
http://localhost/studenthub/
```

---

## 🌐 2. Advanced Extension: Apache Virtual Host Configuration

To run StudentHub under a clean custom local domain (e.g. `http://studenthub.local`):

### Step A: Configure Apache Virtual Host
Open `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and append:
```apache
<VirtualHost *:80>
    ServerAdmin admin@charusat.edu.in
    DocumentRoot "C:/xampp/htdocs/studenthub"
    ServerName studenthub.local
    ServerAlias www.studenthub.local

    <Directory "C:/xampp/htdocs/studenthub">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/studenthub-error.log"
    CustomLog "logs/studenthub-access.log" common
</VirtualHost>
```

### Step B: Map Local DNS in Hosts File
Open `C:\Windows\System32\drivers\etc\hosts` (as Administrator) and append:
```text
127.0.0.1    studenthub.local
```

### Step C: Restart Apache
Restart Apache in XAMPP and open `http://studenthub.local` in your browser.

---

## 🔑 3. Default Seed Credentials for Faculty Evaluation

| Role | Username / Identifier | Password | Dashboard Route |
|:---|:---|:---|:---|
| **Super Administrator** | `admin@charusat.edu.in` | `Admin@123` | `/admin_dashboard.php` |
| **Enrolled Student** | `23it042@charusat.edu.in` | `Student@123` | `/student_dashboard.php` |
| **Peer Student** | `23it001@charusat.edu.in` | `Student@123` | `/student_dashboard.php` |
