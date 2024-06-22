<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';

    // Simple validation example
    if (empty($id) || empty($name) || empty($faculty_id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query to update branch
    $sql = "UPDATE branch SET name = ?, faculty_id = ? WHERE id = ?";

    // Prepare parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $name, $faculty_id, $id);

    // Execute SQL query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Branch updated successfully');
            http_response_code(200);
        } else {
            $response = array('status' => 'error', 'message' => 'Branch not found or data not changed');
            http_response_code(404);
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
