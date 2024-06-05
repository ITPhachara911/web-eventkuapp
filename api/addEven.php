<?php
// Assuming your server endpoint is something like process_data.php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $Bding = isset($_POST['Bding']) ? $_POST['Bding'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $reteDate = isset($_POST['reteDate']) ? $_POST['reteDate'] : '';

    // Simple validation example
    if (empty($name) || empty($detail) || empty($Bding) || empty($room) || empty($reteDate)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
    } else {
        $response = array('status' => 'success', 'message' => 'Data processed successfully');
        http_response_code(200);
        echo json_encode($response);
    }
} else {
    // Invalid request method, return an error response
    http_response_code(500);
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}
