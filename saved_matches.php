<?php

session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}


/* =========================
   UPDATE MATCH STATUS
========================= */

if (isset($_POST['update_status'])) {

    $match_id = intval($_POST['match_id']);
    $match_status = $_POST['match_status'];

    $allowed_statuses = [
        "Pending",
        "Verified",
        "Retrieved",
        "In Transit",
        "Transplanted",
        "Completed"
    ];

    if (in_array($match_status, $allowed_statuses)) {

        /* =========================
           GET CURRENT STATUS
        ========================= */

        $currentSql = "SELECT match_status
                       FROM matches
                       WHERE match_id = ?";

        $currentStmt = $conn->prepare($currentSql);

        if ($currentStmt) {

            $currentStmt->bind_param(
                "i",
                $match_id
            );

            $currentStmt->execute();

            $currentResult = $currentStmt->get_result();

            $currentRow = $currentResult->fetch_assoc();

            $currentStmt->close();

            $old_status = $currentRow['match_status'] ?? "";

        } else {

            die("Database Error: " . $conn->error);

        }


        /* =========================
           UPDATE STATUS
        ========================= */

        $updateSql = "UPDATE matches
                      SET match_status = ?
                      WHERE match_id = ?";

        $updateStmt = $conn->prepare($updateSql);

        if ($updateStmt) {

            $updateStmt->bind_param(
                "si",
                $match_status,
                $match_id
            );

            $updateStmt->execute();

            $updateStmt->close();

        } else {

            die("Database Error: " . $conn->error);

        }


        /* =========================
           CREATE NOTIFICATION
        ========================= */

        if ($old_status != $match_status) {

            $message = "Match #"
                     . $match_id
                     . " status changed from "
                     . $old_status
                     . " to "
                     . $match_status;


            $notificationSql = "INSERT INTO notifications
                                (
                                    match_id,
                                    message,
                                    notification_status
                                )
                                VALUES (?, ?, 'Unread')";

            $notificationStmt =
                $conn->prepare($notificationSql);

            if ($notificationStmt) {

                $notificationStmt->bind_param(
                    "is",
                    $match_id,
                    $message
                );

                $notificationStmt->execute();

                $notificationStmt->close();

            }

        }


        /* =========================
           REDIRECT
        ========================= */

        header(
            "Location: saved_matches.php?updated=1"
        );

        exit();

    }

}


/* =========================
   FETCH SAVED MATCHES
========================= */

$sql = "SELECT
            m.match_id,
            m.donor_id,
            m.receiver_id,
            m.organ,
            m.blood_group_match,
            m.location_match,
            m.availability_match,
            m.match_score,
            m.match_status,
            m.created_at,

            d.name AS donor_name,
            d.blood_group AS donor_blood_group,

            r.name AS receiver_name,
            r.blood_group AS receiver_blood_group

        FROM matches m

        LEFT JOIN donors d
        ON m.donor_id = d.donor_id

        LEFT JOIN receivers r
        ON m.receiver_id = r.receiver_id

        ORDER BY m.match_id DESC";


$result = $conn->query($sql);


if (!$result) {

    die(
        "Database Error: "
        . $conn->error
    );

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Saved Matches</title>


<style>

body {

    font-family: Arial, sans-serif;

    background: #f4f6f9;

    margin: 0;

    padding: 0;

}


.header {

    background: #b30000;

    color: white;

    padding: 20px;

    text-align: center;

}


.container {

    width: 96%;

    margin: 30px auto;

}


.top-bar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 20px;

}


h2 {

    color: #333;

    margin: 0;

}


.back-btn {

    background: #333;

    color: white;

    padding: 10px 18px;

    text-decoration: none;

    border-radius: 5px;

}


.back-btn:hover {

    background: #555;

}


.success-message {

    background: #d4edda;

    color: #155724;

    padding: 12px;

    margin-bottom: 20px;

    border-radius: 5px;

    border: 1px solid #c3e6cb;

}


.table-box {

    background: white;

    padding: 20px;

    border-radius: 8px;

    box-shadow:
        0 2px 8px rgba(0,0,0,0.1);

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 1250px;

}


th {

    background: #b30000;

    color: white;

    padding: 12px;

    text-align: center;

}


td {

    padding: 10px;

    border-bottom: 1px solid #ddd;

    text-align: center;

}


tr:hover {

    background: #f9f9f9;

}


.score {

    font-weight: bold;

    color: #008000;

}


.status-form {

    display: flex;

    gap: 5px;

    justify-content: center;

    align-items: center;

}


.status-select {

    padding: 7px;

    border: 1px solid #ccc;

    border-radius: 5px;

}


.update-btn {

    background: #007bff;

    color: white;

    border: none;

    padding: 7px 10px;

    border-radius: 5px;

    cursor: pointer;

}


.update-btn:hover {

    background: #0056b3;

}


.count-box {

    background: white;

    display: inline-block;

    padding: 12px 20px;

    margin-bottom: 15px;

    border-radius: 6px;

    box-shadow:
        0 2px 5px rgba(0,0,0,0.1);

}


.count-number {

    font-size: 22px;

    font-weight: bold;

    color: #b30000;

}


.no-data {

    text-align: center;

    padding: 30px;

    color: #777;

    font-size: 18px;

}

</style>

</head>


<body>


<div class="header">

    <h1>
        Organ Donation Management System
    </h1>

    <p>
        Saved Matches & Status Tracking
    </p>

</div>


<div class="container">


    <div class="top-bar">

        <h2>
            Saved Donor-Receiver Matches
        </h2>


        <a href="dashboard.php"
           class="back-btn">

            ← Back to Dashboard

        </a>

    </div>


    <?php if (isset($_GET['updated'])): ?>

        <div class="success-message">

            Match status updated successfully!

            Notification created successfully.

        </div>

    <?php endif; ?>


    <?php

    $total_matches = $result->num_rows;

    ?>


    <div class="count-box">

        Total Saved Matches:

        <span class="count-number">

            <?php
            echo $total_matches;
            ?>

        </span>

    </div>


    <div class="table-box">


        <?php if ($total_matches > 0): ?>


        <table>


            <thead>

                <tr>

                    <th>
                        Match ID
                    </th>

                    <th>
                        Donor
                    </th>

                    <th>
                        Donor Blood
                    </th>

                    <th>
                        Receiver
                    </th>

                    <th>
                        Receiver Blood
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
                        Current Status
                    </th>

                    <th>
                        Update Status
                    </th>

                    <th>
                        Created At
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php while (
                $row = $result->fetch_assoc()
            ): ?>


                <tr>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['match_id']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['donor_name']
                            ?? 'Unknown'
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['donor_blood_group']
                            ?? '-'
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['receiver_name']
                            ?? 'Unknown'
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['receiver_blood_group']
                            ?? '-'
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['organ']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['blood_group_match']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['location_match']
                        );

                        ?>

                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['availability_match']
                        );

                        ?>

                    </td>


                    <td class="score">

                        <?php

                        echo htmlspecialchars(
                            $row['match_score']
                        );

                        ?>%

                    </td>


                    <td>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $row['match_status']
                            );

                            ?>

                        </strong>

                    </td>


                    <td>


                        <form method="POST"
                              class="status-form">


                            <input
                                type="hidden"
                                name="match_id"
                                value="<?php
                                echo $row['match_id'];
                                ?>">


                            <select
                                name="match_status"
                                class="status-select">


                                <option value="Pending"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'Pending'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    Pending

                                </option>


                                <option value="Verified"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'Verified'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    Verified

                                </option>


                                <option value="Retrieved"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'Retrieved'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    Retrieved

                                </option>


                                <option value="In Transit"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'In Transit'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    In Transit

                                </option>


                                <option value="Transplanted"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'Transplanted'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    Transplanted

                                </option>


                                <option value="Completed"

                                    <?php

                                    if (
                                        $row['match_status']
                                        == 'Completed'
                                    ) {

                                        echo 'selected';

                                    }

                                    ?>>

                                    Completed

                                </option>


                            </select>


                            <button
                                type="submit"
                                name="update_status"
                                class="update-btn">

                                Update

                            </button>


                        </form>


                    </td>


                    <td>

                        <?php

                        echo htmlspecialchars(
                            $row['created_at']
                        );

                        ?>

                    </td>


                </tr>


            <?php endwhile; ?>


            </tbody>


        </table>


        <?php else: ?>


            <div class="no-data">

                No saved matches found.

            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>