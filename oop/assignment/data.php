<?php
session_start();
header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session storage is initialized
if (!isset($_SESSION['students']) || !is_array($_SESSION['students'])) {
    $_SESSION['students'] = [];
}

// Function to retrieve all students
function getStudents() {
    return array_values($_SESSION['students']);
}

// Function to add a student
function addStudent($id, $name, $department, $email) {
    foreach ($_SESSION['students'] as $student) {
        if ($student['id'] == $id) {
            return ["error" => "Student ID already exists!"];
        }
    }
    
    // Add the new student
    $_SESSION['students'][] = [
        "id" => $id,
        "name" => $name,
        "department" => $department,
        "email" => $email
    ];

    return ["message" => "Student added successfully!", "students" => getStudents()];
}

// Function to edit a student
function editStudent($id, $name, $department, $email) {
    foreach ($_SESSION['students'] as &$student) {
        if ($student['id'] == $id) {
            $student['name'] = $name;
            $student['department'] = $department;
            $student['email'] = $email;
            return ["message" => "Student updated successfully!", "students" => getStudents()];
        }
    }
    return ["error" => "Student not found!"];
}

// Function to delete a student
function deleteStudent($id) {
    foreach ($_SESSION['students'] as $index => $student) {
        if ($student['id'] == $id) {
            array_splice($_SESSION['students'], $index, 1);
            return ["message" => "Student deleted successfully!", "students" => getStudents()];
        }
    }
    return ["error" => "Student not found!"];
}

// Handling incoming requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'] ?? null;
    $department = $_POST['department'] ?? null;
    $email = $_POST['email'] ?? null;

    $response = ["error" => "Invalid request!"];

    if ($action === "add" && $id && $name && $department && $email) {
        $response = addStudent($id, $name, $department, $email);
    } elseif ($action === "edit" && $id && $name && $department && $email) {
        $response = editStudent($id, $name, $department, $email);
    } elseif ($action === "delete" && $id) {
        $response = deleteStudent($id);
    }

    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo json_encode(getStudents());
}
?>
