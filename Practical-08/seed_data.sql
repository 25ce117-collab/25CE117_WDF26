-- ============================================================================
-- Practical 8: Seed Data for StudentHub Database
-- Default Passwords for testing:
--   admin@charusat.edu.in   => Admin@123
--   student accounts        => Student@123
-- ============================================================================

USE `studenthub_db`;

-- ----------------------------------------------------------------------------
-- 1. SEED USERS
-- Password hash for 'Admin@123': $2y$10$8sKzZt9uWd1/3jL87q2iOuU1zF.rY0XyUjW7B61xL7jVz3G.E9W6u (or standard bcrypt)
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `status`, `last_login`) VALUES
(1, 'superadmin', 'admin@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'admin', 'active', '2026-09-22 09:30:00'),
(2, 'satya_parmar', '23it042@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'student', 'active', '2026-09-22 10:15:00'),
(3, 'aarav_sharma', '23it001@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'student', 'active', '2026-09-21 16:45:00'),
(4, 'diya_patel', '23it014@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'student', 'active', '2026-09-20 14:10:00'),
(5, 'ananya_shah', '23ce005@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'student', 'active', '2026-09-21 11:20:00'),
(6, 'pooja_varma', '23cse03@charusat.edu.in', '$2y$10$Z3k38G7Zz4eX0/3qOq4mU.l0j8tX4aG5k1eQ5rZ6w7Y8u9i0o1p2a', 'student', 'active', '2026-09-22 08:50:00');

-- ----------------------------------------------------------------------------
-- 2. SEED STUDENTS (Profiles)
-- ----------------------------------------------------------------------------
INSERT INTO `students` (`id`, `user_id`, `roll_number`, `full_name`, `email`, `phone`, `department`, `semester`, `gpa`, `city`, `status`) VALUES
(1, 2, '23IT042', 'Satyajit Parmar', '23it042@charusat.edu.in', '9876543210', 'Information Technology', 3, 9.65, 'Anand', 'active'),
(2, 3, '23IT001', 'Aarav Sharma', '23it001@charusat.edu.in', '9811223344', 'Information Technology', 3, 9.42, 'Ahmedabad', 'active'),
(3, 4, '23IT014', 'Diya Patel', '23it014@charusat.edu.in', '9822334455', 'Information Technology', 3, 8.85, 'Vadodara', 'active'),
(4, 5, '23CE005', 'Ananya Shah', '23ce005@charusat.edu.in', '9833445566', 'Computer Engineering', 3, 9.50, 'Ahmedabad', 'active'),
(5, 6, '23CSE03', 'Pooja Varma', '23cse03@charusat.edu.in', '9844556677', 'Computer Science & Engineering', 3, 9.80, 'Surat', 'active'),
(6, NULL, '23IT028', 'Kavya Joshi', '23it028@charusat.edu.in', '9855667788', 'Information Technology', 3, 9.10, 'Surat', 'active'),
(7, NULL, '23IT055', 'Rohan Mehta', '23it055@charusat.edu.in', '9866778899', 'Information Technology', 3, 8.30, 'Rajkot', 'active'),
(8, NULL, '23CE022', 'Harsh Trivedi', '23ce022@charusat.edu.in', '9877889900', 'Computer Engineering', 3, 8.75, 'Bhavnagar', 'active'),
(9, NULL, '23CE039', 'Meera Bhatt', '23ce039@charusat.edu.in', '9888990011', 'Computer Engineering', 3, 9.20, 'Vadodara', 'active'),
(10, NULL, '23CE060', 'Neel Dave', '23ce060@charusat.edu.in', '9899001122', 'Computer Engineering', 3, 7.95, 'Gandhinagar', 'active'),
(11, NULL, '23CSE18', 'Rishi Desai', '23cse18@charusat.edu.in', '9711223344', 'Computer Science & Engineering', 3, 8.90, 'Navsari', 'active'),
(12, NULL, '23CSE35', 'Tanvi Solanki', '23cse35@charusat.edu.in', '9722334455', 'Computer Science & Engineering', 3, 8.45, 'Anand', 'active'),
(13, NULL, '23CSE50', 'Viren Rao', '23cse50@charusat.edu.in', '9733445566', 'Computer Science & Engineering', 3, 9.05, 'Vadodara', 'active'),
(14, NULL, '22IT012', 'Siddharth Jani', '22it012@charusat.edu.in', '9744556677', 'Information Technology', 5, 8.60, 'Ahmedabad', 'active'),
(15, NULL, '22CE045', 'Isha Prajapati', '22ce045@charusat.edu.in', '9755667788', 'Computer Engineering', 5, 9.15, 'Nadiad', 'active'),
(16, NULL, '22CSE29', 'Dhruv Barot', '22cse29@charusat.edu.in', '9766778899', 'Computer Science & Engineering', 5, 8.25, 'Rajkot', 'active'),
(17, NULL, '22IT035', 'Priya Patel', '22it035@charusat.edu.in', '9777889900', 'Information Technology', 5, 9.35, 'Anand', 'active'),
(18, NULL, '22CE019', 'Karan Shah', '22ce019@charusat.edu.in', '9788990011', 'Computer Engineering', 5, 8.50, 'Surat', 'active'),
(19, NULL, '21IT008', 'Nikhil Soni', '21it008@charusat.edu.in', '9799001122', 'Information Technology', 7, 8.95, 'Vadodara', 'active'),
(20, NULL, '21CE052', 'Bhavya Patel', '21ce052@charusat.edu.in', '9611223344', 'Computer Engineering', 7, 9.10, 'Ahmedabad', 'active');

-- ----------------------------------------------------------------------------
-- 3. SEED EVENTS
-- ----------------------------------------------------------------------------
INSERT INTO `events` (`id`, `title`, `category`, `department`, `description`, `event_date`, `event_time`, `venue`, `max_capacity`, `registration_fee`, `poster_path`, `status`) VALUES
(1, 'Charusat HackFest 2026', 'Hackathon', 'IT/CE', '36-hour flagship software hackathon with mentorship and awards.', '2026-11-14', '09:00:00', 'CSPIT Central Lab 3', 150, 0.00, 'uploads/posters/hackfest2026.png', 'upcoming'),
(2, 'Full-Stack Web Architectures BootCamp', 'Workshop', 'IT', 'Hands-on training in PHP 8, MySQL PDO, and REST APIs.', '2026-11-20', '14:00:00', 'Seminar Hall A', 60, 150.00, 'uploads/posters/web_bootcamp.png', 'upcoming'),
(3, 'Deep Learning & Generative AI Workshop', 'Workshop', 'CSE', 'Exploring transformer models, prompt engineering, and diffusion.', '2026-11-25', '10:00:00', 'DEPSTAR Lab 1', 50, 200.00, 'uploads/posters/genai.png', 'upcoming'),
(4, 'Cybersecurity & Ethical Hacking Symposium', 'Symposium', 'CE', 'Interactive live exploits, red teaming, and defensive strategies.', '2026-11-29', '11:00:00', 'Auditorium FTE', 250, 0.00, 'uploads/posters/cybersec.png', 'upcoming'),
(5, 'Spoural 2026 - Classical Arts Audition', 'Cultural', 'All', 'Annual university auditions for performing arts and music.', '2026-12-02', '16:00:00', 'Open Amphitheatre', 200, 0.00, 'uploads/posters/spoural.png', 'upcoming');

-- ----------------------------------------------------------------------------
-- 4. SEED REGISTRATIONS
-- ----------------------------------------------------------------------------
INSERT INTO `registrations` (`id`, `student_id`, `event_id`, `ticket_code`, `attendance_status`, `registered_at`) VALUES
(1, 1, 1, 'TKT-HF26-9A102F', 'registered', '2026-09-22 10:30:00'),
(2, 1, 2, 'TKT-WB26-8B203E', 'registered', '2026-09-22 10:35:00'),
(3, 2, 1, 'TKT-HF26-7C304D', 'registered', '2026-09-22 11:00:00'),
(4, 3, 2, 'TKT-WB26-6D405C', 'registered', '2026-09-22 11:15:00'),
(5, 4, 3, 'TKT-GA26-5E506B', 'registered', '2026-09-22 12:00:00'),
(6, 5, 4, 'TKT-CS26-4F607A', 'registered', '2026-09-22 12:45:00');

-- ----------------------------------------------------------------------------
-- 5. SEED AUDIT LOGS
-- ----------------------------------------------------------------------------
INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'DATABASE_INITIALIZATION', 'Schema and initial seed records established.', '127.0.0.1', '2026-09-22 09:00:00'),
(2, 1, 'EVENT_CREATED', 'Created new event: Charusat HackFest 2026', '127.0.0.1', '2026-09-22 09:15:00'),
(3, 2, 'USER_REGISTRATION', 'Student Satyajit Parmar enrolled in portal.', '192.168.1.42', '2026-09-22 10:15:00');
