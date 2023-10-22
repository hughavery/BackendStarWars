<?php
// Include your database connection code
require '../Database/login.php';
// Function to authenticate and login a user
function loginUser($email, $password) {
    try {
        // Establish a database connection
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);

        // Prepare and execute the SQL query to retrieve user information by email
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the user data
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the provided password against the hashed password in the database
            if (password_verify($password, $user['password'])) {
                // Password is correct
                // Close the database connection
                $db = null;

                // Return a success response
                header('Content-Type: application/json');
                echo json_encode(array("message" => "Login successful"));
            } else {
                // Password is incorrect
                header("HTTP/1.1 401 Unauthorized");
                echo json_encode(array("error" => "Incorrect password"));
            }
        } else {
            // User not found
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(array("error" => "User not found"));
        }
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
    if (isset($postData['email'], $postData['password'])) {
        // Call the loginUser function with the provided data
        loginUser($postData['email'], $postData['password']);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("error" => "Missing required fields"));
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>
