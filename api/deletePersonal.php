<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the personnel ID from the request body
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    // Simple validation example
    if (empty($id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    } else {
        // Check if the personnel is referenced in other tables
        $checkSql = "SELECT COUNT(*) AS referenceCount FROM events WHERE personnel_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkRow = $checkResult->fetch_assoc();

        if ($checkRow['referenceCount'] > 0) {
            // If there are references, return an error response
            http_response_code(400);
            $response = array('status' => 'error', 'message' => 'Cannot delete personnel because it is referenced in event tables');
            echo json_encode($response);
            exit;
        }

        // Prepare a parameterized query to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM Personnel WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response = array('status' => 'success', 'message' => 'Personnel deleted successfully');
                http_response_code(200);
            } else {
                $response = array('status' => 'error', 'message' => 'Personnel not found');
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

// Close connection
$conn->close();
?>
