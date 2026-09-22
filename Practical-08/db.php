<?php
/**
 * Practical 8: Secure Database Connection Provider (PDO Singleton)
 * Intermediate Extension: PDO Connection with Prepared Statements & UTF-8
 */

class Database {
    private static ?PDO $instance = null;

    // Default configuration (compatible with XAMPP / WAMP)
    private static string $host = '127.0.0.1';
    private static string $db   = 'studenthub_db';
    private static string $user = 'root';
    private static string $pass = '';
    private static string $port = '3306';
    private static string $charset = 'utf8mb4';

    /**
     * Private constructor prevents direct object instantiation
     */
    private function __construct() {}

    /**
     * Retrieve shared PDO instance
     */
    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                self::$host,
                self::$port,
                self::$db,
                self::$charset
            );

            $options = [
                // Throw PDOExceptions on errors
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Return associative arrays by default
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Disable emulated prepared statements to enforce native server-side prepared statements
                PDO::ATTR_EMULATE_PREPARES   => false,
                // Persistent connection for high-throughput
                PDO::ATTR_PERSISTENT         => false,
                // Ensure MySQL connection collation matches utf8mb4
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                // Log technical error securely and present sanitized message
                error_log("Database Connection Failure: " . $e->getMessage());
                die(json_encode([
                    'status' => 'error',
                    'message' => 'Database connection failed. Please ensure MySQL is running in XAMPP.',
                    'technical' => $e->getMessage()
                ]));
            }
        }

        return self::$instance;
    }
}
