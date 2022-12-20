<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Password</title>
    <link rel="stylesheet" type="text/css" href="common.css">
</head>

<body>
    <main style="margin-left: 0;align-items: center;">
        <header class="login_header">
            <img style="height: 45px; width: 275px;" src="images\header_logo.png" alt="Logo">
            <div class="login_header_container">
                <strong>Welcome to MTU!</strong>
            </div>
        </header>

        <?php
        require "db.php";
        session_start();
        $_SESSION["actual_username"] = fetch_name($_SESSION["username"]);

        // user clicked the update password button */
        if (isset($_POST["update_paswd"])) {
            update_password($_SESSION["username"], $_POST["new_paswd"]);
            $_SESSION["update_paswd"] = true;
            echo '<br>';
            echo '<p align="center" style="color:red"> Thanks ' . $_SESSION["actual_username"] .
                ', for updating the password!' . '</p>';
            echo '<p align="center" style="color:red"> Please login again!</p>';

            if ($_SESSION["user_type"] == "Student") {
                echo '<p align="center" style="color:red"> Password got updated!</p>';
                header("LOCATION:5.student_homepage.php");
                return;
            } else {
                echo '<p align="center" style="color:red"> Password got updated!</p>';
                header("LOCATION:3.instructor_homepage.php");
                return;
            }
        }
        ?>


        <div class="main_container">
            <?php
            echo '<h3 align="center" style="color:red"> Dear ' . $_SESSION["user_type"] . ' ' . $_SESSION["actual_username"] .
                ', please update the password!' . '</h3>';
            ?>

            <form method=post action=1.login.php>
                <p align="center"> <input type="submit" value="Go back to Login page" name="transfer_to_login_page"> </p>
            </form>
            <br>
            <div class="container">
                <form method=post action=2.reset_password.php>
                    <!-- <p style="text-align:right">Old Password: <input type="text" name="old_paswd" id="old_paswd" placeholder='old password' required style="width: 200px;" /> </p> -->
                    <p style="text-align:right">New Password: <input type="text" name="new_paswd" id="new_paswd" placeholder='new password' required style="width: 200px;" /> </p>
                    <br>
                    <input type="submit" value="Update Password" name="update_paswd" />
                </form>
            </div>
        </div>
    </main>
</body>

</html>