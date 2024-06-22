<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $room_number = isset($_POST['room_number']) ? $_POST['room_number'] : '';
    $location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';

    // Simple validation example
    if (empty($name) || empty($room_number) || empty($location_id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query to insert a new room
    $sql = "INSERT INTO rooms (name, room_number, location_id) VALUES (?, ?, ?)";

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $stmt->bind_param("ssi", $name, $room_number, $location_id);

    // Execute SQL query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Room created successfully');
            http_response_code(201); // 201 Created
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to create room');
            http_response_code(500);
        }
        echo json_encode($response);
    } else {
        $response = array('status' => 'error', 'message' => 'Server Error');
        http_response_code(500);
        echo json_encode($response);
    }

    // Close statement
    $stmt->close();
} else {
    // Invalid request method, return an error response
    http_response_code(405); // 405 Method Not Allowed
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}

// Close connection
$conn->close();
?>
