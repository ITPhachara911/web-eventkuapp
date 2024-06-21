<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT `events`.*, `branch`.`name` AS branch_name , `faculty`.`name` AS faculty_name, `location`.`name` AS location_name, `personnel`.`first_name` AS personnel_fname, `personnel`.`last_name` AS  personnel_lname, `personnel`.`position` AS personnel_position, `rooms`.`name` AS room_name, `rooms`.`room_number`
        FROM `events` 
	    LEFT JOIN `branch` ON `events`.`branch_id` = `branch`.`id` 
	    LEFT JOIN `faculty` ON `branch`.`faculty_id` = `faculty`.`id` 
	    LEFT JOIN `location` ON `events`.`location_id` = `location`.`id` 
	    LEFT JOIN `personnel` ON `events`.`personnel_id` = `personnel`.`id` 
	    LEFT JOIN `rooms` ON `events`.`room_id` = `rooms`.`id`
        WHERE events.id = ?;");

        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $event = $result->fetch_assoc();

            if ($event) {
                http_response_code(200);
                echo json_encode($event,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Event not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT `events`.*, `location`.`name` AS location_name
                FROM `events` 
	            LEFT JOIN `location` ON `events`.`location_id` = `location`.`id`;";
        $result = $conn->query($sql);

        if ($result) {
            $events = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode($events);
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
