<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $id = isset($_POST['id']) ? $_POST['id'] : '';

    // Simple validation example
    if (empty($id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

        $checkSql = "SELECT COUNT(*) AS referenceCount FROM events WHERE branch_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkRow = $checkResult->fetch_assoc();

        if ($checkRow['referenceCount'] > 0) {
            // If there are references, return an error response
            http_response_code(400);
            $response = array('status' => 'error', 'message' => 'Cannot delete branch because it is referenced in event tables');
            echo json_encode($response);
            exit;
        }


    // Prepare SQL query to delete branch
    $sql = "DELETE FROM branch WHERE id = ?";

    // Prepare parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute SQL query
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response = array('status' => 'success', 'message' => 'Branch deleted successfully');
            http_response_code(200);
        } else {
            $response = array('status' => 'error', 'message' => 'Branch not found');
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
