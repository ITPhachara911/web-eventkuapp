<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the location ID from the request body
    $location_id = isset($_POST['location_id']) ? $_POST['location_id'] : '';

    // Simple validation example
    if (empty($location_id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Check if the location is referenced in other tables
    $referenceCheckStmt = $conn->prepare("SELECT COUNT(*) AS count FROM events WHERE location_id = ?");
    $referenceCheckStmt->bind_param("i", $location_id);
    $referenceCheckStmt->execute();
    $referenceCheckResult = $referenceCheckStmt->get_result();
    $referenceCheck = $referenceCheckResult->fetch_assoc();

    if ($referenceCheck['count'] > 0) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Location is referenced in other tables and cannot be deleted');
        echo json_encode($response);
        $referenceCheckStmt->close();
        exit;
    }

    $referenceCheckStmt->close();

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM location WHERE id = ?");
    $stmt->bind_param("i", $location_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Location deleted successfully');
            http_response_code(200);
        } else {
            $response = array('status' => 'error', 'message' => 'Location not found');
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

$conn->close();
?>
