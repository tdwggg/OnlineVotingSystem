<?php
if (session_id() == '') {
    session_start();
}

if (isset($_SESSION['voter_id']) && $_SESSION['voter_id'] != '') {
    header('Location: index.php');
    exit();
}

$error_message = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'empty') {
        $error_message = 'Please complete all required fields.';
    } elseif ($_GET['error'] == 'password_mismatch') {
        $error_message = 'Passwords do not match.';
    } elseif ($_GET['error'] == 'weak_password') {
        $error_message = 'Password must be at least 8 characters long.';
    } elseif ($_GET['error'] == 'invalid_voter') {
        $error_message = 'Voter ID does not exist in the official voter database.';
    } elseif ($_GET['error'] == 'already_registered') {
        $error_message = 'This Voter ID already has an account. Please log in instead.';
    } elseif ($_GET['error'] == 'email_exists') {
        $error_message = 'This email address is already used by another voter.';
    } elseif ($_GET['error'] == 'server_error') {
        $error_message = 'A server error occurred. Please try again.';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register - iVotePH</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        img.brandLogo {
            width: 126px !important;
            max-width: 126px !important;
            height: auto !important;
            max-height: 46px !important;
            object-fit: contain !important;
            display: block !important;
        }

        .userNavList,
        .sidebarMenuNav {
            list-style: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .userNavList {
            display: flex !important;
            gap: 8px !important;
            overflow-x: auto !important;
        }

        .userNavList li,
        .sidebarMenuNav li {
            list-style: none !important;
        }

        .userTopbarInner {
            display: flex !important;
            align-items: center !important;
            gap: 14px !important;
        }

        .userSidebar,
        #sidebar {
            transform: translateX(-110%);
        }

        .userSidebar.open,
        #sidebar.open {
            transform: translateX(0);
        }

        .registerAlert {
            border-radius: 16px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 16px;
            padding: 13px 14px;
        }

        .passwordNote {
            font-size: 12px;
            color: #667085;
            margin-top: 6px;
        }
    </style>
</head>

<body class="authPage">
    <div class="registerContainer">
        <div class="registerCard card">
            <div class="authLogo">
                <img src="FINALS 2.png" alt="iVotePH" class="authLogoImg">
            </div>

            <h1>Register</h1>
            <p class="registerSubtitle">Create your voter account using your existing Voter ID</p>

            <?php if ($error_message != '') { ?>
                <div class="alert alert-danger registerAlert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php } ?>

            <form id="registerForm" action="register_process.php" method="POST">
                <div class="formSection">
                    <div class="formSectionTitle">Voter Verification</div>

                    <div class="formGroup">
                        <label for="voter_id" class="formLabel">Voter ID</label>
                        <input 
                            id="voter_id" 
                            name="voter_id" 
                            type="text" 
                            class="formInput" 
                            placeholder="Enter existing Voter ID" 
                            required>
                    </div>
                </div>

                <div class="formSection">
                    <div class="formSectionTitle">Personal Information</div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="first_name" class="formLabel">First Name</label>
                                <input id="first_name" name="first_name" type="text" class="formInput" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="middle_name" class="formLabel">Middle Name</label>
                                <input id="middle_name" name="middle_name" type="text" class="formInput">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="last_name" class="formLabel">Last Name</label>
                                <input id="last_name" name="last_name" type="text" class="formInput" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="birth_date" class="formLabel">Birthdate</label>
                                <input id="birth_date" name="birth_date" type="date" class="formInput" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="sex" class="formLabel">Sex</label>
                                <select id="sex" name="sex" class="formSelect" required>
                                    <option value="">Select Sex</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="formGroup">
                                <label for="mobile_number" class="formLabel">Mobile Number</label>
                                <div class="phoneInputWrapper">
                                    <div class="phonePrefix">+63</div>
                                    <input 
                                        id="mobile_number" 
                                        name="mobile_number" 
                                        type="tel" 
                                        class="formInput" 
                                        placeholder="9123456789" 
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="formGroup">
                        <label for="email" class="formLabel">Email Address</label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="formInput" 
                            placeholder="example@email.com" 
                            required>
                    </div>
                </div>

                <div class="formSection addressSection">
                    <div class="formSectionTitle">Address Information</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="region" class="formLabel">Region</label>
                                <input id="region" name="region" type="text" class="formInput" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="province" class="formLabel">Province</label>
                                <input id="province" name="province" type="text" class="formInput" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="city_municipality" class="formLabel">City / Municipality</label>
                                <input id="city_municipality" name="city_municipality" type="text" class="formInput" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="barangay" class="formLabel">Barangay</label>
                                <input id="barangay" name="barangay" type="text" class="formInput" required>
                            </div>
                        </div>
                    </div>

                    <div class="formGroup">
                        <label for="specific_address" class="formLabel">Specific Address</label>
                        <input id="specific_address" name="specific_address" type="text" class="formInput" required>
                    </div>
                </div>

                <div class="formSection">
                    <div class="formSectionTitle">Account Security</div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="password" class="formLabel">Password</label>
                                <input id="password" name="password" type="password" class="formInput" required>
                                <div class="passwordNote">Minimum of 8 characters.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="formGroup">
                                <label for="confirm_password" class="formLabel">Confirm Password</label>
                                <input id="confirm_password" name="confirm_password" type="password" class="formInput" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="formCheckbox">
                    <input type="checkbox" id="certify" name="certify" value="1" required>
                    <label for="certify">I certify that the information I provided is accurate and complete.</label>
                </div>

                <button type="submit" class="registerBtn btn w-100 py-3">
                    <i class="fa-solid fa-user-plus me-2"></i>
                    Create Account
                </button>
            </form>

            <div class="loginLink text-center mt-3">
                Already registered?
                <a href="login.php">Log in</a>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function (e) {
            var password = document.getElementById('password').value;
            var confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>