-- ============================================================================
-- Practical 15: StudentHub Unified Master Database Dump
-- Subject: Web Development Frameworks (ITUE203)
-- Faculty of Technology & Engineering (FTE), CHARUSAT
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `studenthub_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `studenthub_db`;

-- Drop old tables if re-importing
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `auth_tokens`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- 1. USERS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `status` ENUM('active', 'inactive', 'suspended') NOT NULL DEFAULT 'active',
    `last_login` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. STUDENTS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL UNIQUE,
    `roll_number` VARCHAR(20) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone` VARCHAR(15) NOT NULL,
    `department` VARCHAR(80) NOT NULL,
    `semester` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `gpa` DECIMAL(3, 2) NOT NULL DEFAULT 0.00,
    `city` VARCHAR(50) NOT NULL DEFAULT 'Anand',
    `status` ENUM('active', 'inactive', 'deleted') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_p15_students_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    INDEX `idx_students_dept` (`department`),
    INDEX `idx_students_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. EVENTS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `department` VARCHAR(80) NOT NULL DEFAULT 'All',
    `description` TEXT NOT NULL,
    `event_date` DATE NOT NULL,
    `event_time` TIME NOT NULL,
    `venue` VARCHAR(100) NOT NULL,
    `max_capacity` INT UNSIGNED NOT NULL DEFAULT 100,
    `registration_fee` DECIMAL(7, 2) NOT NULL DEFAULT 0.00,
    `poster_path` VARCHAR(255) NULL,
    `status` ENUM('upcoming', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'upcoming',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_events_status` (`status`),
    INDEX `idx_events_cat` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. REGISTRATIONS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE `registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `event_id` INT UNSIGNED NOT NULL,
    `ticket_code` VARCHAR(40) NOT NULL UNIQUE,
    `attendance_status` ENUM('registered', 'attended', 'absent', 'cancelled') NOT NULL DEFAULT 'registered',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_p15_reg_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_p15_reg_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
    CONSTRAINT `uk_p15_student_event` UNIQUE (`student_id`, `event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. AUTH_TOKENS TABLE (Remember-Me Hashes)
-- ----------------------------------------------------------------------------
CREATE TABLE `auth_tokens` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_p15_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 6. AUDIT_LOGS TABLE
-- ----------------------------------------------------------------------------
CREATE TABLE `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_p15_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- INITIAL SEED DATA
-- Default Passwords:
-- Admin: admin@charusat.edu.in -> Admin@123
-- Student: 23it042@charusat.edu.in -> Student@123
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `last_login`) VALUES
(1, 'superadmin', 'admin@charusat.edu.in', '$2y$10$n7L60yMmsN44x0D9gH6e9uV8rY0XyUjW7B61xL7jVz3G.E9W6u512', 'admin', 'active', '2026-10-24 10:00:00'),
(2, 'satya_parmar', '23it042@charusat.edu.in', '$2y$10$n7L60yMmsN44x0D9gH6e9uV8rY0XyUjW7B61xL7jVz3G.E9W6u512', 'student', 'active', '2026-10-24 11:30:00'),
(3, 'aarav_sharma', '23it001@charusat.edu.in', '$2y$10$n7L60yMmsN44x0D9gH6e9uV8rY0XyUjW7B61xL7jVz3G.E9W6u512', 'student', 'active', '2026-10-23 15:45:00');

INSERT INTO `students` (`id`, `user_id`, `roll_number`, `full_name`, `email`, `phone`, `department`, `semester`, `gpa`, `city`, `status`) VALUES
(1, 2, '23IT042', 'Satyajit Parmar', '23it042@charusat.edu.in', '9876543210', 'Information Technology', 3, 9.65, 'Anand', 'active'),
(2, 3, '23IT001', 'Aarav Sharma', '23it001@charusat.edu.in', '9811223344', 'Information Technology', 3, 9.42, 'Ahmedabad', 'active'),
(3, NULL, '23IT014', 'Diya Patel', '23it014@charusat.edu.in', '9822334455', 'Information Technology', 3, 8.85, 'Vadodara', 'active'),
(4, NULL, '23CE005', 'Ananya Shah', '23ce005@charusat.edu.in', '9833445566', 'Computer Engineering', 3, 9.50, 'Ahmedabad', 'active'),
(5, NULL, '23CSE03', 'Pooja Varma', '23cse03@charusat.edu.in', '9844556677', 'Computer Science & Engineering', 3, 9.80, 'Surat', 'active');

INSERT INTO `events` (`id`, `title`, `category`, `department`, `description`, `event_date`, `event_time`, `venue`, `max_capacity`, `registration_fee`, `poster_path`, `status`) VALUES
(1, 'Charusat HackFest 2026', 'Hackathon', 'IT/CE', '36-hour flagship software hackathon with mentorship and cash awards.', '2026-11-14', '09:00:00', 'CSPIT Central Lab 3', 150, 0.00, 'uploads/posters/hackfest.png', 'upcoming'),
(2, 'Full-Stack Web Architectures BootCamp', 'Workshop', 'IT', 'Hands-on training in PHP 8, MySQL PDO, and REST APIs.', '2026-11-20', '14:00:00', 'Seminar Hall A', 60, 150.00, 'uploads/posters/web_bootcamp.png', 'upcoming'),
(3, 'Deep Learning & Generative AI Workshop', 'Workshop', 'CSE', 'Exploring transformer models, prompt engineering, and diffusion.', '2026-11-25', '10:00:00', 'DEPSTAR Lab 1', 50, 200.00, 'uploads/posters/genai.png', 'upcoming');

INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `ticket_code`, `attendance_status`, `registered_at`) VALUES
(1, 1, 1, 'TKT-HF26-9A102F', 'registered', '2026-10-24 11:35:00'),
(2, 1, 2, 'TKT-WB26-8B203E', 'registered', '2026-10-24 11:38:00'),
(3, 2, 1, 'TKT-HF26-7C304D', 'registered', '2026-10-24 12:00:00');

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'SYSTEM_DEPLOYMENT', 'Final StudentHub project integrated and deployed.', '127.0.0.1', '2026-10-24 09:00:00');
