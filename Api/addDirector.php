<?php
// Include your database connection code
require '../Database/login.php';

function getHighestProducerID() {
    $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
    $stmt = $db->prepare("SELECT MAX(producerID) AS highestProducerID FROM producer");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['highestProducerID'];
}

function addDirector($directorName, $imageUrl) {
    try {
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
        $producerID = getHighestProducerID() + 1;
        $stmt = $db->prepare("INSERT INTO producer (producerID, producer_name, image_url) VALUES (:producerID, :directorName, :imageUrl)");
        $stmt->bindParam(':directorName', $directorName);
        $stmt->bindParam(':imageUrl', $imageUrl);
        $stmt->bindParam(':producerID', $producerID);

        $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode(array("message" => "Director added successfully"));
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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(202);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the POST data
    $postData = json_decode(file_get_contents('php://input'), true);

    // Check if the required fields are present
    if (isset($postData['directorName'], $postData['imageUrl'])) {
        // Call the addDirector function with the provided data
        addDirector(
            $postData['directorName'],
            $postData['imageUrl']
        );
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(array("error" => "Missing required fields"));
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>
