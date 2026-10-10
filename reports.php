<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

include 'db.php';


/* =========================
   BASIC COUNTS
========================= */

$total_donors = 0;
$total_receivers = 0;
$total_hospitals = 0;
$total_matches = 0;

$pending_matches = 0;
$verified_matches = 0;
$retrieved_matches = 0;
$in_transit_matches = 0;
$transplanted_matches = 0;
$completed_matches = 0;


/* =========================
   TOTAL DONORS
========================= */

$sql = "SELECT COUNT(*) AS total FROM donors";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $total_donors = $row['total'];
}


/* =========================
   TOTAL RECEIVERS
========================= */

$sql = "SELECT COUNT(*) AS total FROM receivers";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $total_receivers = $row['total'];
}


/* =========================
   TOTAL HOSPITALS
========================= */

$sql = "SELECT COUNT(*) AS total FROM hospitals";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $total_hospitals = $row['total'];
}


/* =========================
   TOTAL MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total FROM matches";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $total_matches = $row['total'];
}


/* =========================
   PENDING MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'Pending'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $pending_matches = $row['total'];
}


/* =========================
   VERIFIED MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'Verified'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $verified_matches = $row['total'];
}


/* =========================
   RETRIEVED MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'Retrieved'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $retrieved_matches = $row['total'];
}


/* =========================
   IN TRANSIT MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'In Transit'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $in_transit_matches = $row['total'];
}


/* =========================
   TRANSPLANTED MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'Transplanted'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $transplanted_matches = $row['total'];
}


/* =========================
   COMPLETED MATCHES
========================= */

$sql = "SELECT COUNT(*) AS total
        FROM matches
        WHERE match_status = 'Completed'";

$result = $conn->query($sql);

if ($result) {

    $row = $result->fetch_assoc();

    $completed_matches = $row['total'];
}


/* =========================
   ORGAN-WISE DONOR REPORT
========================= */

$organ_reports = [];

$sql = "SELECT
            organ,
            COUNT(*) AS total
        FROM donors
        GROUP BY organ
        ORDER BY total DESC";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $organ_reports[] = $row;

    }
}


/* =========================
   BLOOD GROUP-WISE DONOR REPORT
========================= */

$blood_reports = [];

$sql = "SELECT
            blood_group,
            COUNT(*) AS total
        FROM donors
        GROUP BY blood_group
        ORDER BY blood_group ASC";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $blood_reports[] = $row;

    }
}


/* =========================
   SAVED MATCH REPORT
========================= */

$matches = [];

$sql = "SELECT
            match_id,
            donor_id,
            receiver_id,
            organ,
            blood_group_match,
            location_match,
            availability_match,
            match_score,
            match_status,
            created_at
        FROM matches
        ORDER BY match_id DESC";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $matches[] = $row;

    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Reports - Organ Donation Management System</title>


    <style>

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background-color: #f4f6f8;

        }


        .header {

            background-color: #2c3e50;

            color: white;

            padding: 20px;

            text-align: center;

        }


        .header h1 {

            margin: 0;

        }


        .container {

            width: 95%;

            margin: 30px auto;

        }


        .top-buttons {

            margin-bottom: 25px;

        }


        .btn {

            display: inline-block;

            padding: 10px 18px;

            text-decoration: none;

            border-radius: 5px;

            color: white;

            margin-right: 8px;

            border: none;

            cursor: pointer;

            font-size: 14px;

        }


        .dashboard-btn {

            background-color: #3498db;

        }


        .print-btn {

            background-color: #27ae60;

        }


        .section {

            background-color: white;

            padding: 25px;

            margin-bottom: 30px;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.1);

        }


        .section h2 {

            color: #2c3e50;

            margin-top: 0;

        }


        .summary-grid {

            display: grid;

            grid-template-columns: repeat(4, 1fr);

            gap: 15px;

        }


        .summary-card {

            background-color: #f8f9fa;

            border: 1px solid #ddd;

            padding: 20px;

            text-align: center;

            border-radius: 8px;

        }


        .summary-card h3 {

            margin: 0;

            font-size: 30px;

            color: #2c3e50;

        }


        .summary-card p {

            margin-bottom: 0;

            color: #555;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 15px;

        }


        th {

            background-color: #2c3e50;

            color: white;

            padding: 12px;

            text-align: left;

        }


        td {

            padding: 10px;

            border-bottom: 1px solid #ddd;

        }


        tr:hover {

            background-color: #f5f5f5;

        }


        .status {

            display: inline-block;

            padding: 5px 9px;

            border-radius: 12px;

            font-size: 12px;

        }


        .pending {

            background-color: #f39c12;

            color: white;

        }


        .verified {

            background-color: #3498db;

            color: white;

        }


        .retrieved {

            background-color: #8e44ad;

            color: white;

        }


        .transit {

            background-color: #16a085;

            color: white;

        }


        .transplanted {

            background-color: #2980b9;

            color: white;

        }


        .completed {

            background-color: #27ae60;

            color: white;

        }


        .empty {

            text-align: center;

            color: #777;

            padding: 20px;

        }


        @media print {

            .no-print {

                display: none;

            }


            body {

                background-color: white;

            }


            .section {

                box-shadow: none;

                border: 1px solid #ddd;

            }

        }


        @media (max-width: 900px) {

            .summary-grid {

                grid-template-columns: repeat(2, 1fr);

            }

        }


        @media (max-width: 600px) {

            .summary-grid {

                grid-template-columns: 1fr;

            }


            table {

                font-size: 12px;

            }


            th,
            td {

                padding: 7px;

            }

        }

    </style>

</head>


<body>


<!-- =========================
     HEADER
========================= -->

<div class="header">

    <h1>Organ Donation Management System</h1>

    <p>Reports Module</p>

</div>


<div class="container">


    <!-- =========================
         BUTTONS
    ========================== -->

    <div class="top-buttons no-print">

        <a href="dashboard.php"
           class="btn dashboard-btn">

            ← Back to Dashboard

        </a>


        <button
            onclick="window.print()"
            class="btn print-btn">

            🖨️ Print Report

        </button>

    </div>


    <!-- =========================
         GENERAL SUMMARY
    ========================== -->

    <div class="section">

        <h2>📊 General Summary</h2>


        <div class="summary-grid">


            <div class="summary-card">

                <h3>
                    <?php echo $total_donors; ?>
                </h3>

                <p>
                    Total Donors
                </p>

            </div>


            <div class="summary-card">

                <h3>
                    <?php echo $total_receivers; ?>
                </h3>

                <p>
                    Total Receivers
                </p>

            </div>


            <div class="summary-card">

                <h3>
                    <?php echo $total_hospitals; ?>
                </h3>

                <p>
                    Total Hospitals
                </p>

            </div>


            <div class="summary-card">

                <h3>
                    <?php echo $total_matches; ?>
                </h3>

                <p>
                    Total Saved Matches
                </p>

            </div>


        </div>

    </div>


    <!-- =========================
         MATCH STATUS REPORT
    ========================== -->

    <div class="section">

        <h2>🔄 Match Status Report</h2>


        <table>

            <tr>

                <th>
                    Match Status
                </th>

                <th>
                    Total Matches
                </th>

            </tr>


            <tr>

                <td>
                    Pending
                </td>

                <td>
                    <?php echo $pending_matches; ?>
                </td>

            </tr>


            <tr>

                <td>
                    Verified
                </td>

                <td>
                    <?php echo $verified_matches; ?>
                </td>

            </tr>


            <tr>

                <td>
                    Retrieved
                </td>

                <td>
                    <?php echo $retrieved_matches; ?>
                </td>

            </tr>


            <tr>

                <td>
                    In Transit
                </td>

                <td>
                    <?php echo $in_transit_matches; ?>
                </td>

            </tr>


            <tr>

                <td>
                    Transplanted
                </td>

                <td>
                    <?php echo $transplanted_matches; ?>
                </td>

            </tr>


            <tr>

                <td>
                    Completed
                </td>

                <td>
                    <?php echo $completed_matches; ?>
                </td>

            </tr>

        </table>

    </div>


    <!-- =========================
         ORGAN-WISE REPORT
    ========================== -->

    <div class="section">

        <h2>🩸 Organ-wise Donor Report</h2>


        <?php if (count($organ_reports) > 0) { ?>


            <table>

                <tr>

                    <th>
                        Organ
                    </th>

                    <th>
                        Total Donors
                    </th>

                </tr>


                <?php foreach ($organ_reports as $organ) { ?>

                    <tr>

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $organ['organ']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $organ['total'];

                            ?>

                        </td>

                    </tr>

                <?php } ?>

            </table>


        <?php } else { ?>


            <div class="empty">

                No donor data available.

            </div>


        <?php } ?>

    </div>


    <!-- =========================
         BLOOD GROUP REPORT
    ========================== -->

    <div class="section">

        <h2>🩸 Blood Group-wise Donor Report</h2>


        <?php if (count($blood_reports) > 0) { ?>


            <table>

                <tr>

                    <th>
                        Blood Group
                    </th>

                    <th>
                        Total Donors
                    </th>

                </tr>


                <?php foreach ($blood_reports as $blood) { ?>

                    <tr>

                        <td>

                            <?php

                            echo htmlspecialchars(
                                $blood['blood_group']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo $blood['total'];

                            ?>

                        </td>

                    </tr>

                <?php } ?>

            </table>


        <?php } else { ?>


            <div class="empty">

                No blood group data available.

            </div>


        <?php } ?>

    </div>


    <!-- =========================
         SAVED MATCHES REPORT
    ========================== -->

    <div class="section">

        <h2>📋 Saved Matches Detailed Report</h2>


        <?php if (count($matches) > 0) { ?>


            <table>

                <tr>

                    <th>
                        Match ID
                    </th>

                    <th>
                        Donor ID
                    </th>

                    <th>
                        Receiver ID
                    </th>

                    <th>
                        Organ
                    </th>

                    <th>
                        Blood Match
                    </th>

                    <th>
                        Location
                    </th>

                    <th>
                        Availability
                    </th>

                    <th>
                        Score
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Created
                    </th>

                </tr>


                <?php foreach ($matches as $match) { ?>


                    <tr>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['match_id']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['donor_id']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['receiver_id']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['organ']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['blood_group_match']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['location_match']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['availability_match']
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $match['match_score']
                            );

                            ?>

                        </td>


                        <td>


                            <?php

                            $status_class = "pending";


                            if (
                                $match['match_status']
                                == "Verified"
                            ) {

                                $status_class = "verified";

                            }


                            elseif (
                                $match['match_status']
                                == "Retrieved"
                            ) {

                                $status_class = "retrieved";

                            }


                            elseif (
                                $match['match_status']
                                == "In Transit"
                            ) {

                                $status_class = "transit";

                            }


                            elseif (
                                $match['match_status']
                                == "Transplanted"
                            ) {

                                $status_class = "transplanted";

                            }


                            elseif (
                                $match['match_status']
                                == "Completed"
                            ) {

                                $status_class = "completed";

                            }

                            ?>


                            <span
                                class="status <?php echo $status_class; ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $match['match_status']
                                      );
                                 ?>

</span>

</td>

<td>

<?php

echo

htmlspecialchars(

$match['created_at']

);

?>

</td>

</tr>

<?php } ?>

</table>

<?php } else { ?>

↓

<div clas__mpty">

No saved matches

available.

</div>

<?php } ?>

</div>

</div>

</body>

</html>

 
