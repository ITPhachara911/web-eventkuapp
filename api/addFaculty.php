<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $name = isset($_POST['name']) ? $_POST['name'] : '';

    // Simple validation example
    if (empty($name)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query to insert a new faculty
    $sql = "INSERT INTO faculty (name) VALUES (?)";

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    // Execute SQL query
    if ($stmt->execute()) {
        $response = array('status' => 'success', 'message' => 'Faculty added successfully');
        http_response_code(201); // 201 Created
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
