<?php
require "db.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Homepage</title>
    <link rel="stylesheet" type="text/css" href="common.css">
</head>

<body>
    <nav>
        <img style="height: 170px;" src="images\collage_logo.png" alt="Logo">
        <form method=post action=5.student_homepage.php>
            <p> <input type="submit" value="Home" name="Home"> </p>
        </form>
        <form method=post action=5.student_homepage.php>
            <p align="center"> <input type="submit" value="Profile" name="profile"> </p>
        </form>
        <form method=post action=2.reset_password.php>
            <p align="center"> <input type="submit" value="Update Password" name="update_passward"> </p>
        </form>
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
                    $_SESSION["Student_id"] = fetch_std_id($_SESSION["username"]);
                    echo '<strong align="center"> Dear Student ' . $_SESSION["actual_username"] . ', Welcome!' . '</strong>';
                }
                ?>
            </div>
        </header>


        <div class="main_container">
            <?php
            if (isset($_POST["profile"])) {
            ?>
                <br>
                <form method=post action=5.student_homepage.php>
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
                    $std_course = get_std_courses($_SESSION["Student_id"]);
                    if (empty($std_course)) {
                        echo '<p style="text-align: center;">There is no course that you have registered. <br> </p>';
                    } else {

                    ?>
                        <br>
                        <p> Here are the courses that you are taking! </p>
                        <table align="center">
                            <tr>
                                <th>course_id</th>
                                <th>title</th>
                                <th>credit</th>
                                <th>Instructor name</th>
                            </tr>

                            <?php
                            foreach ($std_course as $row) {
                                echo "<tr>";
                                echo "<td >" . $row[0] . "</td>";
                                echo "<td >" . $row[1] . "</td>";
                                echo "<td >" . $row[2] . "</td>";
                                echo "<td >" . $row[3] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<br> <br>";

                            $std_course = get_std_exam_scores($_SESSION["Student_id"]);
                            if (empty($std_course)) {
                                echo '<p style="text-align: center;">There are no exams in the courses that you are taking. <br> </p>';
                            } else {
                            ?>


                                <p> Here are the exams in each course and your score! </p>
                                <table align="center">
                                    <tr>
                                        <th>course_id</th>
                                        <th>exam_name</th>
                                        <th>exam opening_date</th>
                                        <th>exam closing_date</th>
                                        <th>total_points</th>
                                        <th>student start_timestamp</th>
                                        <th>student finish_timestamp</th>
                                        <th>score</th>
                                    </tr>

                            <?php
                                foreach ($std_course as $row) {
                                    echo "<tr>";
                                    echo "<td >" . $row[0] . "</td>";
                                    echo "<td >" . $row[1] . "</td>";
                                    echo "<td >" . $row[2] . "</td>";
                                    echo "<td >" . $row[3] . "</td>";
                                    echo "<td >" . $row[4] . "</td>";
                                    echo "<td >" . $row[5] . "</td>";
                                    echo "<td >" . $row[6] . "</td>";
                                    echo "<td >" . $row[7] . "</td>";
                                    echo "</tr>";
                                }
                                echo "</table>";
                            }
                        }
                            ?>



                            <br><br>
                            <p> Here is the list of classes that you are not enrolled in yet! </p>
                            <table align="center">
                                <tr>
                                    <th>course_id</th>
                                    <th>title</th>
                                    <th>credit</th>
                                    <th>instructor_id</th>
                                </tr>

                                <?php
                                $ins_course = get_std_not_registerd_courses($_SESSION["Student_id"]);
                                foreach ($ins_course as $row) {
                                    echo "<tr>";
                                    echo "<td >" . $row[0] . "</td>";
                                    echo "<td >" . $row[1] . "</td>";
                                    echo "<td >" . $row[2] . "</td>";
                                    echo "<td >" . $row[3] . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>


                            <br><br>
                            <P align="center"> To register new course, please type the course_id, then click the 'Register New Course' button. </p>
                            <div class="container">
                                <form method=post action=6.student_operations.php>
                                    <p style="text-align:right">Course id: <input type="text" name="course_id" placeholder='Course id' required style="width:200px;" /> </p>

                                    <input type="submit" value="Register New Course" name="register_course" />
                                </form>
                            </div>

                            <br><br><br>

                            <?php
                            if (!empty($std_course)) {
                                //     echo '<p style="text-align: center;">There are no exams in the courses that you are taking. <br> </p>';
                                // } else {
                            ?>

                                <P align="center"> Please enter the course id and the exam name to perform the below operations. </p>
                                <P align="center"> For taking the exam, click the 'Take Exam' button. </p>
                                <P align="center"> For checking the exam score, click the 'Check Score' button. </p>

                                <div class="container">
                                    <form method=post action=6.student_operations.php>
                                        <p style="text-align:right">Course id: <input type="text" name="course_id" placeholder='Course id' required style="width:200px;" /> </p>
                                        <p style="text-align:right">Exam name: <input type="text" name="exam_name" placeholder='Exam name' required style="width:200px;" /> </p>


                                        <input type="submit" value="Take Exam" name="std_option" />
                                        <input type="submit" value="Check Score" name="std_option" />
                                    </form>

                                </div>
                                <br><br><br><br>
                        <?php
                            }
                        }
                        ?>
        </div>

    </main>
</body>

</html>