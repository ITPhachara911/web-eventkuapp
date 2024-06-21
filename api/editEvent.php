<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Assuming you have an event ID coming from somewhere, like $_POST['event_id']
    $event_id = isset($_POST['event_id']) ? $_POST['event_id'] : '';

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $Bding = isset($_POST['Bding']) ? $_POST['Bding'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $time_period = isset($_POST['time_period']) ? $_POST['time_period'] : '';
    $personnel = isset($_POST['personnel']) ? $_POST['personnel'] : '';
    $mainOrg = isset($_POST['mainOrg']) ? $_POST['mainOrg'] : '';
    $subOrg = isset($_POST['subOrg']) ? $_POST['subOrg'] : '';

    // Validate inputs
    if (empty($event_id) || empty($name) || empty($detail) || empty($Bding) || empty($room) || empty($time_period) || empty($personnel) || empty($mainOrg) || empty($subOrg)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Handle image file upload if a file is provided
    $imgPath = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $img = $_FILES['img'];
        $imgPath = 'uploaded_files/' . basename($img['name']);
        if (!move_uploaded_file($img['tmp_name'], $imgPath)) {
            http_response_code(500);
            $response = array('status' => 'error', 'message' => 'Failed to save image');
            echo json_encode($response);
            exit;
        }
    }

    // Parse time period to get start_time and end_time
    list($start_time, $end_time) = parseTimePeriod($time_period);

    // Prepare SQL query with optional image update
    $sql = "UPDATE events SET 
            name = ?, 
            start_date = ?, 
            end_date = ?, 
            description = ?, 
            room_id = ?, 
            location_id = ?, 
            faculty_id = ?, 
            branch_id = ?, 
            personnel_id = ?";
    $params = [$name, $start_time, $end_time, $detail, $room, $Bding, $mainOrg, $subOrg, $personnel];
    
    if ($imgPath) {
        $sql .= ", image_path = ?";
        $params[] = $imgPath;
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $event_id;

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $stmt->bind_param(str_repeat('s', count($params) - 1) . 'i', ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Data updated successfully');
            http_response_code(200);
        } else {
            $response = array('status' => 'error', 'message' => 'No event found or data not changed');
            http_response_code(404);
        }
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => 'Server Error');
        http_response_code(500);
        echo json_encode($response);
    }

    $stmt->close();
} else {
    // Invalid request method, return an error response
    http_response_code(405); // 405 Method Not Allowed
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}

function parseTimePeriod($time_period) {
    // Example parsing function, adjust according to your time_period format
    $parts = explode(' - ', $time_period);
    $start_time = $parts[0];
    $end_time = $parts[1];
    return array($start_time, $end_time);
}
?>
