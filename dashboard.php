<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';

/* =========================
   MARK NOTIFICATION AS READ
========================= */

if (isset($_POST['mark_read'])) {

    $notification_id = intval($_POST['notification_id']);

    $stmt = $conn->prepare(
        "UPDATE notifications
         SET notification_status = 'Read'
         WHERE notification_id = ?"
    );

    if ($stmt) {
        $stmt->bind_param("i", $notification_id);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: dashboard.php");
    exit();
}


/* =========================
   DEFAULT COUNTS
========================= */

$total_donors = 0;
$total_receivers = 0;
$total_hospitals = 0;
$total_matches = 0;
$pending_matches = 0;
$completed_matches = 0;
$unread_notifications = 0;


/* =========================
   TOTAL DONORS
========================= */

$result = $conn->query("SELECT COUNT(*) AS total FROM donors");

if ($result) {
    $row = $result->fetch_assoc();
    $total_donors = (int)$row['total'];
}


/* =========================
   TOTAL RECEIVERS
========================= */

$result = $conn->query("SELECT COUNT(*) AS total FROM receivers");

if ($result) {
    $row = $result->fetch_assoc();
    $total_receivers = (int)$row['total'];
}


/* =========================
   TOTAL HOSPITALS
========================= */

$result = $conn->query("SELECT COUNT(*) AS total FROM hospitals");

if ($result) {
    $row = $result->fetch_assoc();
    $total_hospitals = (int)$row['total'];
}


/* =========================
   TOTAL MATCHES
========================= */

$result = $conn->query("SELECT COUNT(*) AS total FROM matches");

if ($result) {
    $row = $result->fetch_assoc();
    $total_matches = (int)$row['total'];
}


/* =========================
   PENDING MATCHES
========================= */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM matches
     WHERE match_status = 'Pending'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $pending_matches = (int)$row['total'];
}


/* =========================
   COMPLETED MATCHES
========================= */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM matches
     WHERE match_status = 'Completed'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $completed_matches = (int)$row['total'];
}


/* =========================
   UNREAD NOTIFICATIONS
========================= */

$result = $conn->query(
    "SELECT COUNT(*) AS total
     FROM notifications
     WHERE notification_status = 'Unread'"
);

if ($result) {
    $row = $result->fetch_assoc();
    $unread_notifications = (int)$row['total'];
}


/* =========================
   RECENT NOTIFICATIONS
========================= */

$notifications = [];

$result = $conn->query(
    "SELECT notification_id,
            match_id,
            message,
            notification_status,
            created_at
     FROM notifications
     ORDER BY notification_id DESC
     LIMIT 10"
);

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | LifeLink</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f8;
            color: #263238;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            background: #671225;
            color: white;
            padding: 18px 6%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .brand span {
            font-size: 13px;
            display: block;
            font-weight: normal;
            opacity: 0.8;
            margin-top: 3px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-name {
            font-size: 14px;
            opacity: 0.9;
        }

        .logout-btn {
            text-decoration: none;
            color: white;
            background: #8f1d35;
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
        }

        .logout-btn:hover {
            background: #ad3b52;
        }


        /* =========================
           PAGE HEADER
        ========================= */

        .page-header {
            background: white;
            padding: 30px 6%;
            border-bottom: 1px solid #eee;
        }

        .page-header h1 {
            margin: 0;
            color: #671225;
            font-size: 30px;
        }

        .page-header p {
            margin: 8px 0 0;
            color: #777;
        }


        /* =========================
           MAIN CONTAINER
        ========================= */

        .container {
            width: 88%;
            max-width: 1250px;
            margin: 30px auto;
        }


        /* =========================
           STAT CARDS
        ========================= */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
            border-top: 4px solid #8f1d35;
        }

        .card-number {
            font-size: 34px;
            font-weight: bold;
            color: #671225;
            margin-bottom: 8px;
        }

        .card-title {
            color: #687078;
            font-size: 15px;
        }

        .notification-card {
            border-top-color: #ad3b52;
        }

        .notification-card .card-number {
            color: #ad3b52;
        }


        /* =========================
           SECTIONS
        ========================= */

        .section {
            background: white;
            margin-top: 25px;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .section h2 {
            margin: 0 0 20px;
            color: #671225;
            font-size: 21px;
        }


        /* =========================
           NOTIFICATIONS
        ========================= */

        .notification {
            padding: 16px;
            border-bottom: 1px solid #eee;
            border-radius: 7px;
        }

        .notification:last-child {
            border-bottom: none;
        }

        .notification.unread {
            background: #fdf0f3;
        }

        .notification-message {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 7px;
            color: #333;
        }

        .notification-info {
            font-size: 13px;
            color: #888;
        }

        .unread-label {
            display: inline-block;
            background: #ad3b52;
            color: white;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 11px;
            margin-left: 8px;
        }

        .read-label {
            display: inline-block;
            background: #6b8e70;
            color: white;
            padding: 4px 9px;
            border-radius: 20px;
            font-size: 11px;
            margin-left: 8px;
        }

        .read-button {
            margin-top: 10px;
            background: #671225;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
        }

        .read-button:hover {
            background: #8f1d35;
        }

        .empty {
            text-align: center;
            color: #888;
            padding: 25px;
        }


        /* =========================
           MATCH STATUS
        ========================= */

        .status-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .status-box {
            background: #f8e9ed;
            padding: 20px;
            border-radius: 9px;
            text-align: center;
        }

        .status-box strong {
            display: block;
            font-size: 27px;
            color: #671225;
            margin-bottom: 5px;
        }

        .status-box span {
            color: #666;
            font-size: 14px;
        }


        /* =========================
           QUICK ACCESS
        ========================= */

        .quick-links {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .quick-links a {
            text-decoration: none;
            color: white;
            background: #671225;
            padding: 16px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .quick-links a:hover {
            background: #8f1d35;
        }

        .public-link {
            background: #8f1d35 !important;
        }

        .public-link:hover {
            background: #ad3b52 !important;
        }


        /* =========================
           FOOTER
        ========================= */

        .footer {
            text-align: center;
            padding: 30px;
            color: #888;
            font-size: 13px;
        }


        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 950px) {

            .cards {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-links {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 5%;
            }

            .brand {
                font-size: 19px;
            }

            .admin-name {
                display: none;
            }

            .page-header {
                padding: 25px 5%;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .container {
                width: 92%;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .status-grid {
                grid-template-columns: 1fr;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================= -->

<div class="navbar">

    <div class="brand">

        ❤️ LifeLink

        <span>
            Organ Donation Management System
        </span>

    </div>


    <div class="nav-right">

        <div class="admin-name">

            Admin:
            <?php echo htmlspecialchars($_SESSION['admin']); ?>

        </div>


        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</div>


<!-- =========================
     PAGE HEADER
========================= -->

<div class="page-header">

    <h1>
        Admin Dashboard
    </h1>

    <p>
        Manage donors, receivers, hospitals, matches and notifications.
    </p>

</div>


<!-- =========================
     MAIN
========================= -->

<div class="container">


    <!-- =========================
         STAT CARDS
    ========================= -->

    <div class="cards">


        <div class="card">

            <div class="card-number">
                <?php echo $total_donors; ?>
            </div>

            <div class="card-title">
                Total Donors
            </div>

        </div>


        <div class="card">

            <div class="card-number">
                <?php echo $total_receivers; ?>
            </div>

            <div class="card-title">
                Total Receivers
            </div>

        </div>


        <div class="card">

            <div class="card-number">
                <?php echo $total_hospitals; ?>
            </div>

            <div class="card-title">
                Total Hospitals
            </div>

        </div>


        <div class="card">

            <div class="card-number">
                <?php echo $total_matches; ?>
            </div>

            <div class="card-title">
                Total Saved Matches
            </div>

        </div>


        <div class="card">

            <div class="card-number">
                <?php echo $pending_matches; ?>
            </div>

            <div class="card-title">
                Pending Matches
            </div>

        </div>


        <div class="card">

            <div class="card-number">
                <?php echo $completed_matches; ?>
            </div>

            <div class="card-title">
                Completed Matches
            </div>

        </div>


        <div class="card notification-card">

            <div class="card-number">
                <?php echo $unread_notifications; ?>
            </div>

            <div class="card-title">
                Unread Notifications
            </div>

        </div>


    </div>


    <!-- =========================
         NOTIFICATIONS
    ========================= -->

    <div class="section">

        <h2>

            🔔 Recent Notifications

            <?php if ($unread_notifications > 0) { ?>

                <span class="unread-label">

                    <?php echo $unread_notifications; ?>

                    Unread

                </span>

            <?php } ?>

        </h2>


        <?php if (count($notifications) > 0) { ?>


            <?php foreach ($notifications as $notification) { ?>


                <div class="notification
                    <?php

                    if ($notification['notification_status'] === 'Unread') {
                        echo 'unread';
                    }

                    ?>
                ">


                    <div class="notification-message">

                        <?php

                        echo htmlspecialchars(
                            $notification['message']
                        );

                        ?>


                        <?php if (
                            $notification['notification_status'] === 'Unread'
                        ) { ?>

                            <span class="unread-label">
                                Unread
                            </span>

                        <?php } else { ?>

                            <span class="read-label">
                                Read
                            </span>

                        <?php } ?>

                    </div>


                    <div class="notification-info">

                        Match ID:

                        <?php

                        echo htmlspecialchars(
                            $notification['match_id']
                        );

                        ?>

                        &nbsp; | &nbsp;

                        <?php

                        echo htmlspecialchars(
                            $notification['created_at']
                        );

                        ?>

                    </div>


                    <?php if (
                        $notification['notification_status'] === 'Unread'
                    ) { ?>


                        <form method="POST">

                            <input
                                type="hidden"
                                name="notification_id"
                                value="<?php
                                echo $notification['notification_id'];
                                ?>"
                            >


                            <button
                                type="submit"
                                name="mark_read"
                                class="read-button"
                            >

                                Mark as Read

                            </button>

                        </form>


                    <?php } ?>


                </div>


            <?php } ?>


        <?php } else { ?>


            <div class="empty">

                No notifications available.

            </div>


        <?php } ?>


    </div>


    <!-- =========================
         MATCH STATUS
    ========================= -->

    <div class="section">

        <h2>
            Match Status Overview
        </h2>


        <div class="status-grid">


            <div class="status-box">

                <strong>
                    <?php echo $pending_matches; ?>
                </strong>

                <span>
                    Pending Matches
                </span>

            </div>


            <div class="status-box">

                <strong>
                    <?php echo $completed_matches; ?>
                </strong>

                <span>
                    Completed Matches
                </span>

            </div>


            <div class="status-box">

                <strong>
                    <?php echo $total_matches; ?>
                </strong>

                <span>
                    Total Saved Matches
                </span>

            </div>


        </div>

    </div>


    <!-- =========================
         QUICK ACCESS
    ========================= -->

    <div class="section">

        <h2>
            Admin Management
        </h2>


        <div class="quick-links">


            <a href="donor_list.php">
                ❤️ Donor Management
            </a>


            <a href="receiver_list.php">
                🏥 Receiver Management
            </a>


            <a href="hospital_list.php">
                🏨 Hospital Management
            </a>


            <a href="organ_match.php">
                🔗 Find Matches
            </a>


            <a href="saved_matches.php">
                📋 Saved Matches
            </a>


            <a href="reports.php">
                📊 Reports
            </a>


            <a
                href="donor_public_register.php"
                class="public-link"
            >
                ❤️ Public Donor Registration
            </a>


            <a
                href="receiver_public_register.php"
                class="public-link"
            >
                🏥 Public Receiver Registration
            </a>


            <a href="index.php">
                🏠 View Public Home
            </a>


            <a href="about.php">
                ℹ️ About LifeLink
            </a>


        </div>

    </div>


</div>


<!-- =========================
     FOOTER
========================= -->

<div class="footer">

    LifeLink © <?php echo date("Y"); ?>

    <br>

    Organ Donation Management System

</div>


</body>

</html>