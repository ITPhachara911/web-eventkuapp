<?php
require 'dbconnect.php';

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id > 0) {
    // Prepare and execute the query to get the image path
    $stmt = $conn->prepare("SELECT image_path FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if ($imagePath) {
       
        $fullImagePath = __DIR__ . '/' . $imagePath;

        // Check if the image file exists
        if (file_exists($fullImagePath)) {
            // Get the image mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fullImagePath);
            finfo_close($finfo);

            // Send the appropriate headers and output the image
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . filesize($fullImagePath));
            readfile($fullImagePath);
            exit;
        } else {
            http_response_code(404);
            echo "Image not found.";
        }
    } else {
        http_response_code(404);
        echo "Image path not found in database.";
    }
} else {
    http_response_code(400);
    echo "Invalid image ID.";
}

$conn->close();
?>