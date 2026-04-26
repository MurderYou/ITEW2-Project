class StudentForm {
 
    // Constructor: runs when we do "new StudentForm()"
    constructor() {
        // Store references to the form fields
        this.studentId  = document.getElementById("student_id");
        this.firstName  = document.getElementById("first_name");
        this.lastName   = document.getElementById("last_name");
        this.email      = document.getElementById("email");
        this.grade      = document.getElementById("grade");
        this.attendance = document.getElementById("attendance");
    }
 
    // Method to show an error message under a field
    showError(fieldId, message) {
        const errorSpan = document.getElementById("err_" + fieldId);
        if (errorSpan) {
            errorSpan.textContent = message;
        }
    }
 
    // Method to validate an email using Regular Expression
    isValidEmail(email) {
        // This regex pattern checks for a valid email format
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return pattern.test(email);
    }
 
    // Method to validate the Add/Edit student form
    // Returns true if valid, false if there are errors
    validateAdd() {
        let isValid = true;
 
        // ---- Validate Student ID ----
        if (this.studentId && this.studentId.value.trim() === "") {
            this.showError("student_id", "Student ID is required.");
            isValid = false;
        }
 
        // ---- Validate First Name ----
        if (this.firstName && this.firstName.value.trim() === "") {
            this.showError("first_name", "First name is required.");
            isValid = false;
        }
 
        // ---- Validate Last Name ----
        if (this.lastName && this.lastName.value.trim() === "") {
            this.showError("last_name", "Last name is required.");
            isValid = false;
        }
 
        // ---- Validate Email (only if not empty) ----
        if (this.email && this.email.value.trim() !== "") {
            if (!this.isValidEmail(this.email.value.trim())) {
                this.showError("email", "Enter a valid email address.");
                isValid = false;
            }
        }
 
        // ---- Validate Grade ----
        if (this.grade) {
            let gradeVal = parseFloat(this.grade.value);
            if (isNaN(gradeVal) || gradeVal < 0 || gradeVal > 100) {
                this.showError("grade", "Grade must be between 0 and 100.");
                isValid = false;
            }
        }
 
        // ---- Validate Attendance ----
        if (this.attendance) {
            let attVal = parseInt(this.attendance.value);
            if (isNaN(attVal) || attVal < 0 || attVal > 100) {
                this.showError("attendance", "Attendance must be between 0 and 100.");
                isValid = false;
            }
        }
 
        return isValid;
    }
}
 
 
// =============================================
// CLASS 2: StudentTable
// Handles DOM manipulation for the students table.
// Shows how we can use OOP to organize DOM operations.
// =============================================
class StudentTable {
 
    // Constructor takes the table's ID
    constructor(tableId) {
        this.table = document.getElementById(tableId);
    }
 
    // Method: Count how many rows are in the table body
    getRowCount() {
        if (!this.table) return 0;
        return this.table.querySelectorAll("tbody tr.student-row").length;
    }
 
    // Method: Highlight a specific row by student ID
    highlightRow(studentId) {
        const rows = document.querySelectorAll(".student-row");
        rows.forEach(function(row) {
            if (row.getAttribute("data-id") === String(studentId)) {
                row.style.backgroundColor = "#fff3cd";   // Light yellow highlight
            }
        });
    }
 
    // Method: Update the results count display
    updateCount() {
        const count = this.getRowCount();
        const countEl = document.querySelector(".results-count strong");
        if (countEl) {
            countEl.textContent = count;
        }
    }
}
 
 
// =============================================
// jQuery: Run this code when the DOM is ready
// $(document).ready() is the jQuery way to wait
// for the page to fully load before running JS.
// =============================================
$(document).ready(function() {
 
    // ---- jQuery: Animate table rows appearing one by one ----
    // Each row fades in with a slight delay after the previous one
    $(".student-row").each(function(index) {
        $(this).hide().delay(index * 50).fadeIn(300);
    });
 
    // ---- jQuery: Create a StudentTable object and log count ----
    // This demonstrates using our OOP class with jQuery
    let table = new StudentTable("studentsTable");
    console.log("Total students shown: " + table.getRowCount());
 
    // ---- jQuery: Live search filter (filters the table instantly) ----
    // This only works on the client-side (already-loaded rows)
    // For full search, the form submits to PHP
    $("#searchInput").on("keyup", function() {
        let keyword = $(this).val().toLowerCase();
 
        // Loop through each student row
        $(".student-row").each(function() {
            let rowText = $(this).text().toLowerCase();
 
            // Show the row if it contains the keyword, hide if not
            if (rowText.includes(keyword)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
 
    // ---- jQuery: Animate navbar on page load ----
    $(".navbar").hide().slideDown(300);
 
});