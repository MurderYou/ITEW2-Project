<?php
include 'db.php';
include 'auth.php';

header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];

// FETCH SECTIONS FIRST (so we can reuse it)
$sections = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $grade      = mysqli_real_escape_string($conn, $_POST['grade']);
    $attendance = mysqli_real_escape_string($conn, $_POST['attendance']);

    // HANDLE SECTION (IMPORTANT FIX)
    $section_id = !empty($_POST['section_id']) 
        ? mysqli_real_escape_string($conn, $_POST['section_id']) 
        : null;

    // VALIDATION
    if (empty($student_id)) $errors[] = "Student ID is required.";
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name))  $errors[] = "Last name is required.";

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($grade < 0 || $grade > 100) {
        $errors[] = "Grade must be 0–100.";
    }

    if ($attendance < 0 || $attendance > 100) {
        $errors[] = "Attendance must be 0–100.";
    }

    // CHECK DUPLICATE STUDENT ID
    $check = mysqli_query($conn, "SELECT id FROM students WHERE student_id='$student_id'");
    if (mysqli_num_rows($check) > 0) {
        $errors[] = "Student ID already exists.";
    }

    // CHECK IF SECTION EXISTS
    if (!empty($section_id)) {
        $checkSection = mysqli_query($conn, 
            "SELECT id FROM sections WHERE id='$section_id'");
        if (mysqli_num_rows($checkSection) == 0) {
            $errors[] = "Invalid section selected.";
        }
    }

    // INSERT
    if (empty($errors)) {

        // If NULL, do not wrap in quotes
        $section_value = $section_id === null ? "NULL" : "'$section_id'";

        $sql = "INSERT INTO students 
        (student_id, first_name, last_name, email, section_id, grade, attendance)
        VALUES 
        ('$student_id','$first_name','$last_name','$email',$section_value,'$grade','$attendance')";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=Student added successfully!");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Student</title>
<link rel="stylesheet" href="styles.css">
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <h1>Student Records</h1>
    <span>Management System</span>

    <a href="index.php">Students</a>
    <a href="add.php" class="active">Add Student</a>
    <a href="sections.php">Sections</a>

    <br><br>
    <small><?= $_SESSION['full_name'] ?></small><br>
    <a href="logout.php">Sign Out</a>
</aside>

<!-- MAIN -->
<div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <h2>Add Student</h2>
        <div>
            <a href="index.php" class="btn">Back</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="card" style="max-width:700px; margin:auto;">

        <!-- ERRORS -->
        <?php if (!empty($errors)): ?>
            <div class="alert error">
                <?php foreach ($errors as $err): ?>
                    <div><?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

       <form method="POST">

    <!-- ROW -->
    <div class="form-row">
        <div class="form-group">
            <label>Student ID</label>
            <input type="text" name="student_id" required
                value="<?= htmlspecialchars($_POST['student_id'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Section</label>
            <select name="section_id" required>
                <option value="">Select Section</option>
                <?php mysqli_data_seek($sections, 0); ?>
                <?php while ($sec = mysqli_fetch_assoc($sections)): ?>
                    <option value="<?= $sec['id'] ?>"
                        <?= (($_POST['section_id'] ?? '') == $sec['id']) ? 'selected' : '' ?>>
                        <?= $sec['section_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <!-- ROW -->
    <div class="form-row">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" required
                value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" required
                value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        </div>
    </div>

    <!-- EMAIL -->
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>

    <!-- ROW -->
    <div class="form-row">
        <div class="form-group">
            <label>Grade</label>
            <input type="number" name="grade" min="0" max="100" required
                value="<?= htmlspecialchars($_POST['grade'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Attendance</label>
            <input type="number" name="attendance" min="0" max="100" required
                value="<?= htmlspecialchars($_POST['attendance'] ?? '') ?>">
        </div>
    </div>

    <!-- ACTIONS -->
    <div style="margin-top:15px;">
        <button class="btn btn-primary">Save Student</button>
        <a href="index.php" class="btn">Cancel</a>
    </div>

    </form>
    </div>

</div>

</body>
</html>