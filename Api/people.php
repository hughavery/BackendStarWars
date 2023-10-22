<?php
// Include your database connection code
require '../Database/login.php';

function getPeople() {
    try {
        // Establish a database connection
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);

        // Prepare and execute the SQL query
        if (isset($_GET['personId'])) {
            $id = $_GET['personId'];
            $stmt = $db->prepare("SELECT * FROM people WHERE peopleID = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            $stmt = $db->query("SELECT * FROM people");
        }

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
    getPeople();
} else {
    // Handle other HTTP methods if needed
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>
