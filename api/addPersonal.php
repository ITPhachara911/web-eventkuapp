<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve POST data
    $first_name = isset($_POST['first_name']) ? $_POST['first_name'] : '';
    $last_name = isset($_POST['last_name']) ? $_POST['last_name'] : '';
    $personnel_number = isset($_POST['personnel_number']) ? $_POST['personnel_number'] : '';
    $faculty_id = isset($_POST['faculty_id']) ? $_POST['faculty_id'] : '';
    $branch_id = isset($_POST['branch_id']) ? $_POST['branch_id'] : '';

    // Simple validation example
    if (empty($first_name) || empty($last_name) || empty($personnel_number) || empty($faculty_id) || empty($branch_id)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit;
    }

    // Prepare SQL query
    $sql = "INSERT INTO Personnel (first_name, last_name, personnel_number, faculty_id, branch_id)
            VALUES (?, ?, ?, ?, ?)";
    
    // Prepare parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $first_name, $last_name, $personnel_number, $faculty_id, $branch_id);

    // Execute SQL query
    if ($stmt->execute()) {
        $response = array('status' => 'success', 'message' => 'Personnel added successfully');
        http_response_code(201);
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
