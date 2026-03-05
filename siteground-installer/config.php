<?php
/**
 * CARBON[6] Install Code System - Configuration
 * For SiteGround Hosting
 */

// Admin password - CHANGE THIS!
define('ADMIN_PASSWORD', 'your-secure-password-here');

// Installer URL
define('INSTALLER_URL', 'https://vltrnone.github.io/CARBON6-council-system/');

// Database file (SQLite - no MySQL needed)
define('DB_FILE', __DIR__ . '/codes.db');

// Code settings
define('CODE_LENGTH', 8);
define('DEFAULT_EXPIRY_HOURS', 24);

// Initialize database
function initDatabase() {
    $db = new PDO('sqlite:' . DB_FILE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if not exists
    $db->exec("CREATE TABLE IF NOT EXISTS install_codes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        code TEXT UNIQUE NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        expires_at DATETIME NOT NULL,
        purpose TEXT,
        used BOOLEAN DEFAULT 0,
        used_at DATETIME,
        user_ip TEXT,
        user_agent TEXT
    )");

    return $db;
}

// Generate unique code
function generateCode() {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $code = '';
    for ($i = 0; $i < CODE_LENGTH; $i++) {
        $code .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $code;
}

// Clean expired codes
function cleanExpiredCodes() {
    $db = initDatabase();
    $db->exec("DELETE FROM install_codes WHERE expires_at < datetime('now')");
}
