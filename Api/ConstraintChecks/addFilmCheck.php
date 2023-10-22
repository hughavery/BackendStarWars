<?php
require '../../Database/login.php'; // Include your database connection code

// Function to check if a film with the given title exists
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
?>
