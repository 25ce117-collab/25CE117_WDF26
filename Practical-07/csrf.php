<?php
/**
 * Practical 7: CSRF (Cross-Site Request Forgery) Protection Module
 * Advanced Extension: Cryptographic Token Generation & Validation
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate or retrieve existing CSRF token
 */
function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from submitted POST data against session
 */
function verifyCsrfToken(?string $submittedToken): bool {
    if (empty($_SESSION['csrf_token']) || empty($submittedToken)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $submittedToken);
}
