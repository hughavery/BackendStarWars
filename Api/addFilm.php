<?php
// Include your database connection code
require '../Database/login.php';
function filmTitleExists($title) {
    $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM film WHERE film_title = :title");
    $stmt->bindParam(':title', $title);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'] > 0; // If count is greater than 0, the title exists
}

// Function to check if a film with the given episode number exists
function filmEpisodeExists($episodeNumber) {
    $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM film WHERE film_episode_id = :episodeNumber");
    $stmt->bindParam(':episodeNumber', $episodeNumber);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['count'] > 0; // If count is greater than 0, the episode number exists
}

function getHighestFilmID()
{
    $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
    $stmt = $db->prepare("SELECT MAX(filmID) AS highestFilmID FROM film");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['highestFilmID'];
}
function addFilm($film_title, $film_episode_id, $film_opening_crawl, $film_director, $film_release_date, $image_url) {
    try {
        if (filmTitleExists($film_title) || filmEpisodeExists($film_episode_id)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(array("error" => "Film with the given title or episode number already exists"));
            return;
        }

        $filmID = getHighestFilmID() + 1;
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);
        $stmt1 = $db->prepare("INSERT INTO film (filmID, film_title, film_episode_id, film_opening_crawl, film_director, film_release_date, image_url) VALUES (:filmID,:film_title, :film_episode_id, :film_opening_crawl, :film_director, :film_release_date, :image_url)");
        $stmt1->bindParam(':filmID', $filmID);
        $stmt1->bindParam(':film_title', $film_title);
        $stmt1->bindParam(':film_episode_id', $film_episode_id);
        $stmt1->bindParam(':film_opening_crawl', $film_opening_crawl);
        $stmt1->bindParam(':film_director', $film_director);
        $stmt1->bindParam(':film_release_date', $film_release_date);
        $stmt1->bindParam(':image_url', $image_url);
        $stmt1->execute();

        header('Content-Type: application/json');
        echo json_encode(array("message" => "everything is ok"));
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
    if (isset($postData['filmTitle'], $postData['episodeNumber'], $postData['openingScript'], $postData['director'], $postData['releaseDate'], $postData['imageUrl'])) {
        // Call the addFilm function with the provided data and additional parameters
        addFilm(
            $postData['filmTitle'],
            $postData['episodeNumber'],
            $postData['openingScript'],
            $postData['director'],
            $postData['releaseDate'],
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
