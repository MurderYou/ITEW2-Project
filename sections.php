<?php
include 'db.php';
include 'auth.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$sections = mysqli_query($conn, "
SELECT 
    sections.id,
    sections.section_name,
    users.full_name AS teacher_name
FROM sections
LEFT JOIN users ON sections.teacher_id = users.id
ORDER BY sections.section_name
");

$students = mysqli_query($conn, "
SELECT 
    sections.section_name,
    users.full_name AS teacher_name,
    students.student_id,
    students.first_name,
    students.last_name
FROM students
LEFT JOIN sections ON students.section_id = sections.id
LEFT JOIN users ON sections.teacher_id = users.id
ORDER BY sections.section_name, students.last_name
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sections</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <h1>Student Records</h1>
    <span>Management System</span>

    <a href="index.php">Students</a>
    <a href="add.php">Add Student</a>
    <a href="sections.php" class="active">Sections</a>

    <br><br>
    <small><?= $_SESSION['full_name'] ?></small><br>
    <a href="logout.php">Sign Out</a>
</aside>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <h2>Sections</h2>
    </div>

    <div class="body">

        <!-- Section Cards -->
        <div class="stats">

            <?php while ($row = mysqli_fetch_assoc($sections)) { ?>
                <div class="card" style="min-width:200px;">
                    <h4><?= $row['section_name'] ?></h4>
                    <h3 style="margin-top:10px;">
                        <?= $row['teacher_name'] ?? 'No Teacher' ?>
                    </h3>
                </div>
            <?php } ?>

        </div>

        <!-- Table View (optional) -->
       <div class="tcard" style="margin-top:20px;">
    <h3>All Sections with Students</h3>

    <table>
        <tr>
            <th>Section</th>
            <th>Teacher</th>
            <th>Student ID</th>
            <th>Student Name</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($students)) { ?>
        <tr>
            <td><?= $row['section_name'] ?></td>
            <td><?= $row['teacher_name'] ?? 'No Teacher' ?></td>
            <td><?= $row['student_id'] ?? '-' ?></td>
            <td>
                <?= isset($row['first_name']) 
                    ? $row['last_name'] . ', ' . $row['first_name'] 
                    : '-' ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

    </div>
</div>

</body>
</html>