-- Practical 12: Events Schema & Seed Records
CREATE DATABASE IF NOT EXISTS `studenthub_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `studenthub_db`;

CREATE TABLE IF NOT EXISTS `events` (
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
    INDEX `idx_p12_events_category` (`category`),
    INDEX `idx_p12_events_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `events` (`id`, `title`, `category`, `department`, `description`, `event_date`, `event_time`, `venue`, `max_capacity`, `registration_fee`, `poster_path`, `status`) VALUES
(1, 'Charusat HackFest 2026', 'Hackathon', 'IT/CE', '36-hour flagship software hackathon with mentorship and awards.', '2026-11-14', '09:00:00', 'CSPIT Central Lab 3', 150, 0.00, 'uploads/posters/sample_hackfest.png', 'upcoming'),
(2, 'Full-Stack Web Architectures BootCamp', 'Workshop', 'IT', 'Hands-on training in PHP 8, MySQL PDO, and REST APIs.', '2026-11-20', '14:00:00', 'Seminar Hall A', 60, 150.00, 'uploads/posters/sample_web.png', 'upcoming'),
(3, 'Deep Learning & Generative AI Workshop', 'Workshop', 'CSE', 'Exploring transformer models, prompt engineering, and diffusion.', '2026-11-25', '10:00:00', 'DEPSTAR Lab 1', 50, 200.00, 'uploads/posters/sample_genai.png', 'upcoming'),
(4, 'Cybersecurity & Ethical Hacking Symposium', 'Symposium', 'CE', 'Interactive live exploits, red teaming, and defensive strategies.', '2026-11-29', '11:00:00', 'Auditorium FTE', 250, 0.00, 'uploads/posters/sample_cyber.png', 'upcoming'),
(5, 'Spoural 2026 - Classical Arts Audition', 'Cultural', 'All', 'Annual university auditions for performing arts and music.', '2026-12-02', '16:00:00', 'Open Amphitheatre', 200, 0.00, 'uploads/posters/sample_spoural.png', 'upcoming'),
(6, 'Robotics & Drone Flight Demonstration', 'Technical', 'ME/EC', 'Live demonstration of automated quadcopters and robotic rovers.', '2026-12-08', '15:30:00', 'Cricket Ground Arena', 300, 0.00, NULL, 'completed')
ON DUPLICATE KEY UPDATE title = VALUES(title);
