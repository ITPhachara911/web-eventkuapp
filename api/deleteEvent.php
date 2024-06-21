<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the event ID from the request body
    $event_id = isset($_POST['event_id']) ? $_POST['event_id'] : '';

    // Simple validation example
    if (empty($event_id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
    } else {
        // Prepare a parameterized query to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $event_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = array('status' => 'success', 'message' => 'Event deleted successfully');
                http_response_code(200);
            } else {
                $response = array('status' => 'error', 'message' => 'Event not found');
                http_response_code(404);
            }
            echo json_encode($response);
        } else {
            $response = array('status' => 'error', 'message' => 'Server Error');
            http_response_code(500);
            echo json_encode($response);
        }

        $stmt->close();
    }
} else {
    // Invalid request method, return an error response
    http_response_code(405); // 405 Method Not Allowed
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}

$conn->close();
?>
