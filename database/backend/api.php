<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "student_planner";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM tasks ORDER BY deadline ASC");
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    echo json_encode($tasks);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['title']) && !empty($data['course']) && !empty($data['deadline'])) {
        $title = $conn->real_escape_string($data['title']);
        $course = $conn->real_escape_string($data['course']);
        $deadline = $conn->real_escape_string($data['deadline']);
        $priority = $conn->real_escape_string($data['priority']);
        
        $sql = "INSERT INTO tasks (title, course, deadline, priority) VALUES ('$title', '$course', '$deadline', '$priority')";
        
        if ($conn->query($sql)) {
            echo json_encode(["status" => "success", "message" => "Task added"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Insertion failed"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Missing fields"]);
    }
}

$conn->close();
?>
