<?php
/**
 * Practical 13: RESTful JSON API Endpoint for Events
 * Implements GET, POST, PUT, DELETE with strict HTTP status codes
 */

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/response.php';

// Enable CORS for local testing
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$pdo = Database::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

// Parse incoming JSON body for PUT, POST, or DELETE requests
$rawInput = file_get_contents('php://input');
$body = json_decode($rawInput, true) ?? $_POST;

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($body['id']) ? (int)$body['id'] : 0);

switch ($method) {
    // ------------------------------------------------------------------------
    // 1. GET: Retrieve all events or single event by ID
    // ------------------------------------------------------------------------
    case 'GET':
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $event = $stmt->fetch();

            if ($event) {
                ApiResponse::success($event, "Event #{$id} retrieved successfully.", 200);
            } else {
                ApiResponse::error("Event with ID {$id} not found.", 404);
            }
        } else {
            $search = trim($_GET['search'] ?? '');
            $category = trim($_GET['category'] ?? 'ALL');

            $where = [];
            $params = [];

            if ($search !== '') {
                $where[] = "(title LIKE :s1 OR venue LIKE :s2 OR description LIKE :s3)";
                $params[':s1'] = "%{$search}%";
                $params[':s2'] = "%{$search}%";
                $params[':s3'] = "%{$search}%";
            }

            if ($category !== '' && $category !== 'ALL') {
                $where[] = "category = :cat";
                $params[':cat'] = $category;
            }

            $whereSql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            $stmt = $pdo->prepare("SELECT * FROM events {$whereSql} ORDER BY event_date ASC");
            $stmt->execute($params);
            $events = $stmt->fetchAll();

            ApiResponse::success($events, "Retrieved " . count($events) . " events.", 200);
        }
        break;

    // ------------------------------------------------------------------------
    // 2. POST: Create a new event
    // ------------------------------------------------------------------------
    case 'POST':
        $title       = trim($body['title'] ?? '');
        $category    = trim($body['category'] ?? 'General');
        $department  = trim($body['department'] ?? 'All');
        $description = trim($body['description'] ?? '');
        $eventDate   = trim($body['event_date'] ?? date('Y-m-d'));
        $eventTime   = trim($body['event_time'] ?? '10:00:00');
        $venue       = trim($body['venue'] ?? '');
        $maxCapacity = (int)($body['max_capacity'] ?? 100);
        $fee         = (float)($body['registration_fee'] ?? 0.0);

        $errors = [];
        if (empty($title)) $errors[] = 'Event title is required.';
        if (empty($venue)) $errors[] = 'Event venue is required.';
        if (empty($eventDate)) $errors[] = 'Event date is required.';

        if (!empty($errors)) {
            ApiResponse::error('Validation failure on event creation.', 400, $errors);
        }

        try {
            $stmt = $pdo->prepare("
                INSERT INTO events (title, category, department, description, event_date, event_time, venue, max_capacity, registration_fee, status)
                VALUES (:title, :cat, :dept, :desc, :edate, :etime, :venue, :cap, :fee, 'upcoming')
            ");
            $stmt->execute([
                ':title' => $title,
                ':cat'   => $category,
                ':dept'  => $department,
                ':desc'  => $description,
                ':edate' => $eventDate,
                ':etime' => $eventTime,
                ':venue' => $venue,
                ':cap'   => $maxCapacity,
                ':fee'   => $fee
            ]);

            $newId = $pdo->lastInsertId();
            ApiResponse::success(['id' => $newId, 'title' => $title], "Event created successfully.", 201);
        } catch (PDOException $e) {
            ApiResponse::error('Database insertion error: ' . $e->getMessage(), 500);
        }
        break;

    // ------------------------------------------------------------------------
    // 3. PUT: Update an existing event
    // ------------------------------------------------------------------------
    case 'PUT':
        if ($id <= 0) {
            ApiResponse::error("A valid event ID parameter (?id=X) is required for updates.", 400);
        }

        $stmt = $pdo->prepare("SELECT id FROM events WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        if (!$stmt->fetch()) {
            ApiResponse::error("Event with ID {$id} not found.", 404);
        }

        $title       = trim($body['title'] ?? '');
        $category    = trim($body['category'] ?? '');
        $description = trim($body['description'] ?? '');
        $eventDate   = trim($body['event_date'] ?? '');
        $venue       = trim($body['venue'] ?? '');
        $maxCapacity = (int)($body['max_capacity'] ?? 100);
        $status      = trim($body['status'] ?? 'upcoming');

        if (empty($title) || empty($venue)) {
            ApiResponse::error("Title and venue cannot be empty.", 400);
        }

        try {
            $upd = $pdo->prepare("
                UPDATE events 
                SET title = :title, category = :cat, description = :desc, 
                    event_date = :edate, venue = :venue, max_capacity = :cap, status = :status
                WHERE id = :id
            ");
            $upd->execute([
                ':title'  => $title,
                ':cat'    => $category,
                ':desc'   => $description,
                ':edate'  => $eventDate,
                ':venue'  => $venue,
                ':cap'    => $maxCapacity,
                ':status' => $status,
                ':id'     => $id
            ]);

            ApiResponse::success(['id' => $id], "Event #{$id} updated successfully.", 200);
        } catch (PDOException $e) {
            ApiResponse::error("Database error during update: " . $e->getMessage(), 500);
        }
        break;

    // ------------------------------------------------------------------------
    // 4. DELETE: Remove an event
    // ------------------------------------------------------------------------
    case 'DELETE':
        if ($id <= 0) {
            ApiResponse::error("A valid event ID parameter is required for deletion.", 400);
        }

        $del = $pdo->prepare("DELETE FROM events WHERE id = :id");
        $del->execute([':id' => $id]);

        if ($del->rowCount() > 0) {
            ApiResponse::success(['id' => $id], "Event #{$id} deleted successfully.", 200);
        } else {
            ApiResponse::error("Event #{$id} not found or already deleted.", 404);
        }
        break;

    default:
        ApiResponse::error("HTTP Method '{$method}' is not supported.", 405);
        break;
}
