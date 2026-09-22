-- ============================================================================
-- Practical 8: StudentHub Relational Database Schema (3NF Normalized)
-- Course: ITUE203 Web Development Frameworks
-- Charotar University of Science and Technology (CHARUSAT)
-- ============================================================================

CREATE DATABASE IF NOT EXISTS `studenthub_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `studenthub_db`;

-- Drop tables in reverse order of foreign key dependency
DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `auth_tokens`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- Table 1: USERS (Core Authentication & RBAC)
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
-- Table 2: STUDENTS (Extended Student Profiles)
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
    CONSTRAINT `fk_students_user_id` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_students_department` (`department`),
    INDEX `idx_students_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 3: EVENTS (Campus Events, Workshops & Hackathons)
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
    INDEX `idx_events_date` (`event_date`),
    INDEX `idx_events_status` (`status`),
    INDEX `idx_events_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 4: REGISTRATIONS (Student Event Enrolment & Attendance)
-- ----------------------------------------------------------------------------
CREATE TABLE `registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `student_id` INT UNSIGNED NOT NULL,
    `event_id` INT UNSIGNED NOT NULL,
    `ticket_code` VARCHAR(40) NOT NULL UNIQUE,
    `attendance_status` ENUM('registered', 'attended', 'absent', 'cancelled') NOT NULL DEFAULT 'registered',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reg_student` 
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_reg_event` 
        FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `uk_student_event` UNIQUE (`student_id`, `event_id`),
    INDEX `idx_reg_ticket` (`ticket_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 5: AUTH_TOKENS (Secure Remember-Me SHA-256 Tokens)
-- ----------------------------------------------------------------------------
CREATE TABLE `auth_tokens` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `token_hash` VARCHAR(64) NOT NULL UNIQUE,
    `expires_at` DATETIME NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tokens_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX `idx_tokens_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Table 6: AUDIT_LOGS (System Activity & Compliance Trail)
-- ----------------------------------------------------------------------------
CREATE TABLE `audit_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` TEXT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` 
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX `idx_audit_action` (`action`),
    INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
