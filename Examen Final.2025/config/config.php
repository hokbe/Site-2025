<?php
if (!defined('DB_HOST')) {
    define('DB_HOST', getenv('DB_HOST') ?: 'db.3wa.io');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', getenv('DB_NAME') ?: 'chokbengbouneric_Final');
}
if (!defined('DB_USER')) {
    define('DB_USER', getenv('DB_USER') ?: 'chokbengbouneric');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', getenv('DB_PASS') ?: '807095bdc113fa97c1ee59cc137fd29e');
}

define('SECURE_SESSION', true);
define('SESSION_LIFETIME', 3600);
?>
