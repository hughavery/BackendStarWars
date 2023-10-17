<?php
// Get the absolute path to the directory where this script is located
$scriptDirectory = dirname(__FILE__);

// Define the absolute path to your database file
$databasePath = $scriptDirectory . '/star_wars.db';

define("CONNECTION_STRING", "sqlite:" . $databasePath);
define("CONNECTION_USER", "");
define("CONNECTION_PASSWORD", "");
define("CONNECTION_OPTIONS", [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
?>
