<?php
require 'dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $people = array(
            array("name" => 'ลอยกระทง', "age" => 23, "date" => 'อาคาร 20 ห้อง 101', "id" => 1),
            array("name" => 'คนที่ 2', "age" => 25, "date" => 30, "id" => 2),
            array("name" => 'คนที่ 3', "age" => 40, "date" => 28, "id" => 3),
            array("name" => 'คนที่ 4', "age" => 32, "date" => 35, "id" => 4),
            array("name" => 'คนที่ 5', "age" => 28, "date" => 29, "id" => 5),
            array("name" => 'คนที่ 6', "age" => 45, "date" => 50, "id" => 6),
            array("name" => 'คนที่ 7', "age" => 45, "date" => 50, "id" => 7),
            array("name" => 'คนที่ 8', "age" => 45, "date" => 50, "id" => 8),
            array("name" => 'คนที่ 9', "age" => 45, "date" => 50, "id" => 9),
            array("name" => 'คนที่ 10', "age" => 45, "date" => 50, "id" => 10),
            array("name" => 'คนที่ 11', "age" => 45, "date" => 50, "id" => 11),
            array("name" => 'คนที่ 12', "age" => 45, "date" => 50, "id" => 12),
            array("name" => 'คนที่ 13', "age" => 45, "date" => 50, "id" => 13),
            array("name" => 'คนที่ 14', "age" => 45, "date" => 50, "id" => 14),
        );

        $foundPerson = array();

        foreach ($people as $person) {
            if ($person['id'] == $id) {
                $foundPerson = $person;
                break; // Stop the loop once a match is found
            }
        }

        if ($foundPerson !== null) {
            http_response_code(200);
            // $foundPerson contains the person with the matching id
            echo json_encode($foundPerson);
        } else {
            echo 'no see';
        }
    } else {
        $people = array(
            array("name" => 'ลอยกระทง', "age" => 23, "date" => 'อาคาร 20 ห้อง 101', "id" => 1),
            array("name" => 'คนที่ 2', "age" => 25, "date" => 30, "id" => 2),
            array("name" => 'คนที่ 3', "age" => 40, "date" => 28, "id" => 3),
            array("name" => 'คนที่ 4', "age" => 32, "date" => 35, "id" => 4),
            array("name" => 'คนที่ 5', "age" => 28, "date" => 29, "id" => 5),
            array("name" => 'คนที่ 6', "age" => 45, "date" => 50, "id" => 6),
            array("name" => 'คนที่ 7', "age" => 45, "date" => 50, "id" => 7),
            array("name" => 'คนที่ 8', "age" => 45, "date" => 50, "id" => 8),
            array("name" => 'คนที่ 9', "age" => 45, "date" => 50, "id" => 9),
            array("name" => 'คนที่ 10', "age" => 45, "date" => 50, "id" => 10),
            array("name" => 'คนที่ 11', "age" => 45, "date" => 50, "id" => 11),
            array("name" => 'คนที่ 12', "age" => 45, "date" => 50, "id" => 12),
            array("name" => 'คนที่ 13', "age" => 45, "date" => 50, "id" => 13),
            array("name" => 'คนที่ 14', "age" => 45, "date" => 50, "id" => 14),
        );
        http_response_code(200);
        echo json_encode($people, JSON_UNESCAPED_UNICODE);
    }
} else {
    // Invalid request method, return an error response
    http_response_code(500);
    $response = array('status' => 'error', 'message' => 'Invalid request method');
    echo json_encode($response);
}
