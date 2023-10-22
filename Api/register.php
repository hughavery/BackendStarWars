<?php
// Include your database connection code
require '../Database/login.php';

// Function to create a user
function createUser($username, $email, $password) {
    try {
        // Establish a database connection
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);

        // Create the users table if it doesn't exist
        $db->exec("CREATE TABLE IF NOT EXISTS users (
            email VARCHAR(255) PRIMARY KEY NOT NULL,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL
        )");

        // Hash the password (you should use a secure hashing algorithm)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and execute the SQL query to insert a new user
        $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->execute();

        // Close the database connection
        $db = null;

        // Return a success response
        header('Content-Type: application/json');
        echo json_encode(array("message" => "User created successfully"));
    } catch (PDOException $e) {
        // Handle any database errors
        header("HTTP/1.1 500 Internal Server Error");
        echo json_encode(array("error" => $e->getMessage()));
    }
}

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(202);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $postData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (isset($postData['username'], $postData['email'], $postData['password'])) {
        // Call the createUser function with the provided data
        createUser($postData['username'], $postData['email'], $postData['password']);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("error" => "Missing required fields"));
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>
