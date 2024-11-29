<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start(); // Start the session

// Database connection
$servername = "sql311.infinityfree.com"; // Replace with your server name
$username = "if0_37331201";        // Replace with your database username
$password = "W33LAgXuWC3JFnI";            // Replace with your database password
$dbname = "if0_37331201_testzone";      // Replace with your database name


$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $teacher_name = $_POST['name'];
        $teacher_pass = $_POST['password'];

        $login_query = "SELECT teacher_id, teacher_pass, teacher_gender FROM teachers WHERE teacher_name = '$teacher_name'";

        $result = $conn->query($login_query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['teacher_pass'];
            $teacher_id = $row['teacher_id'];
            $teacher_gender = $row['teacher_gender'];

            // Verify the entered password against the hashed password in the database
            if (password_verify($teacher_pass, $hashed_password)) {
                // Set session variables for authenticated teacher
                $_SESSION['teacherName'] = $teacher_name;
                $_SESSION['teacherID'] = $teacher_id;
                $_SESSION['teacherGender'] = $teacher_gender;

                header("Location: teacher.php");
                exit();
            } else {
                $login_error = "Username / Password invalid.";
            }
        } else {
            $login_error = "Username / Password invalid.";
        }
    }

    if (isset($_POST['signup'])) {
        $teacher_name = $_POST['name'];
        $teacher_pass = $_POST['password'];
        $teacher_gender = $_POST['satisfaction'];

        // Hash the password for security
        $hashed_password = password_hash($teacher_pass, PASSWORD_DEFAULT);

        // Generate a unique 5-digit teacher_id
        do {
            $teacher_id = rand(10000, 99999);
            $check_id_query = "SELECT teacher_id FROM teachers WHERE teacher_id = $teacher_id";
            $result = $conn->query($check_id_query);
        } while ($result->num_rows > 0);

        // Insert the new teacher into the database
        $insert_query = "INSERT INTO teachers (teacher_id, teacher_name, teacher_pass, teacher_gender) 
                         VALUES ($teacher_id, '$teacher_name', '$hashed_password', '$teacher_gender')";

        if ($conn->query($insert_query) === TRUE) {
            $_SESSION['teacherID'] = $teacher_id;
            $_SESSION['teacherName'] = $teacher_name;
            $_SESSION['teacherGender'] = $teacher_gender;

            header("Location: login_signup_teacher.php");
            exit();
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <title>TestZone</title>
    <link rel="stylesheet" href="assets/css/login_signup.css" />
    <link rel="icon" href="assets/images/page_logo.png">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-grad-school.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/gender.css">
    <link rel="stylesheet" href="assets/css/lightbox.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    <link rel="stylesheet" href="assets/css/track.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header clearfix" style="display: block;" role="header">
        <div class="logo">
            <a style="margin-left: 3rem;" href="#"><em>Test</em> Zone</a>
        </div>
        <a href="#menu" class="menu-link"><i class="fa fa-bars"></i></a>
        <nav id="menu" class="main-nav" role="navigation">
            <ul class="main-menu">
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="wrapper">
        <div class="form login">
            <header>Login</header>
            <form method="POST">
                <input type="text" name="name" placeholder="Name" required />   
                <input type="password" name="password" placeholder="Password" required />
                <input type="submit" name="login" value="Login" />
            </form>
            <?php if (isset($login_error)) { echo "<p style='color: red;'>$login_error</p>"; } ?>
        </div>

        <div class="form signup">
            <header>Signup</header>
            <form method="POST" onsubmit="return validatePassword()">
                <input class="name" type="text" name="name" placeholder="Enter Name" required />
                <input id="password" class="password" type="password" name="password" placeholder="Create Password" required /> 
                <input id="confirm_password" class="password" type="password" placeholder="Re-Enter Password" required /> 
                
                <div class="normal-container">
                    <div class="smile-rating-container">
                        <div class="smile-rating-toggle-container" style="display: inline-flex;">
                            <input id="meh" name="satisfaction" type="radio" value="Male" required /> 
                            <input id="fun" name="satisfaction" type="radio" value="Female" required /> 
                            <label for="meh" class="rating-label rating-label-meh">Male</label>
                            <div class="smile-rating-toggle"></div>
                            <div class="rating-eye rating-eye-left"></div>
                            <div class="rating-eye rating-eye-right"></div>
                            <div class="mouth rating-eye-bad-mouth"></div>
                            <div class="toggle-rating-pill"></div>
                            <label for="fun" class="rating-label rating-label-fun">Female</label>
                        </div>
                    </div>
                </div>
                
                <div class="checkbox">
                    <input type="checkbox" id="signupCheck" required />
                    <label for="signupCheck" style="color: black;">I accept all terms & conditions</label>
                </div>
                <input type="submit" name="signup" value="Signup" />
            </form>
        </div>

        <script>
            const wrapper = document.querySelector(".wrapper"),
                signupHeader = document.querySelector(".login header"),
                loginHeader = document.querySelector(".signup header");

            loginHeader.addEventListener("click", () => {
                wrapper.classList.add("active");
            });
            signupHeader.addEventListener("click", () => {
                wrapper.classList.remove("active");
            });
        </script>

        <script src="assets/js/isotope.min.js"></script>
        <script src="assets/js/custom.js"></script>
    </section>
    <script>
        function validatePassword() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</body>
</html>
