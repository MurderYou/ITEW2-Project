<?php

include 'db.php';
include 'auth.php';

header("Cache-Control: no-cache, no-store, must-revalidate, private");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$sections_result = mysqli_query($conn, "SELECT * FROM sections ORDER BY section_name");

$sql = "SELECT students.*, sections.section_name
        FROM students
        LEFT JOIN sections ON students.section_id = sections.id
        WHERE 1=1";

if (!empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql .= " AND (students.first_name LIKE '%$search%'
               OR students.last_name  LIKE '%$search%'
               OR students.student_id LIKE '%$search%'
               OR students.email      LIKE '%$search%')";
}
if (!empty($_GET['section'])) {
    $section_filter = mysqli_real_escape_string($conn, $_GET['section']);
    $sql .= " AND students.section_id = '$section_filter'";
}
$sql .= " ORDER BY students.last_name ASC";

$result     = mysqli_query($conn, $sql);
$total      = mysqli_num_rows($result);
$total_all  = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
$total_pass = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students WHERE grade >= 75"))[0];
$total_fail = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students WHERE grade < 75"))[0];
$avg_grade  = mysqli_fetch_row(mysqli_query($conn, "SELECT ROUND(AVG(grade),1) FROM students"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Student Records</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <h1>Student Records</h1>
    <span>Management System</span>

    <a href="index.php" class="active">Students</a>
    <a href="add.php">Add Student</a>
    <a href="sections.php">Sections</a>


    <br><br>
    <small><?= $_SESSION['full_name'] ?></small><br>
    <a href="logout.php">Sign Out</a>
</aside>

<!-- MAIN -->
<div class="main">

    <!-- Top Bar -->
<div class="topbar">
    <h2>Students</h2>
    <a href="add.php" class="btn btn-primary">Add Student</a>
</div>

    <div class="body">

        <!-- Toast -->
        <div class="toast" id="toast">
            <?php if (!empty($_GET['msg'])): ?>
                <?= htmlspecialchars($_GET['msg']) ?>
            <?php endif; ?>
        </div>

        <!-- Stats -->
<div class="stats">
    <div class="card">
        <h4>Total Students</h4>
        <h2><?= $total_all ?></h2>
    </div>
    <div class="card">
        <h4>Passed</h4>
        <h2><?= $total_pass ?></h2>
    </div>
    <div class="card">
        <h4>Failed</h4>
        <h2><?= $total_fail ?></h2>
    </div>
    <div class="card">
        <h4>Average</h4>
        <h2><?= $avg_grade ?></h2>
    </div>
</div>

        <!-- Filter Bar -->
   <div class="fbar">
<form method="GET">
    <input type="text" name="search" placeholder="Search..." value="<?= $_GET['search'] ?? '' ?>">
    <select name="section">
        <option value="">All Sections</option>
        <?php while ($sec = mysqli_fetch_assoc($sections_result)): ?>
            <option value="<?= $sec['id'] ?>">
                <?= $sec['section_name'] ?>
            </option>
        <?php endwhile; ?>
    </select>
    <button class="btn">Search</button>
</form>
</div>

        <!-- Table Card -->
        <div class="tcard fu" style="animation-delay:.22s">
            <div class="tmeta">
                <span class="tmeta-lbl">
                    Showing <strong><?= $total ?></strong> student<?= $total != 1 ? 's' : '' ?>
                    <?php if (!empty($_GET['search'])): ?>
                        &nbsp;for&nbsp;<strong>"<?= htmlspecialchars($_GET['search']) ?>"</strong>
                    <?php endif; ?>
                </span>
            </div>
            <table id="studentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Section</th>
                        <th>Grade</th>
                        <th>Attendance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($total == 0): ?>
                    <tr><td colspan="8" class="no-rec">No students found. <a href="index.php" style="color:var(--accent)">Clear filters</a></td></tr>
                <?php else:
                    $c = 1;
                    while ($row = mysqli_fetch_assoc($result)):
                        $att = $row['attendance'];
                ?>
                <tr class="srow" data-id="<?= $row['id'] ?>" style="animation-delay:<?= $c * 0.04 ?>s">
                    <td><?= $c++ ?></td>
                    <td><span class="sid"><?= htmlspecialchars($row['student_id']) ?></span></td>
                    <td><span class="sname"><?= htmlspecialchars($row['last_name'].', '.$row['first_name']) ?></span></td>
                    <td><?= htmlspecialchars($row['email'] ?: '—') ?></td>
                    <td><?= htmlspecialchars($row['section_name'] ?? 'N/A') ?></td>
                    <td>
                        <span class="grade-badge <?= $row['grade'] >= 75 ? 'pass' : 'fail' ?>">
                            <?= number_format($row['grade'], 2) ?>
                        </span>
                    </td>
                    <td>
                        <div class="att-wrap">
                            <div class="att-bar">
                                <div class="att-fill <?= $att < 75 ? 'low' : '' ?>" style="width:<?= $att ?>%"></div>
                            </div>
                            <span class="att-txt"><?= $att ?>%</span>
                        </div>
                    </td>
                    <td>
                        <div class="ags">
                            <a href="view.php?id=<?= $row['id'] ?>"   class="btn btn-sm btn-view">View</a>
                            <a href="edit.php?id=<?= $row['id'] ?>"   class="btn btn-sm btn-edit">Edit</a>
                            <button class="btn btn-sm btn-del delete-btn"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?>">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

// DELETE MODAL
<div id="deleteModal" class="modal-bg">
    <div class="modal-box">
        <div class="m-icon">🗑</div>
        <h3>Delete Student</h3>
        <p id="deleteMessage">Are you sure you want to delete this student? This cannot be undone.</p>
        <div class="m-acts">
            <button id="cancelDelete"  type="button" class="btn btn-ghost">Cancel</button>
            <button id="confirmDelete" type="button" class="btn btn-del">Yes, Delete</button>
        </div>
    </div>
</div>

<script src="script.js"></script>
<script>
$(document).ready(function() {

   
    $(".sc").each(function(i) {
        var card = this;
        setTimeout(function() { $(card).addClass("in"); }, i * 70);
    });

   
    <?php if (!empty($_GET['msg'])): ?>
    $("#toast").fadeIn(300).delay(3000).fadeOut(500);
    <?php endif; ?>

   
    $("#searchInput").on("keyup", function() {
        var kw = $(this).val().toLowerCase();
        $(".srow").each(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(kw) > -1);
        });
    });

   
    var deleteId = null;

    function openModal(id, name) {
        deleteId = id;
        $("#deleteMessage").text("Are you sure you want to remove " + name + "? This action cannot be undone.");
        $("#deleteModal").addClass("open");
    }

    function closeModal() {
        $("#deleteModal").removeClass("open");
        deleteId = null;
    }

    $(".delete-btn").on("click", function() {
        openModal($(this).data("id"), $(this).data("name"));
    });

    $("#confirmDelete").on("click", function() {
        if (deleteId) window.location.href = "delete.php?id=" + deleteId;
    });

    $("#cancelDelete").on("click", closeModal);

    // Close when clicking the dark backdrop
    $("#deleteModal").on("click", function(e) {
        if ($(e.target).is("#deleteModal")) closeModal();
    });

    // Close with Escape key
    $(document).on("keydown", function(e) {
        if (e.key === "Escape") closeModal();
    });

});
</script>
</body>
</html>