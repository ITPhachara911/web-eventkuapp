<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['locaion'])) {
        $location_id = $_GET['locaion'];
        $stmt = $conn->prepare("SELECT `rooms`.* FROM `rooms` WHERE location_id = ?;");
        $stmt->bind_param("i", $location_id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $rooms = $result->fetch_all(MYSQLI_ASSOC);

            if ($rooms) {
                http_response_code(200);
                echo json_encode($rooms,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'room not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    }
    else if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT `rooms`.* FROM `rooms` WHERE id = ?;");
        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $room = $result->fetch_assoc();

            if ($room) {
                http_response_code(200);
                echo json_encode($room,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'room not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT `rooms`.*,`location`.`name` as location_name FROM `rooms` left join location on rooms.location_id = location.id;";
        $result = $conn->query($sql);

        if ($result) {
            $rooms = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode($rooms);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
    }
} else {
    // Invalid request method, return an error response
    http_response_code(405);  // 405 Method Not Allowed
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}

$conn->close();
