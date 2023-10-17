<?php
// Include your database connection code
require '../Database/login.php';




function getFilms() {
    try {
        // Establish a database connection
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);

        // Prepare and execute the SQL query
        $stmt = $db->query("SELECT * FROM film ORDER BY film_episode_id");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Close the database connection
        $db = null;

        // Return the result as JSON
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        echo json_encode($result);
    } catch (PDOException $e) {
        // Handle any database errors
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// Define the HTTP request method and route
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    getFilms();
} else {
    // Handle other HTTP methods if needed
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>