<?php
session_start();

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$dbusername = "root"; // Replace with your MySQL username
$dbpassword = ""; // Replace with your MySQL password
$dbname = "college_attendance";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $mobile_number = $_POST['mobile_number'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO students (name, dob, mobile_number, email, password)
            VALUES ('$name', '$dob', '$mobile_number', '$email', '$password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registration successful!');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM students WHERE name='$username' OR email='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user'] = $row['name'];
            header("Location: index.php"); // Redirect to the same page to show the home screen
            exit;
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('No user found with that username!');</script>";
    }
}

// Handle Payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // Save payment details to the database
    $sql = "INSERT INTO payments (name, email, phone)
            VALUES ('$name', '$email', '$phone')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Payment details saved successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Handle Logout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coder's King</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php if (isset($_SESSION['user'])): ?>
        <div class="home-container">
            <nav>
                <div class="logo">
                    <img src="CodeKing_simple.png" alt="Logo"> <!-- Logo Image -->
                </div>
                <ul>
                    <li><a href="#" onclick="toggleContent('home')">Home</a></li>
                    <li><a href="#" onclick="toggleContent('courses')">Courses</a></li>
                    <li><a href="#" onclick="togglePayment()">Enroll Now</a></li>
                </ul>
            </nav>
            <section class="content" id="home-content">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
                <p>Your hub for workshops and resources.</p>
                <form method="post">
                    <button type="submit" name="logout">Logout</button>
                </form>
            </section>
            <section class="content" id="course-content" style="display:none;">
                <div class="course-box">
                    <button class="close-btn" onclick="toggleContent('home')">&times;</button>
                    <h1>Python Course</h1>
                    <p>Duration: 2 months</p>
                    <p>Price: 1399</p>
                    <p>Features:</p>
                    <ul>
                        <li>Friendly Coaching</li>
                        <li>Individual Attention</li>
                        <li>Interactive Sessions</li>
                        <li>Hands-on Projects</li>
                        <li>Flexible Timings</li>
                    </ul>
                </div>
            </section>
            <section class="content" id="payment-content" style="display:none;">
                <div class="payment-box">
                    <button class="close-btn" onclick="togglePayment()">&times;</button>
                    <h1>Payment</h1>
                    <form method="post">
                        <input type="text" name="name" placeholder="Name" required>
                        <input type="email" name="email" placeholder="Email" required>
                        <input type="text" name="phone" placeholder="Phone" required>
                        <button type="submit" name="pay">Submit Payment</button>
                    </form>
                    <div>
                        <p>Pay via QR code:</p>
                        <img src="qr.jpeg" alt="QR Code">
                        <a href="https://wa.me/9443192318?text=I%20have%20made%20a%20payment%20and%20am%20sharing%20the%20screenshot%20with%20you." class="whatsapp-link" target="_blank">Share payment screenshot on WhatsApp</a>
                    </div>
                </div>
            </section>
        </div>
    <?php else: ?>
        <div class="login-container" id="login-container">
            <div class="login-box">
                <h1>Login</h1>
                <form method="post" action="">
                    <input type="text" name="username" placeholder="Username or Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login">Login</button>
                </form>
                <p>Don't have an account? <a href="#" onclick="toggleForms()">Register here</a></p>
            </div>
        </div>
        <div class="register-container" id="register-container" style="display:none;">
            <div class="register-box">
                <h1>Register</h1>
                <form method="post">
                    <input type="text" name="name" placeholder="Full Name" required>
                    <input type="date" name="dob" placeholder="Date of Birth" required>
                    <input type="text" name="mobile_number" placeholder="Mobile Number" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="register">Register</button>
                </form>
                <p>Already have an account? <a href="#" onclick="toggleForms()">Login here</a></p>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function toggleContent(contentId) {
            var homeContent = document.getElementById('home-content');
            var courseContent = document.getElementById('course-content');
            var paymentContent = document.getElementById('payment-content');
            
            if (contentId === 'home') {
                homeContent.style.display = 'block';
                courseContent.style.display = 'none';
                paymentContent.style.display = 'none';
            } else if (contentId === 'courses') {
                homeContent.style.display = 'none';
                courseContent.style.display = 'block';
                paymentContent.style.display = 'none';
            } else if (contentId === 'payment') {
                homeContent.style.display = 'none';
                courseContent.style.display = 'none';
                paymentContent.style.display = 'block';
            }
        }

        function togglePayment() {
            var paymentContent = document.getElementById('payment-content');
            var homeContent = document.getElementById('home-content');
            var courseContent = document.getElementById('course-content');

            if (paymentContent) {
                paymentContent.style.display = paymentContent.style.display === 'none' ? 'block' : 'none';
                homeContent.style.display = 'none';
                courseContent.style.display = 'none';
            }
        }

        function toggleForms() {
            var loginContainer = document.getElementById('login-container');
            var registerContainer = document.getElementById('register-container');
            
            if (loginContainer.style.display === 'none') {
                loginContainer.style.display = 'block';
                registerContainer.style.display = 'none';
            } else {
                loginContainer.style.display = 'none';
                registerContainer.style.display = 'block';
            }
        }
    </script>
</body>
</html>
