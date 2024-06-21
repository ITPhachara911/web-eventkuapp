<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
    $building_number = isset($_POST['building_number']) ? $_POST['building_number'] : '';

    // Validate inputs
    if (empty($id) || empty($name) || empty($latitude) || empty($longitude) || empty($building_number)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($_POST['name']);
        exit;
    }

    // Prepare SQL query with optional image update
    $sql = "UPDATE location SET 
            name = ?, 
            latitude = ?, 
            longitude = ?, 
            building_number = ?
            WHERE id = ?";
    $params = [$name, $latitude, $longitude, $building_number, $id];

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $stmt->bind_param("ssssi", ...$params);

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
?>
