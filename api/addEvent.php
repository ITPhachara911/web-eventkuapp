<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $detail = isset($_POST['detail']) ? $_POST['detail'] : '';
    $Bding = isset($_POST['Bding']) ? $_POST['Bding'] : '';
    $room = isset($_POST['room']) ? $_POST['room'] : '';
    $personnel = isset($_POST['personnel']) ? $_POST['personnel'] : '';
    $mainOrg = isset($_POST['mainOrg']) ? $_POST['mainOrg'] : '';
    $subOrg = isset($_POST['subOrg']) ? $_POST['subOrg'] : '';
    $time_period = isset($_POST['reteDate']) ? $_POST['reteDate'] : '';

    // Simple validation example
    if (empty($name) || empty($detail) || empty($Bding) || empty($room) || empty($time_period) || empty($personnel) || empty($mainOrg) || empty($subOrg)) {
        http_response_code(400);
        $response = array('status' => 'error', 'message' => 'Incomplete data');
        echo json_encode($response);
        exit();
    }

    // Assuming you parse $time_period to get $start_time and $end_time
    list($start_time, $end_time) = parseTimePeriod($time_period);

    // Handle file upload
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['img']['tmp_name'];
        $fileName = $_FILES['img']['name'];
        $fileSize = $_FILES['img']['size'];
        $fileType = $_FILES['img']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $uploadFileDir = 'uploaded_files/';
        $dest_path = $uploadFileDir . $fileName;

        if (!move_uploaded_file($fileTmpPath, $dest_path)) {
            $response = array('status' => 'error', 'message' => 'Error moving the uploaded file.');
            http_response_code(500);
            echo json_encode($response);
            exit();
        }
    } else {
        $response = array('status' => 'error', 'message' => 'Error uploading file.');
        http_response_code(400);
        echo json_encode($response);
        exit();
    }

    // Prepare a parameterized query to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO events (name, start_date, end_date, description, room_id, location_id, faculty_id, branch_id, personnel_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssiiiiis", $name, $start_time, $end_time, $detail, $room, $Bding, $mainOrg, $subOrg, $personnel, $dest_path);

    if ($stmt->execute()) {
        $response = array('status' => 'success', 'message' => 'Data created successfully');
        http_response_code(200);
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

function parseTimePeriod($time_period) {
    // Example parsing function, adjust according to your time_period format
    $parts = explode(' - ', $time_period);
    $start_time = $parts[0];
    $end_time = $parts[1];
    return array($start_time, $end_time);
}
?>
