<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$error = "";

$donor_pk = "donor_id";
$receiver_pk = "receiver_id";

/* Find donor primary key */
$donor_columns = [];

$result = $conn->query("SHOW COLUMNS FROM donors");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $donor_columns[] = $row["Field"];
    }
}

if (!in_array("donor_id", $donor_columns) && in_array("id", $donor_columns)) {
    $donor_pk = "id";
}

/* Find receiver primary key */
$receiver_columns = [];

$result = $conn->query("SHOW COLUMNS FROM receivers");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $receiver_columns[] = $row["Field"];
    }
}

if (!in_array("receiver_id", $receiver_columns) && in_array("id", $receiver_columns)) {
    $receiver_pk = "id";
}


/* Blood compatibility */
$blood_compatibility = [
    "O-" => ["O-"],
    "O+" => ["O-", "O+"],
    "A-" => ["O-", "A-"],
    "A+" => ["O-", "O+", "A-", "A+"],
    "B-" => ["O-", "B-"],
    "B+" => ["O-", "O+", "B-", "B+"],
    "AB-" => ["O-", "A-", "B-", "AB-"],
    "AB+" => [
        "O-", "O+", "A-", "A+",
        "B-", "B+", "AB-", "AB+"
    ]
];


/* Get receivers */
$receivers = [];

$sql = "SELECT * FROM receivers ORDER BY request_date DESC";

$result = $conn->query($sql);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $receivers[] = $row;
    }
}


/* Selected receiver */
$selected_receiver = null;

if (isset($_GET["receiver_id"])) {

    $receiver_id = intval($_GET["receiver_id"]);

    $stmt = $conn->prepare(
        "SELECT * FROM receivers WHERE $receiver_pk = ?"
    );

    $stmt->bind_param("i", $receiver_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $selected_receiver = $result->fetch_assoc();
    }

    $stmt->close();
}


/* Match donors */
$matches = [];

if ($selected_receiver) {

    $required_organ = strtolower(
        trim($selected_receiver["required_organ"])
    );

    $receiver_blood = strtoupper(
        trim($selected_receiver["blood_group"])
    );

    $receiver_location = strtolower(
        trim($selected_receiver["location"])
    );

    $sql = "SELECT * FROM donors
            WHERE LOWER(organ) = ?
            AND LOWER(availability) IN ('available', 'yes', '1')
            AND LOWER(verification_status) = 'verified'";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $required_organ);
    $stmt->execute();

    $result = $stmt->get_result();

    while ($donor = $result->fetch_assoc()) {

        $donor_blood = strtoupper(
            trim($donor["blood_group"])
        );

        if (
            !isset($blood_compatibility[$receiver_blood]) ||
            !in_array(
                $donor_blood,
                $blood_compatibility[$receiver_blood]
            )
        ) {
            continue;
        }

        $score = 40;

        /* Blood score */
        if ($donor_blood === $receiver_blood) {
            $score += 30;
            $blood_match = "Exact Match";
        } else {
            $score += 20;
            $blood_match = "Compatible";
        }

        /* Availability */
        $score += 20;
        $availability_match = "Available";

        /* Location */
        $donor_location = strtolower(
            trim($donor["location"])
        );

        if (
            $donor_location !== "" &&
            $receiver_location !== "" &&
            $donor_location === $receiver_location
        ) {
            $score += 10;
            $location_match = "Same Location";
        } else {
            $location_match = "Different Location";
        }

        $donor["match_score"] = $score;
        $donor["blood_match"] = $blood_match;
        $donor["availability_match"] = $availability_match;
        $donor["location_match"] = $location_match;

        $matches[] = $donor;
    }

    $stmt->close();

    /* Highest score first */
    usort(
        $matches,
        function ($a, $b) {
            return $b["match_score"] - $a["match_score"];
        }
    );
}


/* Save match */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        isset($_POST["save_match"]) &&
        isset($_POST["donor_id"]) &&
        isset($_POST["receiver_id"])
    ) {

        $donor_id = intval($_POST["donor_id"]);
        $receiver_id = intval($_POST["receiver_id"]);
        $score = intval($_POST["match_score"]);

        $organ = $_POST["organ"];
        $blood_group_match = $_POST["blood_group_match"];
        $location_match = $_POST["location_match"];
        $availability_match = $_POST["availability_match"];

        /* Check duplicate */
        $check = $conn->prepare(
            "SELECT match_id FROM matches
             WHERE donor_id = ?
             AND receiver_id = ?"
        );

        $check->bind_param(
            "ii",
            $donor_id,
            $receiver_id
        );

        $check->execute();

        $duplicate_result = $check->get_result();

        if ($duplicate_result->num_rows > 0) {

            $error = "This donor and receiver match is already saved.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO matches
                (
                    donor_id,
                    receiver_id,
                    organ,
                    blood_group_match,
                    location_match,
                    availability_match,
                    match_score,
                    match_status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')"
            );

            $stmt->bind_param(
                "iissssi",
                $donor_id,
                $receiver_id,
                $organ,
                $blood_group_match,
                $location_match,
                $availability_match,
                $score
            );

            if ($stmt->execute()) {

                $match_id = $conn->insert_id;

                $notification_message =
                    "New organ match saved. Match #"
                    . $match_id
                    . " is pending.";

                $notification = $conn->prepare(
                    "INSERT INTO notifications
                    (match_id, message, notification_status)
                    VALUES (?, ?, 'Unread')"
                );

                $notification->bind_param(
                    "is",
                    $match_id,
                    $notification_message
                );

                $notification->execute();
                $notification->close();

                $message =
                    "Match saved successfully! Match #"
                    . $match_id
                    . " has been created.";

            } else {

                $error =
                    "Unable to save match: "
                    . $stmt->error;
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Smart Organ Matching - LifeLink</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f5f6f8;
    color: #263238;
}

/* NAVBAR */

.navbar {
    background: #671225;
    padding: 18px 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    color: white;
    font-size: 25px;
    font-weight: bold;
    text-decoration: none;
}

.logo span {
    color: #f3b5c2;
}

.nav-links {
    display: flex;
    gap: 25px;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
}

.nav-links a:hover {
    color: #f3b5c2;
}


/* PAGE */

.container {
    width: 92%;
    max-width: 1200px;
    margin: 35px auto;
}

.page-title {
    font-size: 32px;
    color: #671225;
    margin-bottom: 8px;
}

.page-subtitle {
    color: #777;
    margin-bottom: 30px;
}


/* ALERT */

.alert-success {
    background: #e8f7ed;
    color: #26733d;
    border-left: 5px solid #35a853;
    padding: 15px 18px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.alert-error {
    background: #fdeaea;
    color: #a32020;
    border-left: 5px solid #c62828;
    padding: 15px 18px;
    border-radius: 8px;
    margin-bottom: 25px;
}


/* RECEIVER SELECT */

.receiver-section {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    margin-bottom: 30px;
}

.section-title {
    color: #671225;
    font-size: 20px;
    margin-bottom: 18px;
}

.receiver-form {
    display: flex;
    gap: 15px;
    align-items: end;
}

.form-group {
    flex: 1;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: bold;
    margin-bottom: 7px;
    color: #555;
}

.form-group select {
    width: 100%;
    padding: 13px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
}

.form-group select:focus {
    border-color: #8f1d35;
}

.btn {
    border: none;
    border-radius: 8px;
    padding: 13px 22px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
}

.btn-primary {
    background: #671225;
    color: white;
}

.btn-primary:hover {
    background: #8f1d35;
}


/* RECEIVER CARD */

.receiver-card {
    background: linear-gradient(
        135deg,
        #671225,
        #8f1d35
    );
    color: white;
    padding: 25px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.receiver-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.receiver-name {
    font-size: 24px;
    font-weight: bold;
}

.receiver-label {
    font-size: 12px;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.receiver-info {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.receiver-box {
    background: rgba(255,255,255,0.12);
    padding: 15px;
    border-radius: 10px;
}

.receiver-box small {
    display: block;
    opacity: 0.75;
    margin-bottom: 5px;
}

.receiver-box strong {
    font-size: 15px;
}


/* MATCH HEADER */

.match-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.match-title {
    font-size: 24px;
    color: #671225;
}

.match-count {
    background: #f8e9ed;
    color: #671225;
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}


/* DONOR CARD */

.donor-card {
    background: white;
    border-radius: 18px;
    padding: 25px;
    margin-bottom: 22px;
    box-shadow: 0 6px 22px rgba(0,0,0,0.07);
    border: 1px solid #eee;
}

.donor-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
}

.donor-details {
    display: flex;
    align-items: center;
    gap: 15px;
}

.donor-avatar {
    width: 58px;
    height: 58px;
    background: #f8e9ed;
    color: #671225;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 25px;
    font-weight: bold;
}

.donor-name {
    font-size: 21px;
    font-weight: bold;
    color: #263238;
}

.donor-basic {
    color: #777;
    font-size: 13px;
    margin-top: 5px;
}

.best-match {
    background: #671225;
    color: white;
    padding: 8px 13px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
}

.score {
    font-size: 25px;
    font-weight: bold;
    color: #671225;
}

.score span {
    font-size: 12px;
    color: #777;
    font-weight: normal;
}


/* INFO BOXES */

.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 22px;
}

.info-box {
    background: #f8f9fa;
    border: 1px solid #eee;
    border-radius: 11px;
    padding: 15px;
}

.info-icon {
    font-size: 20px;
    margin-bottom: 7px;
}

.info-title {
    color: #888;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    color: #263238;
    font-weight: bold;
    font-size: 14px;
    margin-top: 4px;
}

.match-good {
    color: #25833e;
}

.match-neutral {
    color: #a36a00;
}


/* DONOR FOOTER */

.donor-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top: 1px solid #eee;
    padding-top: 18px;
}

.contact-info {
    color: #777;
    font-size: 13px;
}

.save-btn {
    background: #671225;
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

.save-btn:hover {
    background: #8f1d35;
}


/* EMPTY */

.empty-box {
    background: white;
    padding: 45px;
    text-align: center;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
}

.empty-icon {
    font-size: 45px;
    margin-bottom: 15px;
}

.empty-box h3 {
    color: #671225;
    margin-bottom: 8px;
}

.empty-box p {
    color: #777;
}


/* RESPONSIVE */

@media (max-width: 900px) {

    .navbar {
        padding: 18px 25px;
    }

    .nav-links {
        gap: 12px;
    }

    .receiver-info {
        grid-template-columns: repeat(2, 1fr);
    }

    .info-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 650px) {

    .navbar {
        flex-direction: column;
        gap: 15px;
    }

    .nav-links {
        flex-wrap: wrap;
        justify-content: center;
    }

    .receiver-form {
        flex-direction: column;
        align-items: stretch;
    }

    .receiver-info {
        grid-template-columns: 1fr;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .donor-top {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .donor-footer {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

}

</style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar">

    <a href="dashboard.php" class="logo">
        Life<span>Link</span>
    </a>

    <div class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="donor_list.php">Donors</a>
        <a href="receiver_list.php">Receivers</a>
        <a href="organ_match.php">Smart Match</a>
        <a href="saved_matches.php">Saved Matches</a>
        <a href="logout.php">Logout</a>
    </div>

</nav>


<div class="container">

    <h1 class="page-title">
        Smart Organ Matching
    </h1>

    <p class="page-subtitle">
        Find suitable donors using organ, blood group, availability and location.
    </p>


    <?php if ($message != "") { ?>

        <div class="alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <?php if ($error != "") { ?>

        <div class="alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php } ?>


    <!-- RECEIVER SELECTION -->

    <div class="receiver-section">

        <div class="section-title">
            Select Receiver
        </div>

        <form method="GET" class="receiver-form">

            <div class="form-group">

                <label>
                    Receiver
                </label>

                <select
                    name="receiver_id"
                    required
                >

                    <option value="">
                        -- Select Receiver --
                    </option>

                    <?php foreach ($receivers as $receiver) { ?>

                        <option
                            value="<?php echo $receiver[$receiver_pk]; ?>"
                            <?php
                            if (
                                $selected_receiver &&
                                $selected_receiver[$receiver_pk]
                                == $receiver[$receiver_pk]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $receiver["name"]
                            );
                            ?>

                            -
                            <?php
                            echo htmlspecialchars(
                                $receiver["required_organ"]
                            );
                            ?>

                            -
                            <?php
                            echo htmlspecialchars(
                                $receiver["blood_group"]
                            );
                            ?>

                        </option>

                    <?php } ?>

                </select>

            </div>


            <button
                type="submit"
                class="btn btn-primary"
            >
                Find Smart Matches
            </button>

        </form>

    </div>


    <?php if ($selected_receiver) { ?>

        <!-- RECEIVER CARD -->

        <div class="receiver-card">

            <div class="receiver-top">

                <div>

                    <div class="receiver-label">
                        Selected Receiver
                    </div>

                    <div class="receiver-name">
                        <?php
                        echo htmlspecialchars(
                            $selected_receiver["name"]
                        );
                        ?>
                    </div>

                </div>

                <div>
                    ❤️ Receiver
                </div>

            </div>


            <div class="receiver-info">

                <div class="receiver-box">

                    <small>Age</small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $selected_receiver["age"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="receiver-box">

                    <small>Blood Group</small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $selected_receiver["blood_group"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="receiver-box">

                    <small>Required Organ</small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $selected_receiver["required_organ"]
                        );
                        ?>
                    </strong>

                </div>


                <div class="receiver-box">

                    <small>Location</small>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $selected_receiver["location"]
                        );
                        ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- MATCH HEADER -->

        <div class="match-header">

            <div class="match-title">
                Smart Match Results
            </div>

            <div class="match-count">
                <?php echo count($matches); ?> Matches Found
            </div>

        </div>


        <?php if (count($matches) > 0) { ?>


            <?php foreach ($matches as $index => $donor) { ?>

                <div class="donor-card">


                    <!-- DONOR TOP -->

                    <div class="donor-top">

                        <div class="donor-details">

                            <div class="donor-avatar">
                                <?php
                                echo strtoupper(
                                    substr(
                                        $donor["name"],
                                        0,
                                        1
                                    )
                                );
                                ?>
                            </div>


                            <div>

                                <div class="donor-name">

                                    <?php
                                    echo htmlspecialchars(
                                        $donor["name"]
                                    );
                                    ?>

                                </div>

                                <div class="donor-basic">

                                    Age:
                                    <?php
                                    echo htmlspecialchars(
                                        $donor["age"]
                                    );
                                    ?>

                                    &nbsp; | &nbsp;

                                    <?php
                                    echo htmlspecialchars(
                                        $donor["gender"]
                                    );
                                    ?>

                                    &nbsp; | &nbsp;

                                    <?php
                                    echo htmlspecialchars(
                                        $donor["location"]
                                    );
                                    ?>

                                </div>

                            </div>

                        </div>


                        <div>

                            <?php if ($index == 0) { ?>

                                <div class="best-match">
                                    ⭐ BEST MATCH
                                </div>

                            <?php } ?>

                            <div class="score">

                                <?php
                                echo $donor["match_score"];
                                ?>%

                                <span>
                                    MATCH
                                </span>

                            </div>

                        </div>

                    </div>


                    <!-- INFO BOXES -->

                    <div class="info-grid">


                        <div class="info-box">

                            <div class="info-icon">
                                🫀
                            </div>

                            <div class="info-title">
                                Organ
                            </div>

                            <div class="info-value match-good">
                                ✓ Match
                            </div>

                        </div>


                        <div class="info-box">

                            <div class="info-icon">
                                🩸
                            </div>

                            <div class="info-title">
                                Blood
                            </div>

                            <div class="info-value match-good">

                                ✓
                                <?php
                                echo htmlspecialchars(
                                    $donor["blood_match"]
                                );
                                ?>

                            </div>

                        </div>


                        <div class="info-box">

                            <div class="info-icon">
                                ✓
                            </div>

                            <div class="info-title">
                                Availability
                            </div>

                            <div class="info-value match-good">
                                Available
                            </div>

                        </div>


                        <div class="info-box">

                            <div class="info-icon">
                                📍
                            </div>

                            <div class="info-title">
                                Location
                            </div>

                            <div class="info-value">

                                <?php
                                echo htmlspecialchars(
                                    $donor["location_match"]
                                );
                                ?>

                            </div>

                        </div>


                        <div class="info-box">

                            <div class="info-icon">
                                ✓
                            </div>

                            <div class="info-title">
                                Verification
                            </div>

                            <div class="info-value match-good">
                                Verified
                            </div>

                        </div>


                        <div class="info-box">

                            <div class="info-icon">
                                🩸
                            </div>

                            <div class="info-title">
                                Donor Blood
                            </div>

                            <div class="info-value">

                                <?php
                                echo htmlspecialchars(
                                    $donor["blood_group"]
                                );
                                ?>

                            </div>

                        </div>

                    </div>


                    <!-- DONOR FOOTER -->

                    <div class="donor-footer">

                        <div class="contact-info">

                            📞
                            <?p                            &nbsphp
                            echo htmlspecialchars(
                                $donor["contact"]
                            );
                            ?>
                              &nbsp;&nbsp;

                            📍
                            <?php
                            echo htmlspecialchars(
                                $donor["location"]
                            );
                            ?>

                        </div>


                        <form method="POST">

                            <input
                                type="hidden"
                                name="donor_id"
                                value="<?php
                                echo $donor[$donor_pk];
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="receiver_id"
                                value="<?php
                                echo $selected_receiver[$receiver_pk];
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="match_score"
                                value="<?php
                                echo $donor["match_score"];
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="organ"
                                value="<?php
                                echo htmlspecialchars(
                                    $selected_receiver["required_organ"]
                                );
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="blood_group_match"
                                value="<?php
                                echo htmlspecialchars(
                                    $donor["blood_match"]
                                );
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="location_match"
                                value="<?php
                                echo htmlspecialchars(
                                    $donor["location_match"]
                                );
                                ?>"
                            >

                            <input
                                type="hidden"
                                name="availability_match"
                                value="Available"
                            >

                            <button
                                type="submit"
                                name="save_match"
                                class="save-btn"
                            >
                                🔗 Save Match
                            </button>

                        </form>

                    </div>


                </div>

            <?php } ?>


        <?php } else { ?>


            <div class="empty-box">

                <div class="empty-icon">
                    🔍
                </div>

                <h3>
                    No Suitable Donor Found
                </h3>

                <p>
                    No verified and compatible donor is currently
                    available for this receiver.
                </p>

            </div>


        <?php } ?>


    <?php } else { ?>


        <div class="empty-box">

            <div class="empty-icon">
                🫀
            </div>

            <h3>
                Select a Receiver
            </h3>

            <p>
                Select a receiver above to find the most suitable
                organ donors.
            </p>

        </div>


    <?php } ?>


</div>

</body>
</html>

