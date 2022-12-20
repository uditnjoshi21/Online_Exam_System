<?php
require "db.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instructor Operations</title>
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
                    echo '<strong align="center">Instructor (' . $_SESSION["actual_username"] . ') > ' . $_POST["ins_option"] . '</strong>';
                }
                ?>
            </div>
        </header>


        <div class="main_container">
            <form method=post action=3.instructor_homepage.php>
                <p align="center"> <input type="submit" value="Go Back" name="homepage" style="font-size: 1em;"> </p>
            </form>
            <br>

            <div class="exam_box">
                <?php
                if (!isset($_SESSION["username"])) {
                    header("LOCATION:1.login.php");
                }

                if ($_POST["ins_option"] == "Check Score") {
                    echo '<br';
                    $ins_check_sc = instructor_check_scores($_SESSION['username'], $_POST["course_id"], $_POST["exam_name"]);
                    if (empty($ins_check_sc)) {
                        echo '<p style="text-align: center;">There is no such exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                            '</strong> created by you(<strong>' . fetch_name($_SESSION["username"]) . '</strong>). <br> </p>';
                    } else {
                        // echo '<p style="text-align: center;"> <strong> Student exam statisitics </strong> </p>';
                        echo '<strong align="center"> Student exam statisitics </strong>';
                ?>
                        <table align="center">
                            <tr>
                                <th>course_id</th>
                                <th>total student</th>
                                <th>exam_name</th>
                                <th>exam completed by</th>
                                <th>minimum score</th>
                                <th>maximum score</th>
                                <th>average score</th>
                            </tr>

                            <?php
                            foreach ($ins_check_sc as $row) {
                                echo "<tr>";
                                echo "<td >" . $row[0] . "</td>";
                                echo "<td >" . $row[1] . "</td>";
                                echo "<td >" . $row[2] . "</td>";
                                echo "<td >" . $row[3] . "</td>";
                                echo "<td >" . $row[4] . "</td>";
                                echo "<td >" . $row[5] . "</td>";
                                echo "<td >" . $row[6] . "</td>";
                                echo "</tr>";
                                echo "<table>";
                            }

                            echo '<br><br>';
                            $ins_check_sc = instructor_CS_individual_students($_SESSION['username'], $_POST["course_id"], $_POST["exam_name"]);
                            if (empty($ins_check_sc)) {
                                echo '<p style="text-align: center;">There are no student who took the <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                                    '</strong> exam. <br> </p>';
                            } else {
                                echo '<strong align="center" >Individual Student exam submission details </strong>';
                            ?>

                                <table align="center">
                                    <tr>
                                        <th>student_id</th>
                                        <th>name</th>
                                        <th>start_timestamp</th>
                                        <th>finish_timestamp</th>
                                        <th>score</th>
                                    </tr>

                        <?php
                                $ins_check_sc = instructor_CS_individual_students($_SESSION['username'], $_POST["course_id"], $_POST["exam_name"]);
                                foreach ($ins_check_sc as $row) {
                                    echo "<tr>";
                                    echo "<td >" . $row[0] . "</td>";
                                    echo "<td >" . $row[1] . "</td>";
                                    echo "<td >" . $row[2] . "</td>";
                                    echo "<td >" . $row[3] . "</td>";
                                    echo "<td >" . $row[4] . "</td>";
                                    echo "</tr>";
                                }
                                echo "<table>";
                            }
                        }
                    }
                        ?>


                        <?php
                        if ($_POST["ins_option"] == "Review Exam") {
                            echo '<br';
                            $course_exam_id = fetch_course_exam_id($_SESSION["username"], $_POST["course_id"], $_POST["exam_name"]);
                            if (empty($course_exam_id)) {
                                echo '<p style="text-align: center;">There is no such exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                                    '</strong> created by you(<strong>' . fetch_name($_SESSION["username"]) . '</strong>). <br> </p>';
                            } else {
                                echo '<div style="text-align: left; justify-content: left;" class="review_exam_box">';
                                echo '<p style="text-align: center;">Here are the questions for <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                                    '</strong> created by you(<strong>' . fetch_name($_SESSION["username"]) . '</strong>). <br> </p>';
                                // echo $course_exam_id . '<br>';
                                echo '<br>';
                                $questions = fetch_each_questions($course_exam_id);
                                foreach ($questions as $ques) {
                                    echo $ques[0] . '<br>';
                                    // echo $ques[1] . '<br><br>';
                                    foreach (fetch_each_options_by_question_id($ques[1]) as $option) {
                                        echo $option[1] . '<br>';
                                    }
                                    echo '<br><br>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>



                        <?php
                        if ($_POST["ins_option"] == "Create Exam") {
                            echo '<br>';
                            echo '<p align="center" style="color:red">This function is not implemented yet! </p>';
                        }
                        ?>
            </div>
        </div>
    </main>
</body>

</html>