<?php

include 'db.php';
include 'auth.php';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

class Student {

    private $conn;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Delete method
    public function delete($id) {
        $id = mysqli_real_escape_string($this->conn, $id);

        if (empty($id)) {
            return "Invalid student ID.";
        }

        $sql = "DELETE FROM students WHERE id = '$id'";

        if (mysqli_query($this->conn, $sql)) {
            return "Student deleted successfully.";
        } else {
            return "Error deleting student.";
        }
    }
}

$student = new Student($conn);

$id = $_GET['id'] ?? '';
$message = $student->delete($id);

// Redirect with message
header("Location: index.php?msg=" . urlencode($message));
exit();