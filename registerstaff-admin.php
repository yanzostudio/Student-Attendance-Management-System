<?php
require 'db_config.php';

// Handle form submission for staff registration
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['type'] == "staff" || $_POST['type'] == "teacher") {
        if (!empty($_POST['name']) && !empty($_POST['contactNo']) && !empty($_POST['email']) && !empty($_POST['password'])) {
            // Get values from the form
            $name = $_POST['name'];
            $contactNo = $_POST['contactNo'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $dob = $_POST['dob'];  // Date of Birth
            $position = $_POST['position']; // Staff Position
            $salary = $_POST['salary'];  // Staff Salary
            $qualification = $_POST['qualification']; // Staff Qualification
            $hireDate = $_POST['hireDate']; // Staff Hire Date

            // Insert staff into STAFF table
            $stmtStaff = oci_parse($conn, "INSERT INTO staffs (STAFF_NAME, STAFF_EMAIL, STAFF_PASSWORD, STAFF_CONTACTNO, STAFF_DATEOFBIRTH, STAFF_POSITION, STAFF_SALARY, STAFF_QUALIFICATION, STAFF_HIREDATE) 
                                          VALUES (:name, :email, :password, :contactNo, TO_DATE(:dob, 'YYYY-MM-DD'), :position, :salary, :qualification, TO_DATE(:hireDate, 'YYYY-MM-DD'))
                                          RETURNING STAFF_ID INTO :staffId");

            // Bind values
            oci_bind_by_name($stmtStaff, ":name", $name);
            oci_bind_by_name($stmtStaff, ":email", $email);
            oci_bind_by_name($stmtStaff, ":password", $password);
            oci_bind_by_name($stmtStaff, ":contactNo", $contactNo);
            oci_bind_by_name($stmtStaff, ":dob", $dob);
            oci_bind_by_name($stmtStaff, ":position", $position);
            oci_bind_by_name($stmtStaff, ":salary", $salary);
            oci_bind_by_name($stmtStaff, ":qualification", $qualification);
            oci_bind_by_name($stmtStaff, ":hireDate", $hireDate);

            // Bind the staff ID to capture it
            $staffId = null;
            oci_bind_by_name($stmtStaff, ":staffId", $staffId, 32);

            if (oci_execute($stmtStaff)) {
                // Successfully inserted staff, now handle teacher data if type is teacher
                if ($_POST['type'] == "teacher") {
                    // Ensure teacher-specific fields are provided
                    $specialization = $_POST['specialization'] ?? null;
                    $achievement = $_POST['achievement'] ?? null;
                    $status = $_POST['status'] ?? null;

                    // Debugging: Check if teacher-specific fields are set
                    if (empty($specialization) || empty($achievement) || empty($status)) {
                        echo "Teacher-specific fields are missing. Please ensure that all fields are filled.";
                    } else {
                        // Insert into teacher table
                        $stmtTeacher = oci_parse($conn, "INSERT INTO teachers (STAFF_ID, TEACHER_SPECIALIZATION, TEACHER_ACHIEVEMENT, TEACHER_STATUS) 
                                                         VALUES (:staffId, :specialization, :achievement, :status)");

                        oci_bind_by_name($stmtTeacher, ":staffId", $staffId);
                        oci_bind_by_name($stmtTeacher, ":specialization", $specialization);
                        oci_bind_by_name($stmtTeacher, ":achievement", $achievement);
                        oci_bind_by_name($stmtTeacher, ":status", $status);

                        if (oci_execute($stmtTeacher)) {
                            // Successfully inserted teacher data
                            oci_free_statement($stmtTeacher);
                        } else {
                            $e = oci_error($stmtTeacher);
                            echo "Teacher Insert Error: " . $e['message'];
                        }
                    }
                }
                // Redirect after successful registration
              header('Location: registerstaff-admin.php');
                exit;
            } else {
                $e = oci_error($stmtStaff);
                echo "Error: " . $e['message'];
            }

            // Close statement
            oci_free_statement($stmtStaff);
        }
    }
    oci_close($conn); // Close connection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Staff Page</title>
    <link rel="stylesheet" href="css/regUser-admin-styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php require 'sidebar-admin.php'; ?>

    <!-- Main Content -->
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Admin</span>
                <h2>Register Staff</h2>
            </div>
        </div>

        <div class="content">
            <!-- Register Form -->
            <div class="register-wrapper">
                <h2>Register Staff</h2>
                <form action="registerstaff-admin.php" method="POST">
                    <div>
                        <input type="radio" id="staff" name="type" value="staff" required>
                        <label for="staff">Staff</label>
                        <input type="radio" id="teacher" name="type" value="teacher">
                        <label for="teacher">Teacher</label>
                    </div>

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" required />
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="text" name="password" id="password" required />
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required />
                    </div>

                    <div class="form-group">
                        <label for="contactNo">Contact No</label>
                        <input type="tel" name="contactNo" id="contactNo" />
                    </div>

                    <div class="form-group">
                        <label for="dob">DOB</label>
                        <input type="date" name="dob" id="dob" />
                    </div>

                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" name="position" id="position" required />
                    </div>

                    <div class="form-group">
                        <label for="salary">Salary</label>
                        <input type="number" name="salary" id="salary" required />
                    </div>

                    <div class="form-group">
                        <label for="qualification">Qualification</label>
                        <input type="text" name="qualification" id="qualification" required />
                    </div>

                    <div class="form-group">
                        <label for="hireDate">Hire Date</label>
                        <input type="date" name="hireDate" id="hireDate" required />
                    </div>

                    <!-- Teacher Specific Fields -->
                    <div class="teacher-fields" style="display: none;">
                        <div class="form-group">
                            <label for="specialization">Specialization</label>
                            <input type="text" name="specialization" id="specialization" />
                        </div>

                        <div class="form-group">
                            <label for="achievement">Achievement</label>
                            <input type="text" name="achievement" id="achievement" />
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <input type="number" name="status" id="status" />
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Add Staff">
                        <input type="reset" value="Reset">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle teacher fields based on selection
        document.querySelectorAll('input[name="type"]').forEach((input) => {
            input.addEventListener('change', function () {
                const teacherFields = document.querySelector('.teacher-fields');
                if (this.value === 'teacher') {
                    teacherFields.style.display = 'block';
                } else {
                    teacherFields.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
