<?php
include 'auth.php';

// Prevent back button and browser caching
header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id'] ?? '');

// Fetch the student along with their section name using a JOIN
$sql = "SELECT students.*, sections.section_name, users.full_name AS teacher_name
        FROM students
        LEFT JOIN sections ON students.section_id = sections.id
        LEFT JOIN users ON sections.teacher_id = users.id
        WHERE students.id = '$id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php?msg=Student not found.");
    exit();
}

$student = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <h2>Student Details</h2>
        <div>
            <a href="index.php" class="btn btn-back">Back</a>
            <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-primary">Edit</a>
        </div>
    </div>

    <!-- CARD -->
    <div class="card">

        <div class="student-name">
            <?= htmlspecialchars($student['last_name'] . ', ' . $student['first_name']) ?>
        </div>

        <div class="student-id">
            Student ID: <?= htmlspecialchars($student['student_id']) ?>
        </div>

        <div class="grid">

            <div class="box">
                <div class="label">Email</div>
                <div class="value"><?= htmlspecialchars($student['email'] ?: 'N/A') ?></div>
            </div>

            <div class="box">
                <div class="label">Section</div>
                <div class="value"><?= htmlspecialchars($student['section_name'] ?? 'N/A') ?></div>
            </div>

            <div class="box">
                <div class="label">Teacher</div>
                <div class="value"><?= htmlspecialchars($student['teacher_name'] ?? 'N/A') ?></div>
            </div>

            <div class="box">
                <div class="label">Grade</div>
                <div class="value <?= $student['grade'] >= 75 ? 'pass' : 'fail' ?>">
                    <?= number_format($student['grade'], 2) ?>
                    (<?= $student['grade'] >= 75 ? 'PASSED' : 'FAILED' ?>)
                </div>
            </div>

            <div class="box">
                <div class="label">Attendance</div>
                <div class="value"><?= $student['attendance'] ?>%</div>
            </div>

            <div class="box">
                <div class="label">Date Added</div>
                <div class="value">
                    <?= date("F d, Y", strtotime($student['created_at'])) ?>
                </div>
            </div>

        </div>

        <!-- ACTIONS -->
        <div class="actions">
            <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-primary">Edit</a>
            <a href="index.php" class="btn">Back</a>
        </div>

    </div>

</div>
</html>