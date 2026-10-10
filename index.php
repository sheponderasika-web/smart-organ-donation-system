<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>LifeLink | Organ Donation Management System</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8f6f7;
            color: #292929;
        }

        /* ================= NAVBAR ================= */

        .navbar {
            height: 76px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            border-bottom: 1px solid #eee5e7;
            box-shadow: 0 3px 15px rgba(60, 20, 30, 0.06);
            position: relative;
            z-index: 10;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #8f1d35;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-icon {
            width: 43px;
            height: 43px;
            background: #f8e9ed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 21px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 27px;
        }

        .nav-links a {
            text-decoration: none;
            color: #454545;
            font-size: 14px;
            font-weight: 600;
            transition: 0.25s;
        }

        .nav-links a:hover {
            color: #8f1d35;
        }

        .admin-login {
            background: #8f1d35;
            color: #ffffff !important;
            padding: 11px 19px;
            border-radius: 6px;
        }

        .admin-login:hover {
            background: #701529 !important;
        }


        /* ================= HERO ================= */

        .hero {
            min-height: calc(100vh - 76px);

            background:
                radial-gradient(
                    circle at 85% 20%,
                    rgba(255,255,255,0.12),
                    transparent 27%
                ),
                linear-gradient(
                    125deg,
                    #671225 0%,
                    #8f1d35 50%,
                    #ad3b52 100%
                );

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 60px 7%;

            color: white;
            overflow: hidden;
        }

        .hero-content {
            width: 57%;
            max-width: 700px;
        }

        .hero-label {
            display: inline-block;

            padding: 9px 16px;

            border: 1px solid rgba(255,255,255,0.25);
            background: rgba(255,255,255,0.10);

            border-radius: 30px;

            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;

            margin-bottom: 22px;
        }

        .hero h1 {
            font-size: 54px;
            line-height: 1.12;
            font-weight: 700;

            margin-bottom: 22px;
        }

        .hero h1 span {
            color: #f7dce2;
        }

        .hero-description {
            font-size: 17px;
            line-height: 1.8;

            color: #f8edef;

            max-width: 640px;

            margin-bottom: 31px;
        }

        .hero-buttons {
            display: flex;
            gap: 13px;
            flex-wrap: wrap;
        }

        .hero-button {
            display: inline-block;

            padding: 14px 23px;

            border-radius: 7px;

            text-decoration: none;

            font-size: 14px;
            font-weight: bold;

            transition: 0.25s;
        }

        .donor-button {
            background: #ffffff;
            color: #8f1d35;
        }

        .receiver-button {
            color: #ffffff;

            border: 1px solid rgba(255,255,255,0.75);

            background: rgba(255,255,255,0.05);
        }

        .hero-button:hover {
            transform: translateY(-2px);
        }


        /* ================= HERO VISUAL ================= */

        .hero-visual {
            width: 43%;

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .visual-card {
            width: 330px;
            height: 330px;

            border-radius: 50%;

            background: rgba(255,255,255,0.08);

            border: 1px solid rgba(255,255,255,0.20);

            display: flex;
            align-items: center;
            justify-content: center;

            box-shadow:
                0 25px 70px rgba(0,0,0,0.16);
        }

        .visual-inner {
            width: 235px;
            height: 235px;

            border-radius: 50%;

            background: rgba(255,255,255,0.10);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .heart {
            font-size: 105px;

            filter:
                drop-shadow(
                    0 10px 15px rgba(0,0,0,0.18)
                );
        }


        /* ================= BOTTOM INFO ================= */

        .bottom-info {
            position: absolute;

            left: 7%;
            right: 7%;
            bottom: 25px;

            display: grid;
            grid-template-columns: repeat(3, 1fr);

            gap: 16px;
        }

        .info {
            background: rgba(255,255,255,0.96);

            border-radius: 7px;

            padding: 15px 18px;

            color: #343434;

            box-shadow:
                0 8px 25px rgba(40,10,20,0.12);
        }

        .info strong {
            display: block;

            color: #8f1d35;

            font-size: 14px;

            margin-bottom: 4px;
        }

        .info span {
            font-size: 12px;

            color: #777;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 950px) {

            .navbar {
                height: auto;

                padding: 15px 5%;

                flex-direction: column;

                gap: 14px;
            }

            .nav-links {
                flex-wrap: wrap;

                justify-content: center;

                gap: 13px;
            }

            .hero {
                min-height: calc(100vh - 125px);

                flex-direction: column;

                justify-content: center;

                text-align: center;

                padding: 50px 5%;

                gap: 45px;
            }

            .hero-content {
                width: 100%;
            }

            .hero-visual {
                width: 100%;
            }

            .hero h1 {
                font-size: 40px;
            }

            .hero-description {
                margin-left: auto;
                margin-right: auto;
            }

            .hero-buttons {
                justify-content: center;
            }

            .visual-card {
                width: 220px;
                height: 220px;
            }

            .visual-inner {
                width: 155px;
                height: 155px;
            }

            .heart {
                font-size: 70px;
            }

            .bottom-info {
                display: none;
            }
        }


        @media (max-width: 600px) {

            .logo {
                font-size: 20px;
            }

            .hero h1 {
                font-size: 34px;
            }

            .hero-description {
                font-size: 15px;
            }

            .nav-links a {
                font-size: 13px;
            }

        }

    </style>

</head>

<body>


<!-- ================= NAVBAR ================= -->

<nav class="navbar">

    <div class="logo">

        <div class="logo-icon">
            ❤️
        </div>

        LifeLink

    </div>


    <div class="nav-links">

        <a href="index.php">
            Home
        </a>

        <a href="about.php">
            About
        </a>

        <a href="donor_public_register.php">
            Donor Registration
        </a>

        <a href="receiver_public_register.php">
            Receiver Registration
        </a>

        <a href="login.php" class="admin-login">
            Admin Login
        </a>

    </div>

</nav>


<!-- ================= HERO ================= -->

<section class="hero">


    <div class="hero-content">


        <div class="hero-label">

            ORGAN DONATION MANAGEMENT SYSTEM

        </div>


        <h1>

            Give the Gift of
            <span>Life.</span>

        </h1>


        <p class="hero-description">

            LifeLink provides a simple and organized platform for
            connecting organ donors, receivers and healthcare
            information. Register your details and become a part
            of a system built around hope, care and life.

        </p>


        <div class="hero-buttons">


            <a
                href="donor_public_register.php"
                class="hero-button donor-button"
            >

                ❤️ Register as Donor

            </a>


            <a
                href="receiver_public_register.php"
                class="hero-button receiver-button"
            >

                🫶 Register as Receiver

            </a>


        </div>


    </div>


    <!-- RIGHT SIDE VISUAL -->

    <div class="hero-visual">

        <div class="visual-card">

            <div class="visual-inner">

                <div class="heart">
                    ❤️
                </div>

            </div>

        </div>

    </div>


</section>


<!-- ================= QUICK INFO ================= -->

<div class="bottom-info">


    <div class="info">

        <strong>
            ❤️ Organ Donor
        </strong>

        <span>
            Register your donation information.
        </span>

    </div>


    <div class="info">

        <strong>
            🫶 Organ Receiver
        </strong>

        <span>
            Register your organ requirement.
        </span>

    </div>


    <div class="info">

        <strong>
            🏥 Healthcare Support
        </strong>

        <span>
            Organized donor and receiver management.
        </span>

    </div>


</div>


</body>
</html>