-- Practical 11: Seed SQL for Students Table (20+ Records)
CREATE DATABASE IF NOT EXISTS `studenthub_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub_db`;

CREATE TABLE IF NOT EXISTS `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    INDEX `idx_students_search` (`roll_number`, `full_name`, `email`),
    INDEX `idx_students_filter` (`department`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `students` (`id`, `roll_number`, `full_name`, `email`, `phone`, `department`, `semester`, `gpa`, `city`, `status`) VALUES
(1, '23IT042', 'Satyajit Parmar', '23it042@charusat.edu.in', '9876543210', 'Information Technology', 3, 9.65, 'Anand', 'active'),
(2, '23IT001', 'Aarav Sharma', '23it001@charusat.edu.in', '9811223344', 'Information Technology', 3, 9.42, 'Ahmedabad', 'active'),
(3, '23IT014', 'Diya Patel', '23it014@charusat.edu.in', '9822334455', 'Information Technology', 3, 8.85, 'Vadodara', 'active'),
(4, '23CE005', 'Ananya Shah', '23ce005@charusat.edu.in', '9833445566', 'Computer Engineering', 3, 9.50, 'Ahmedabad', 'active'),
(5, '23CSE03', 'Pooja Varma', '23cse03@charusat.edu.in', '9844556677', 'Computer Science & Engineering', 3, 9.80, 'Surat', 'active'),
(6, '23IT028', 'Kavya Joshi', '23it028@charusat.edu.in', '9855667788', 'Information Technology', 3, 9.10, 'Surat', 'active'),
(7, '23IT055', 'Rohan Mehta', '23it055@charusat.edu.in', '9866778899', 'Information Technology', 3, 8.30, 'Rajkot', 'active'),
(8, '23CE022', 'Harsh Trivedi', '23ce022@charusat.edu.in', '9877889900', 'Computer Engineering', 3, 8.75, 'Bhavnagar', 'active'),
(9, '23CE039', 'Meera Bhatt', '23ce039@charusat.edu.in', '9888990011', 'Computer Engineering', 3, 9.20, 'Vadodara', 'active'),
(10, '23CE060', 'Neel Dave', '23ce060@charusat.edu.in', '9899001122', 'Computer Engineering', 3, 7.95, 'Gandhinagar', 'active'),
(11, '23CSE18', 'Rishi Desai', '23cse18@charusat.edu.in', '9711223344', 'Computer Science & Engineering', 3, 8.90, 'Navsari', 'active'),
(12, '23CSE35', 'Tanvi Solanki', '23cse35@charusat.edu.in', '9722334455', 'Computer Science & Engineering', 3, 8.45, 'Anand', 'active'),
(13, '23CSE50', 'Viren Rao', '23cse50@charusat.edu.in', '9733445566', 'Computer Science & Engineering', 3, 9.05, 'Vadodara', 'active'),
(14, '22IT012', 'Siddharth Jani', '22it012@charusat.edu.in', '9744556677', 'Information Technology', 5, 8.60, 'Ahmedabad', 'active'),
(15, '22CE045', 'Isha Prajapati', '22ce045@charusat.edu.in', '9755667788', 'Computer Engineering', 5, 9.15, 'Nadiad', 'active'),
(16, '22CSE29', 'Dhruv Barot', '22cse29@charusat.edu.in', '9766778899', 'Computer Science & Engineering', 5, 8.25, 'Rajkot', 'active'),
(17, '22IT035', 'Priya Patel', '22it035@charusat.edu.in', '9777889900', 'Information Technology', 5, 9.35, 'Anand', 'active'),
(18, '22CE019', 'Karan Shah', '22ce019@charusat.edu.in', '9788990011', 'Computer Engineering', 5, 8.50, 'Surat', 'active'),
(19, '21IT008', 'Nikhil Soni', '21it008@charusat.edu.in', '9799001122', 'Information Technology', 7, 8.95, 'Vadodara', 'active'),
(20, '21CE052', 'Bhavya Patel', '21ce052@charusat.edu.in', '9611223344', 'Computer Engineering', 7, 9.10, 'Ahmedabad', 'active'),
(21, '23EC011', 'Rajesh Kulkarni', '23ec011@charusat.edu.in', '9622334455', 'Electronics & Communication', 3, 8.70, 'Anand', 'active'),
(22, '23EC025', 'Smriti Nair', '23ec025@charusat.edu.in', '9633445566', 'Electronics & Communication', 3, 9.25, 'Vadodara', 'active')
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);
