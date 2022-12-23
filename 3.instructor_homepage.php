<?php
require "db.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instructor Homepage</title>
    <link rel="stylesheet" type="text/css" href="common.css">
</head>

<body>
    <nav>
        <img style="height: 170px;" src="images\collage_logo.png" alt="Logo">
        <form method=post action=3.instructor_homepage.php>
            <p> <input type="submit" value="Home" name="Home"> </p>
        </form>
        <form method=post action=3.instructor_homepage.php>
            <p align="center"> <input type="submit" value="Profile" name="profile"> </p>
        </form>
        <form method=post action=2.reset_password.php>
            <p align="center"> <input type="submit" value="Update Password" name="update_passward"> </p>
        </form>
        <!-- Logout function, directed to login page -->
        <form method=post action=1.login.php>
            <p align="center"> <input type="submit" value="Logout" name="logout_name"> </p>
        </form>
    </nav>


    <main>
        <header>
            <img style="height: 30px; width: 200px;" src="images\header_logo.png" alt="Logo">
            <div class="header_container">
                <?php
                if (!isset($_SESSION["username"])) {
                    header("LOCATION:1.login.php");
                } else {
                    $_SESSION["actual_username"] = fetch_name($_SESSION["username"]);
                    echo '<strong align="center"> Dear Instructor ' . $_SESSION["actual_username"] . ', Welcome!' . '</strong>';
                }
                ?>
            </div>
        </header>


        <div class="main_container">
            <?php
            if (isset($_POST["profile"])) {
            ?>
                <br>
                <form method=post action=3.instructor_homepage.php>
                    <p align="center"> <input type="submit" value="Go Back" name="homepage" style="font-size: 1em;"> </p>
                </form>
                <?php
                echo '<br>';
                echo '<p align="center" style="color:red;font-size: 1.25em;">Your profile details! </p>';
                ?>
                <table align="center">
                    <tr>
                        <th>id</th>
                        <th>account_id</th>
                        <th>name</th>
                        <th>email_id</th>
                        <th>dept_name</th>
                    </tr>

                    <?php
                    $user_profile = profile($_SESSION['username'], $_SESSION["user_type"]);
                    foreach ($user_profile as $row) {
                        echo "<tr>";
                        echo "<td >" . $row[0] . "</td>";
                        echo "<td >" . $row[1] . "</td>";
                        echo "<td >" . $row[2] . "</td>";
                        echo "<td >" . $row[3] . "</td>";
                        echo "<td >" . $row[4] . "</td>";
                        echo "</tr>";
                    }
                    echo '</table>';
                } else {
                    $ins_course = get_instructor_courses($_SESSION['username']);
                    if (empty($ins_course)) {
                        echo '<p style="text-align: center;">There is no course assign to you. <br> </p>';
                    } else {
                    ?>
                        <br>
                        <p> Here are the courses that you are teaching! </p>
                        <table align="center">
                            <tr>
                                <th>course_id</th>
                                <th>title</th>
                                <th>credit</th>
                            </tr>

                            <?php
                            foreach ($ins_course as $row) {
                                echo "<tr>";
                                echo "<td >" . $row[0] . "</td>";
                                echo "<td >" . $row[1] . "</td>";
                                echo "<td >" . $row[2] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<br> <br>";

                            $ins_course = get_instructor_course_exams($_SESSION['username']);
                            if (empty($ins_course)) {
                                echo '<p style="text-align: center;">There are no exams created to you. <br> </p>';
                            } else {
                            ?>
                                <p> The exams that you have created for each courses! </p>
                                <table align="center">
                                    <tr>
                                        <th>course_id</th>
                                        <th>exam_name</th>
                                        <th>opening_date</th>
                                        <th>closing_date</th>
                                        <th>total_points</th>
                                        <th>creation_timestamp</th>
                                    </tr>

                                <?php
                                foreach ($ins_course as $row) {
                                    echo "<tr>";
                                    echo "<td >" . $row[0] . "</td>";
                                    echo "<td >" . $row[1] . "</td>";
                                    echo "<td >" . $row[2] . "</td>";
                                    echo "<td >" . $row[3] . "</td>";
                                    echo "<td >" . $row[4] . "</td>";
                                    echo "<td >" . $row[5] . "</td>";
                                    echo "</tr>";
                                }
                            }
                                ?>
                                </table>

                                <br><br>
                                <P align="center"> Please enter the course id and the exam name to perform the below operations. </p>
                                <div class="container">
                                    <form method=post action=4.instructor_operations.php>
                                        <p style="text-align:right">Course id: <input type="text" name="course_id" placeholder='Course id' required style="width:200px;" /> </p>
                                        <p style="text-align:right">Exam name: <input type="text" name="exam_name" placeholder='Exam name' required style="width:200px;" /> </p>

                                        <input type="submit" value="Check Score" name="ins_option" />
                                        <input type="submit" value="Review Exam" name="ins_option" />
                                        <input type="submit" value="Create Exam" name="ins_option" />
                                    </form>
                                </div>
                        <?php
                    }
                }
                        ?>
        </div>

    </main>
</body>

</html>