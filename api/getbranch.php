<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
     if (isset($_GET['Facultyid'])) {
        $id = $_GET['Facultyid'];

        $stmt = $conn->prepare("SELECT `branch`.* FROM `branch` WHERE faculty_id = ?;");
        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $branchs = $result->fetch_all(MYSQLI_ASSOC);

            if ($branchs) {
                http_response_code(200);
                echo json_encode($branchs,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'branch not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    }
    else if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $stmt = $conn->prepare("SELECT `branch`.* FROM `branch`  WHERE branch.id = ?;");
        $stmt->bind_param("i", $id);  
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $branch = $result->fetch_assoc();

            if ($branch) {
                http_response_code(200);
                echo json_encode($branch,);
            } else {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'branch not found']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        }
        
        $stmt->close();
    } else {
        $sql = "SELECT `branch`.*, faculty.name as faculty_name
        FROM `branch`left join faculty on branch.faculty_id = faculty.id";
        $result = $conn->query($sql);

        if ($result) {
            $branchs = $result->fetch_all(MYSQLI_ASSOC);

            http_response_code(200);
            echo json_encode($branchs);
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
