<?php
// error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';

    // Validate password
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        echo "<script>alert('Password must be at least 6 characters long');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
        exit;
    }

    // Check if email already exists
    $check_email = "SELECT id FROM volunteer_list WHERE email = ?";
    $check_stmt = $conn->prepare($check_email);
    $check_stmt->bind_param("s", $email);
    $email = $_POST['email'];
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email address already registered. Please use a different email or try logging in.');</script>";
        $check_stmt->close();
        exit;
    }
    $check_stmt->close();

    // Generate roll number (format: YYYY + 3-digit sequence)
    $year = date('Y');
    $roll_query = "SELECT MAX(CAST(SUBSTRING(roll, 5) AS UNSIGNED)) as max_num FROM volunteer_list WHERE roll LIKE '$year%'";
    $roll_result = $conn->query($roll_query);
    $roll_row = $roll_result->fetch_assoc();
    $next_num = ($roll_row['max_num'] ?? 0) + 1;
    $roll = $year . str_pad($next_num, 3, '0', STR_PAD_LEFT);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO volunteer_list (roll, firstname, middlename, lastname, contact, email, password, motivation, comment) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $roll, $firstname, $middlename, $lastname, $contact, $email, $hashed_password, $motivation, $comment);

    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $motivation = $_POST['motivation'];
    $comment = ''; // Empty comment for new registrations

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! Your roll number is: $roll');</script>";
        include("registersucceed.php");

    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">

<head>
    <link rel="shortcut icon" href="gms1.png" type="image/png">

<body class="hold-transition login-page  dark-mode">

    <body
        class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed dark-mode sidebar-mini-md sidebar-mini-xs"
        data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1, shrink-to-fit=no">
        <title>GMS - Volunteer Registration</title>
        <link rel="stylesheet" type="text/css" href="inc/.css" media="all">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            body {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .registration-card {
                background: rgba(255, 255, 255, 0.95);
                border-radius: 20px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                padding: 40px;
                margin: 20px auto;
                max-width: 600px;
                color: #333;
            }

            .form-control {
                border-radius: 10px;
                border: 2px solid #e9ecef;
                padding: 12px 15px;
                transition: all 0.3s ease;
            }

            .form-control:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .btn-primary {
                background: linear-gradient(45deg, #667eea, #764ba2);
                border: none;
                border-radius: 25px;
                padding: 12px 30px;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            }

            .password-strength {
                height: 5px;
                border-radius: 3px;
                margin-top: 5px;
                transition: all 0.3s ease;
            }

            .strength-weak {
                background: #dc3545;
            }

            .strength-medium {
                background: #ffc107;
            }

            .strength-strong {
                background: #28a745;
            }

            .card-header {
                background: linear-gradient(45deg, #667eea, #764ba2);
                color: white;
                border-radius: 15px 15px 0 0 !important;
                text-align: center;
                padding: 20px;
            }

            .form-group label {
                font-weight: 600;
                color: #555;
            }

            .required-field::after {
                content: " *";
                color: #dc3545;
            }
        </style>
        </head>

        <div class="wrapper">
            <section class="content">
                <div class="container-fluid">

                    <div class="registration-card">
                        <div class="card-header">
                            <h2><i class="fas fa-user-plus"></i> Volunteer Registration Form</h2>
                            <p class="mb-0">Join our community and make a difference!</p>
                        </div>

                        <body>

                            <div class="container">
                                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                                    id="registrationForm">
                                    <div class="form-group">
                                        <label for="firstname" class="required-field">First Name:</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                            placeholder="Enter your first name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="middlename">Middle Name:</label>
                                        <input type="text" class="form-control form" id="middlename" name="middlename"
                                            placeholder="Enter your middle name (optional)">
                                    </div>
                                    <div class="form-group">
                                        <label for="lastname" class="required-field">Last Name:</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname"
                                            placeholder="Enter your last name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="contact" class="required-field">Contact Number:</label>
                                        <input type="text" class="form-control" id="contact" name="contact"
                                            placeholder="078-000-0000" pattern="\d{10}" maxlength="10"
                                            oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                        <small class="form-text text-muted">Only 10 digits allowed.</small>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="required-field">Email Address:</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="your.email@example.com"
                                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                                            title="Enter a valid email address" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="required-field">Password:</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Enter your password (min 6 characters)" minlength="6" required>
                                        <div class="password-strength" id="passwordStrength"></div>
                                        <small class="form-text text-muted">Password must be at least 6 characters
                                            long.</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="confirm_password" class="required-field">Confirm Password:</label>
                                        <input type="password" class="form-control" id="confirm_password"
                                            name="confirm_password" placeholder="Confirm your password" required>
                                        <div id="passwordMatch"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="motivation" class="required-field">Motivation:</label>
                                        <textarea class="form-control" id="motivation" name="motivation"
                                            placeholder="Tell us why you want to volunteer (e.g., Kindness, Calling, Fight against poverty, etc.)"
                                            rows="4" required></textarea>
                                    </div>

                                    <div class="text-center">
                                        <button type="submit" name="save" class="btn btn-primary btn-lg">
                                            <i class="fas fa-user-plus"></i> Register as Volunteer
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-lg ml-2"
                                            onclick="window.location.href='./index.php'">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </div>

                                    <div class="text-center mt-3">
                                        <a href="./index.php" class="text-muted">
                                            <i class="fas fa-arrow-left"></i> Back to Homepage
                                        </a>
                                    </div>

                                </form>
                            </div>
                        </body>

</html>

<script>
    // Password strength checker
    document.getElementById('password').addEventListener('input', function () {
        const password = this.value;
        const strengthBar = document.getElementById('passwordStrength');
        let strength = 0;

        if (password.length >= 6) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;

        strengthBar.className = 'password-strength';
        if (strength < 2) {
            strengthBar.classList.add('strength-weak');
            strengthBar.style.width = '20%';
        } else if (strength < 4) {
            strengthBar.classList.add('strength-medium');
            strengthBar.style.width = '60%';
        } else {
            strengthBar.classList.add('strength-strong');
            strengthBar.style.width = '100%';
        }
    });

    // Password confirmation checker
    document.getElementById('confirm_password').addEventListener('input', function () {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        const matchDiv = document.getElementById('passwordMatch');

        if (confirmPassword === '') {
            matchDiv.innerHTML = '';
        } else if (password === confirmPassword) {
            matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>';
        } else {
            matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>';
        }
    });

    // Form validation
    document.getElementById('registrationForm').addEventListener('submit', function (e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }

        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
            return false;
        }
    });
</script>