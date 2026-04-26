<?php

include 'db.php';
include 'auth.php';

// Prevent back button and browser caching
header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
 
// Get the student ID from the URL (e.g., edit.php?id=3)
$id = mysqli_real_escape_string($conn, $_GET['id'] ?? '');
 
// ---- FETCH STUDENT DATA from database ----
$student_result = mysqli_query($conn, "SELECT * FROM students WHERE id = '$id'");
 
// If student not found, redirect back
if (mysqli_num_rows($student_result) == 0) {
    header("Location: index.php?msg=Student not found.");
    exit();
}
 
// Get the student's data as an array
$student = mysqli_fetch_assoc($student_result);
 
$errors = [];
 
// ---- PROCESS FORM WHEN SUBMITTED ----
if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    // Get and clean all form inputs
    $student_id = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name  = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $email      = mysqli_real_escape_string($conn, trim($_POST['email']));
    $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
    $grade      = mysqli_real_escape_string($conn, $_POST['grade']);
    $attendance = mysqli_real_escape_string($conn, $_POST['attendance']);
 
    // ---- SERVER-SIDE VALIDATION ----
    if (empty($student_id)) $errors[] = "Student ID is required.";
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name))  $errors[] = "Last name is required.";
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($grade < 0 || $grade > 100)      $errors[] = "Grade must be 0-100.";
    if ($attendance < 0 || $attendance > 100) $errors[] = "Attendance must be 0-100.";
 
    // Check if student_id is taken by ANOTHER student (not this one)
    $check = mysqli_query($conn,
        "SELECT id FROM students WHERE student_id = '$student_id' AND id != '$id'"
    );
    if (mysqli_num_rows($check) > 0) {
        $errors[] = "Student ID '$student_id' is already used by another student.";
    }
 
    // ---- UPDATE if no errors ----
    if (empty($errors)) {
        $sql = "UPDATE students SET
                    student_id = '$student_id',
                    first_name = '$first_name',
                    last_name  = '$last_name',
                    email      = '$email',
                    section_id = '$section_id',
                    grade      = '$grade',
                    attendance = '$attendance'
                WHERE id = '$id'";
 
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php?msg=Student updated successfully!");
            exit();
        } else {
            $errors[] = "Database error: " . mysqli_error($conn);
        }
    }
 
    // If there were errors, update $student array so the form shows what the user typed
    $student['student_id'] = $_POST['student_id'];
    $student['first_name'] = $_POST['first_name'];
    $student['last_name']  = $_POST['last_name'];
    $student['email']      = $_POST['email'];
    $student['section_id'] = $_POST['section_id'];
    $student['grade']      = $_POST['grade'];
    $student['attendance'] = $_POST['attendance'];
}
 
// Fetch sections for dropdown
$sections = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
 
<div class="container">
    <div style="margin-bottom:18px;">
    <h2 style="font-size:18px;color:#0f1b2d;">Edit Student</h2>
</div>
   
    <!-- PHP Errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert error">
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
 
   <div class="tcard" style="max-width:720px;margin:auto;padding:20px;">
        <!-- The form posts back to edit.php with the same student ID -->
        <form id="editForm" action="edit.php?id=<?= $id ?>" method="POST">
 
            <div class="form-row">
                <div class="form-group">
                    <label for="student_id">Student ID <span class="required">*</span></label>
                    <input type="text" id="student_id" name="student_id"
                           value="<?= htmlspecialchars($student['student_id']) ?>">
                    <span class="field-error" id="err_student_id"></span>
                </div>
 
                <div class="form-group">
                    <label for="section_id">Section</label>
                    <select id="section_id" name="section_id">
                        <option value="">-- Select Section --</option>
                        <?php while ($sec = mysqli_fetch_assoc($sections)): ?>
                            <option value="<?= $sec['id'] ?>"
                                <?= $student['section_id'] == $sec['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sec['section_name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
 
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           value="<?= htmlspecialchars($student['first_name']) ?>">
                    <span class="field-error" id="err_first_name"></span>
                </div>
 
                <div class="form-group">
                    <label for="last_name">Last Name <span class="required">*</span></label>
                    <input type="text" id="last_name" name="last_name"
                           value="<?= htmlspecialchars($student['last_name']) ?>">
                    <span class="field-error" id="err_last_name"></span>
                </div>
            </div>
 
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email"
                       value="<?= htmlspecialchars($student['email']) ?>">
                <span class="field-error" id="err_email"></span>
            </div>
 
            <div class="form-row">
                <div class="form-group">
                    <label for="grade">Grade (0-100)</label>
                    <input type="number" id="grade" name="grade"
                           min="0" max="100" step="0.01"
                           value="<?= htmlspecialchars($student['grade']) ?>">
                    <span class="field-error" id="err_grade"></span>
                </div>
 
                <div class="form-group">
                    <label for="attendance">Attendance % (0-100)</label>
                    <input type="number" id="attendance" name="attendance"
                           min="0" max="100"
                           value="<?= htmlspecialchars($student['attendance']) ?>">
                    <span class="field-error" id="err_attendance"></span>
                </div>
            </div>
 
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="index.php" class="btn btn-outline">Cancel</a>
            </div>
 
        </form>
    </div>
</div>
 
<script src="script.js"></script>
<script>
    // ---- jQuery: Validate the edit form before submitting ----
    $("#editForm").on("submit", function(e) {
        $(".field-error").text("");
 
        // Use our StudentForm OOP class from script.js
        let form = new StudentForm();
        let valid = form.validateAdd();   // Same validation rules as add
 
        if (!valid) {
            e.preventDefault();
            $(this).addClass("shake");
            setTimeout(() => $(this).removeClass("shake"), 500);
        }
    });
 
    // ---- jQuery: Slide in the form card ----
    $(".form-card").hide().slideDown(400);
</script>
</body>
</html>