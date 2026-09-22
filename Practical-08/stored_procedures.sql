-- ============================================================================
-- Practical 8: Stored Procedures, Views, and Triggers (Advanced Extension)
-- Course: ITUE203 Web Development Frameworks
-- ============================================================================

USE `studenthub_db`;

DELIMITER $$

-- ----------------------------------------------------------------------------
-- 1. STORED PROCEDURE: sp_register_student_event
-- Encapsulates atomic registration with capacity check and ticket generation
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_register_student_event`$$

CREATE PROCEDURE `sp_register_student_event`(
    IN  p_student_id   INT UNSIGNED,
    IN  p_event_id     INT UNSIGNED,
    OUT p_status_code  VARCHAR(30),
    OUT p_ticket_code  VARCHAR(40)
)
proc_label: BEGIN
    DECLARE v_capacity INT UNSIGNED;
    DECLARE v_enrolled INT UNSIGNED;
    DECLARE v_already  INT UNSIGNED;
    DECLARE v_ticket   VARCHAR(40);

    -- 1. Check if student already registered
    SELECT COUNT(*) INTO v_already 
    FROM `registrations` 
    WHERE `student_id` = p_student_id AND `event_id` = p_event_id;

    IF v_already > 0 THEN
        SET p_status_code = 'ALREADY_REGISTERED';
        SET p_ticket_code = NULL;
        LEAVE proc_label;
    END IF;

    -- 2. Check event capacity and status
    SELECT `max_capacity` INTO v_capacity 
    FROM `events` 
    WHERE `id` = p_event_id AND `status` = 'upcoming';

    IF v_capacity IS NULL THEN
        SET p_status_code = 'EVENT_NOT_AVAILABLE';
        SET p_ticket_code = NULL;
        LEAVE proc_label;
    END IF;

    SELECT COUNT(*) INTO v_enrolled 
    FROM `registrations` 
    WHERE `event_id` = p_event_id AND `attendance_status` != 'cancelled';

    IF v_enrolled >= v_capacity THEN
        SET p_status_code = 'CAPACITY_REACHED';
        SET p_ticket_code = NULL;
        LEAVE proc_label;
    END IF;

    -- 3. Generate unique ticket code and perform insertion
    SET v_ticket = CONCAT('TKT-', LPAD(p_event_id, 3, '0'), '-', UPPER(SUBSTRING(MD5(RAND()), 1, 8)));

    INSERT INTO `registrations` (`student_id`, `event_id`, `ticket_code`, `attendance_status`)
    VALUES (p_student_id, p_event_id, v_ticket, 'registered');

    SET p_status_code = 'SUCCESS';
    SET p_ticket_code = v_ticket;
END proc_label$$

-- ----------------------------------------------------------------------------
-- 2. STORED PROCEDURE: sp_get_event_attendees
-- Retrieves student roster for a specific event
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_event_attendees`$$

CREATE PROCEDURE `sp_get_event_attendees`(
    IN p_event_id INT UNSIGNED
)
BEGIN
    SELECT 
        r.`id` AS registration_id,
        r.`ticket_code`,
        r.`registered_at`,
        r.`attendance_status`,
        s.`roll_number`,
        s.`full_name`,
        s.`email`,
        s.`phone`,
        s.`department`
    FROM `registrations` r
    INNER JOIN `students` s ON r.`student_id` = s.`id`
    WHERE r.`event_id` = p_event_id
    ORDER BY r.`registered_at` DESC;
END$$

-- ----------------------------------------------------------------------------
-- 3. STORED PROCEDURE: sp_get_dashboard_metrics
-- Aggregates vital system KPI statistics
-- ----------------------------------------------------------------------------
DROP PROCEDURE IF EXISTS `sp_get_dashboard_metrics`$$

CREATE PROCEDURE `sp_get_dashboard_metrics`(
    OUT p_total_students      INT,
    OUT p_total_events        INT,
    OUT p_total_registrations INT,
    OUT p_active_users        INT
)
BEGIN
    SELECT COUNT(*) INTO p_total_students FROM `students` WHERE `status` = 'active';
    SELECT COUNT(*) INTO p_total_events FROM `events` WHERE `status` != 'cancelled';
    SELECT COUNT(*) INTO p_total_registrations FROM `registrations`;
    SELECT COUNT(*) INTO p_active_users FROM `users` WHERE `status` = 'active';
END$$

-- ----------------------------------------------------------------------------
-- 4. DATABASE VIEW: v_active_registrations
-- ----------------------------------------------------------------------------
DROP VIEW IF EXISTS `v_active_registrations`$$

CREATE VIEW `v_active_registrations` AS
SELECT 
    r.`id` AS registration_id,
    r.`ticket_code`,
    r.`attendance_status`,
    r.`registered_at`,
    s.`roll_number`,
    s.`full_name` AS student_name,
    s.`email` AS student_email,
    s.`department` AS student_dept,
    e.`title` AS event_title,
    e.`category` AS event_category,
    e.`event_date`,
    e.`venue`
FROM `registrations` r
INNER JOIN `students` s ON r.`student_id` = s.`id`
INNER JOIN `events` e ON r.`event_id` = e.`id`$$

DELIMITER ;
