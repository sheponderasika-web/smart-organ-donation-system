<?php
include 'db.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name']);
    $age = (int) $_POST['age'];
    $gender = $_POST['gender'];
    $blood_group = $_POST['blood_group'];
    $organ = $_POST['organ'];
    $contact = trim($_POST['contact']);
    $location = trim($_POST['location']);

    $availability = "Available";
    $verification_status = "Pending";

    if ($age < 18 || $age > 100) {

        $error = "Please enter a valid age between 18 and 100.";

    } elseif (!preg_match('/^[0-9]{10,15}$/', $contact)) {

        $error = "Please enter a valid contact number.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO donors
            (name, age, gender, blood_group, organ, contact, location, availability, verification_status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        if ($stmt) {

            $stmt->bind_param(
                "sisssssss",
                $name,
                $age,
                $gender,
                $blood_group,
                $organ,
                $contact,
                $location,
                $availability,
                $verification_status
            );

            if ($stmt->execute()) {
                $success = "Your donor registration has been submitted successfully.";
            } else {
                $error = "Registration failed. Please try again.";
            }

            $stmt->close();

        } else {
            $error = "Database connection error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Donor Registration | LifeLink</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #f8e9ed, #fff7f8);
    color: #292929;
}

/* ================= NAVBAR ================= */

.navbar {
    height: 72px;
    background: #671225;
    padding: 0 7%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 3px 15px rgba(0,0,0,0.12);
}

.logo {
    color: white;
    text-decoration: none;
    font-size: 24px;
    font-weight: bold;
    letter-spacing: .3px;
}

.logo span {
    color: #f4b8c5;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 25px;
}

.nav-links a {
    color: #fff;
    text-decoration: none;
    font-size: 14px;
    transition: .2s;
}

.nav-links a:hover {
    color: #f5b8c4;
}

.nav-links .active {
    color: #f5b8c4;
    font-weight: bold;
}

/* ================= PAGE ================= */

.page {
    min-height: calc(100vh - 72px);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 35px 20px;
}

/* ================= CARD ================= */

.card {
    width: 100%;
    max-width: 920px;
    background: #ffffff;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(103, 18, 37, 0.13);
}

/* ================= CARD HEADER ================= */

.card-header {
    background: linear-gradient(135deg, #671225, #8f1d35);
    color: white;
    padding: 30px 40px;
    display: flex;
    align-items: center;
    gap: 22px;
}

.heart-box {
    width: 68px;
    height: 68px;
    background: rgba(255,255,255,0.13);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 35px;
}

.card-header h1 {
    margin: 0 0 7px;
    font-size: 29px;
}

.card-header p {
    margin: 0;
    color: #f7dfe4;
    font-size: 14px;
}

/* ================= FORM AREA ================= */

.form-area {
    padding: 34px 40px 38px;
}

.section-title {
    margin-bottom: 22px;
}

.section-title h2 {
    margin: 0;
    color: #671225;
    font-size: 19px;
}

.section-title p {
    margin: 6px 0 0;
    color: #777;
    font-size: 13px;
}

/* ================= MESSAGE ================= */

.success,
.error {
    padding: 14px 16px;
    border-radius: 10px;
    margin-bottom: 22px;
    font-size: 14px;
}

.success {
    background: #e8f7ed;
    color: #176b36;
    border-left: 4px solid #2e8b57;
}

.error {
    background: #fdebed;
    color: #8b1e35;
    border-left: 4px solid #b32645;
}

/* ================= FORM ================= */

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full {
    grid-column: 1 / -1;
}

label {
    font-size: 13px;
    font-weight: bold;
    color: #454545;
    margin-bottom: 8px;
}

input,
select {
    width: 100%;
    height: 46px;
    padding: 0 14px;
    border: 1px solid #ddd;
    border-radius: 9px;
    background: #fcfcfc;
    color: #333;
    font-size: 14px;
    transition: .2s;
}

input::placeholder {
    color: #aaa;
}

input:hover,
select:hover {
    border-color: #c7a1aa;
}

input:focus,
select:focus {
    outline: none;
    background: #fff;
    border-color: #8f1d35;
    box-shadow: 0 0 0 3px rgba(143,29,53,0.08);
}

/* ================= BUTTON ================= */

.btn {
    width: 100%;
    height: 50px;
    margin-top: 28px;
    border: none;
    border-radius: 10px;
    background: linear-gradient(135deg, #8f1d35, #671225);
    color: white;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
    box-shadow: 0 7px 18px rgba(103,18,37,0.20);
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 23px rgba(103,18,37,0.25);
}

/* ================= BOTTOM INFO ================= */

.bottom-info {
    margin-top: 24px;
    padding: 15px 18px;
    background: #faf4f6;
    border: 1px solid #f0dfe3;
    border-radius: 10px;
    text-align: center;
    color: #777;
    font-size: 12px;
    line-height: 1.6;
}

.bottom-info strong {
    color: #671225;
}

.back {
    display: block;
    text-align: center;
    margin-top: 18px;
    color: #8f1d35;
    text-decoration: none;
    font-size: 13px;
    font-weight: bold;
}

.back:hover {
    text-decoration: underline;
}

/* ================= MOBILE ================= */

@media (max-width: 760px) {

    .navbar {
        height: auto;
        padding: 18px 20px;
        flex-direction: column;
        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px 17px;
    }

    .page {
        padding: 20px 12px;
        align-items: flex-start;
    }

    .card-header {
        padding: 25px 22px;
    }

    .heart-box {
        width: 55px;
        height: 55px;
        font-size: 28px;
    }

    .card-header h1 {
        font-size: 23px;
    }

    .form-area {
        padding: 27px 20px 30px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-group.full {
        grid-column: auto;
    }
}

</style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar">

    <a href="index.php" class="logo">
        Life<span>Link</span>
    </a>

    <div class="nav-links">

        <a href="index.php">Home</a>

        <a href="about.php">About</a>

        <a href="donor_public_register.php" class="active">
            Donor Registration
        </a>

        <a href="receiver_public_register.php">
            Receiver Registration
        </a>

        <a href="login.php">
            Admin Login
        </a>

    </div>

</nav>


<!-- MAIN -->

<div class="page">

    <div class="card">

        <!-- HEADER -->

        <div class="card-header">

            <div class="heart-box">
                🫀
            </div>

            <div>

                <h1>Become an Organ Donor</h1>

                <p>
                    Your decision today can help give someone a chance at life.
                </p>

            </div>

        </div>


        <!-- FORM -->

        <div class="form-area">

            <div class="section-title">

                <h2>Donor Information</h2>

                <p>
                    Please provide your basic details carefully.
                </p>

            </div>


            <?php if (!empty($success)): ?>

                <div class="success">
                    ✓
                    <?php echo htmlspecialchars($success); ?>
                    Your details will be reviewed by the administrator.
                </div>

            <?php endif; ?>


            <?php if (!empty($error)): ?>

                <div class="error">
                    ⚠
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>


            <form method="POST">

                <div class="form-grid">


                    <div class="form-group">

                        <label>Full Name</label>

                        <input
                            type="text"
                            name="name"
                            maxlength="100"
                            placeholder="Enter your full name"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Age</label>

                        <input
                            type="number"
                            name="age"
                            min="18"
                            max="100"
                            placeholder="Enter your age"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>Gender</label>

                        <select name="gender" required>

                            <option value="">
                                Select gender
                            </option>

                            <option value="Male">
                                Male
                            </option>

                            <option value="Female">
                                Female
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Blood Group</label>

                        <select name="blood_group" required>

                            <option value="">
                                Select blood group
                            </option>

                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Organ</label>

                        <select name="organ" required>

                            <option value="">
                                Select organ
                            </option>

                            <option value="Kidney">Kidney</option>
                            <option value="Liver">Liver</option>
                            <option value="Heart">Heart</option>
                            <option value="Lung">Lung</option>
                            <option value="Pancreas">Pancreas</option>
                            <option value="Cornea">Cornea</option>

                        </select>

                    </div>


                    <div class="form-group">

                        <label>Contact Number</label>

                        <input
                            type="text"
                            name="contact"
                            maxlength="15"
                            placeholder="Enter contact number"
                            required
                        >

                    </div>


                    <div class="form-group full">

                        <label>Location</label>

                        <input
                            type="text"
                            name="location"
                            maxlength="100"
                            placeholder="Enter your city / location"
                            required
                        >

                    </div>

                </div>


                <button type="submit" class="btn">
                    Register as Donor
                </button>

            </form>


            <div class="bottom-info">

                🔒 Your registration is submitted with
                <strong>Pending Verification</strong>.
                <br>
                The administrator will review your details before verification.

            </div>


            <a href="index.php" class="back">
                ← Back to Home
            </a>

        </div>

    </div>

</div>

</body>

</html>