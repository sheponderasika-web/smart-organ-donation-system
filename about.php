<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>About | LifeLink</title>

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

/* NAVBAR */

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
    color: white;
    text-decoration: none;
    font-size: 14px;
}

.nav-links a:hover {
    color: #f5b8c4;
}

.nav-links .active {
    color: #f5b8c4;
    font-weight: bold;
}

/* MAIN */

.page {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 35px 20px;
}

.about-card {
    width: 100%;
    max-width: 1050px;
    background: white;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(103,18,37,0.13);
}

/* TOP */

.about-top {
    background: linear-gradient(135deg, #671225, #8f1d35);
    color: white;
    padding: 42px 50px;
    display: flex;
    align-items: center;
    gap: 30px;
}

.icon {
    width: 85px;
    height: 85px;
    border-radius: 22px;
    background: rgba(255,255,255,0.13);
    border: 1px solid rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 42px;
}

.about-top h1 {
    margin: 0 0 10px;
    font-size: 34px;
}

.about-top p {
    margin: 0;
    color: #f7dfe4;
    line-height: 1.6;
    font-size: 15px;
}

/* CONTENT */

.content {
    padding: 38px 50px 45px;
}

.section {
    margin-bottom: 30px;
}

.section h2 {
    color: #671225;
    margin: 0 0 10px;
    font-size: 21px;
}

.section p {
    color: #666;
    line-height: 1.7;
    margin: 0;
}

/* FEATURES */

.features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-top: 25px;
}

.feature {
    background: #faf4f6;
    border: 1px solid #f0dfe3;
    border-radius: 14px;
    padding: 23px;
}

.feature-icon {
    font-size: 28px;
    margin-bottom: 12px;
}

.feature h3 {
    margin: 0 0 8px;
    color: #671225;
    font-size: 16px;
}

.feature p {
    font-size: 13px;
    line-height: 1.6;
    color: #777;
    margin: 0;
}

/* FLOW */

.flow {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-top: 20px;
}

.flow-item {
    text-align: center;
    padding: 18px;
}

.number {
    width: 42px;
    height: 42px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: #8f1d35;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.flow-item h3 {
    margin: 5px 0;
    color: #671225;
    font-size: 15px;
}

.flow-item p {
    color: #777;
    font-size: 12px;
    line-height: 1.5;
}

/* BUTTONS */

.actions {
    text-align: center;
    margin-top: 15px;
}

.btn {
    display: inline-block;
    text-decoration: none;
    padding: 13px 25px;
    border-radius: 9px;
    font-size: 14px;
    font-weight: bold;
    margin: 5px;
}

.btn-primary {
    background: #8f1d35;
    color: white;
}

.btn-primary:hover {
    background: #671225;
}

.btn-light {
    background: #f8e9ed;
    color: #671225;
}

/* MOBILE */

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

    .about-top {
        padding: 30px 25px;
        flex-direction: column;
        text-align: center;
    }

    .about-top h1 {
        font-size: 27px;
    }

    .content {
        padding: 30px 22px;
    }

    .features,
    .flow {
        grid-template-columns: 1fr;
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

        <a href="index.php">
            Home
        </a>

        <a href="about.php" class="active">
            About
        </a>

        <a href="donor_public_register.php">
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


<!-- ABOUT PAGE -->

<div class="page">

    <div class="about-card">

        <!-- HEADER -->

        <div class="about-top">

            <div class="icon">
                ❤️
            </div>

            <div>

                <h1>About LifeLink</h1>

                <p>
                    A simple and organized platform designed to manage
                    organ donation information and support the connection
                    between donors, receivers and healthcare facilities.
                </p>

            </div>

        </div>


        <!-- CONTENT -->

        <div class="content">

            <div class="section">

                <h2>Our Purpose</h2>

                <p>
                    LifeLink is an Organ Donation Management System
                    created to organize donor and receiver information
                    in one place. The system helps administrators manage
                    registrations, hospitals, matching information and
                    the progress of organ donation requests.
                </p>

            </div>


            <div class="section">

                <h2>What LifeLink Provides</h2>

                <div class="features">

                    <div class="feature">

                        <div class="feature-icon">
                            🫀
                        </div>

                        <h3>Donor Registration</h3>

                        <p>
                            Individuals can register their details
                            and provide information about the organ
                            they are willing to donate.
                        </p>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            🏥
                        </div>

                        <h3>Receiver Registration</h3>

                        <p>
                            Receivers can submit their requirements,
                            hospital details and urgency information
                            for administrative review.
                        </p>

                    </div>


                    <div class="feature">

                        <div class="feature-icon">
                            🔗
                        </div>

                        <h3>Organ Matching</h3>

                        <p>
                            The system helps administrators compare
                            donor and receiver information and manage
                            potential matches.
                        </p>

                    </div>

                </div>

            </div>


            <div class="section">

                <h2>How the System Works</h2>

                <div class="flow">

                    <div class="flow-item">

                        <div class="number">
                            1
                        </div>

                        <h3>Register</h3>

                        <p>
                            Donors and receivers submit
                            their information.
                        </p>

                    </div>


                    <div class="flow-item">

                        <div class="number">
                            2
                        </div>

                        <h3>Review</h3>

                        <p>
                            Administrators review and
                            manage submitted information.
                        </p>

                    </div>


                    <div class="flow-item">

                        <div class="number">
                            3
                        </div>

                        <h3>Match & Manage</h3>

                        <p>
                            Potential matches and their
                            progress are managed through
                            the admin system.
                        </p>

                    </div>

                </div>

            </div>


            <div class="actions">

                <a href="donor_public_register.php"
                   class="btn btn-primary">
                    Become a Donor
                </a>

                <a href="receiver_public_register.php"
                   class="btn btn-light">
                    Register as Receiver
                </a>

            </div>

        </div>

    </div>

</div>

</body>

</html>