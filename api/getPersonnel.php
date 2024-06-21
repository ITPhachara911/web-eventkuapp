<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT `Personnel`.* FROM `Personnel` WHERE id = ?;");
        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $Personnel = $result->fetch_assoc();

            if ($Personnel) {
                http_response_code(200);
                echo json_encode($Personnel,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'location not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT `personnel`.*, `faculty`.`name` AS facultyName, `branch`.`name` AS branchName
        FROM `personnel` 
	    LEFT JOIN `faculty` ON `personnel`.`faculty_id` = `faculty`.`id` 
	    LEFT JOIN `branch` ON `branch`.`faculty_id` = `faculty`.`id`;";
        $result = $conn->query($sql);

        if ($result) {
            $Personnels = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode($Personnels);
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
