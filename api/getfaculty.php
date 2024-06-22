<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT `faculty`.* FROM `faculty` WHERE id = ?;");
        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $faculty = $result->fetch_assoc();

            if ($faculty) {
                http_response_code(200);
                echo json_encode($faculty,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'faculty not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT `faculty`.*
        FROM `faculty`";
        $result = $conn->query($sql);

        if ($result) {
            $facultys = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode($facultys);
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
