<?php
// Include your database connection code
require '../Database/login.php';




function getRelation() {
    try {



        // Establish a database connection
        $db = new PDO(CONNECTION_STRING, CONNECTION_USER, CONNECTION_PASSWORD, CONNECTION_OPTIONS);

        // Prepare and execute the SQL query
        if (isset($_GET['people'])) {
            $film = $_GET['people'];
            $stmt = $db->prepare("SELECT people.* FROM people INNER JOIN film_people ON people.peopleId = film_people.peopleId 
                WHERE film_people.filmId = :film");
            $stmt->bindParam(':film', $film, PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif (isset($_GET['producers'])) {
            $film = $_GET['producers'];

            $stmt = $db->prepare("SELECT producer.* FROM producer INNER JOIN film_producer ON producer.producerId = film_producer.producerId 
                WHERE film_producer.filmId = :film");

            $stmt->bindParam(':film', $film, PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif (isset($_GET['planets'])) {
            $film = $_GET['planets'];
            $stmt = $db->prepare("SELECT planet.* FROM planet INNER JOIN film_planet ON planet.planetId = film_planet.planetId 
                WHERE film_planet.filmId = :film");
            $stmt->bindParam(':film', $film, PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif (isset($_GET['vehicles'])) {
            $film = $_GET['vehicles'];
            $stmt = $db->prepare("SELECT vehicle.* FROM vehicle INNER JOIN film_vehicles ON vehicle.vehicleId = film_vehicles.vehicleId 
                WHERE film_vehicles.filmId = :film");
            $stmt->bindParam(':film', $film, PDO::PARAM_INT);
            $stmt->execute();
        }
        elseif (isset($_GET['starships'])) {
            $film = $_GET['starships'];
            $stmt = $db->prepare("SELECT starship.* FROM starship INNER JOIN film_starships ON starship.starshipId = film_starships.starshipId 
                WHERE film_starships.filmId = :film");
            $stmt->bindParam(':film', $film, PDO::PARAM_INT);
            $stmt->execute();
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
    getRelation();
} else {
    // Handle other HTTP methods if needed
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(array("error" => "Method not allowed"));
}
?>