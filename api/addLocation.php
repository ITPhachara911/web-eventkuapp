<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : '';
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : '';
    $building_number = isset($_POST['building_number']) ? $_POST['building_number'] : '-';

    // Validate inputs
    if (empty($name) || empty($latitude) || empty($longitude) || empty($building_number)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query to insert a new location
    $sql = "INSERT INTO location (name, latitude, longitude, building_number) VALUES (?, ?, ?, ?)";
    $params = [$name, $latitude, $longitude, $building_number];

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare($sql);

    // Bind parameters dynamically
    $stmt->bind_param("ssss", ...$params);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Data created successfully');
            http_response_code(201); // 201 Created
        } else {
            $response = array('status' => 'error', 'message' => 'Failed to create data');
            http_response_code(500);
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
