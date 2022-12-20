<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
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

        // print_r($_SESSION);     
        // print_r($_POST); 

        if (isset($_POST["login_name"])) {
            // user clicked the login button */
            if ($_POST["login_name"] == "login") {
                if (authenticate($_POST["username"], $_POST["password"], $_POST["radio"]) == 1 && first_time_login($_POST["username"]) == -1) {
                    $_SESSION["username"] = $_POST["username"];
                    $_SESSION["user_type"] = $_POST["radio"];
                    $_SESSION["old_password"] = $_POST["password"];
                    header("LOCATION:2.reset_password.php");
                    return;
                // if (1 == 0) {
                } else {
                    //check the username and passwd, if correct, redirect to main.php page
                    // if ($_POST["username"]=="Mary" && $_POST["password"] =="Hello" && $_POST["radio"] =="instructor") {
                    if (authenticate($_POST["username"], $_POST["password"], $_POST["radio"]) == 1 && $_POST["radio"] == "Instructor") {
                        $_SESSION["username"] = $_POST["username"];
                        $_SESSION["user_type"] = $_POST["radio"];
                        header("LOCATION:3.instructor_homepage.php");
                        return;
                    }
                    // if ($_POST["username"]=="Ma" && $_POST["password"] =="He" && $_POST["radio"] =="student") {
                    elseif (authenticate($_POST["username"], $_POST["password"], $_POST["radio"]) == 1 && $_POST["radio"] == "Student") {
                        $_SESSION["username"] = $_POST["username"];
                        $_SESSION["user_type"] = $_POST["radio"];
                        header("LOCATION:5.student_homepage.php");
                        return;
                    } else {
                        echo '<br>';
                        echo '<p align="center" style="color:red">Incorrect username, password or usertype. </p>';
                        echo '<p align="center" style="color:red"> Please try again!</p>';
                    }
                }
            }
            // user reset page
            if ($_POST["login_name"] == "Reset password") {
                header("LOCATION:2.reset_password.php");
                return;
            }
        }

        // user clicked the logout button */
        if (isset($_POST["logout_name"])) {
            session_destroy();
            echo '<br>';
            echo '<p align="center" style="color:red"> Thanks ' . $_SESSION["actual_username"] .
                ', for visiting the website!' . '</p>';
        }
        ?>


        <div class="main_container">
            <h1>Login</h1>
            <div class="container">
                <form method=post action=1.login.php>
                    <p style="text-align:right">Username: <input type="text" name="username" id="uname" autocomplete="uname" placeholder=username required style="width: 200px;" /> </p>
                    <p style="text-align:right">Password: <input type="password" name="password" id="paswd" autocomplete="paswd" placeholder=password required style="width: 200px;" /> </p>

                    <div class="wrapper">
                        <input type="radio" name="radio" id="opt1" value="Instructor" required>
                        <label for="opt1" class="label1">
                            <span>Instructor</span>
                        </label>
                        <input type="radio" name="radio" id="opt2" value="Student">
                        <label for="opt2" class="label2">
                            <span>Student</span>
                        </label>
                    </div>

                    <br>
                    <input type="submit" value="login" name="login_name" />
                </form>
            </div>
        </div>
    </main>
</body>

</html>