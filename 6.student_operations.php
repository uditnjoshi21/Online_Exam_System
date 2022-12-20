<?php
require "db.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Operations</title>
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
                    if (isset($_POST["std_option"])) {
                        echo '<strong align="center">Student (' . $_SESSION["actual_username"] . ') > ' . $_POST["std_option"] . '</strong>';
                    } elseif (isset($_POST["register_course"])) {
                        echo '<strong align="center">Student (' . $_SESSION["actual_username"] . ') > ' . $_POST["register_course"] . '</strong>';
                    }
                }
                ?>
            </div>
        </header>


        <div class="main_container">
            <?php
            if (isset($_POST["std_option"])) {
            ?>
                <form method=post action=5.student_homepage.php>
                    <p align="center"> <input type="submit" value="Go Back" name="homepage" style="font-size: 1em;"> </p>
                </form>
                <br>
                <div style="text-align: left; justify-content: left;" class="exam_box">
                    <?php
                    if (!isset($_SESSION["username"])) {
                        header("LOCATION:1.login.php");
                    }


                    if ($_POST["std_option"] == "Check Score") {
                        // echo "<div>";
                        echo '<br>';

                        $std_check_sc = std_check_scores($_SESSION["Student_id"], $_POST["course_id"], $_POST["exam_name"]);
                        if (empty($std_check_sc)) {
                            echo '<p style="text-align: center;">May be there is no such exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] . '</strong> or you have not taken it. <br> </p>';
                        } else {
                            echo '<p align="center" style="color:red">Exam statisitics </p>';
                    ?>
                            <table align="center">
                                <tr>
                                    <th>total score</th>
                                    <th>out of</th>
                                    <th>percentage</th>
                                    <th>start_timestamp</th>
                                    <th>finish_timestamp</th>
                                    <th>duration_in_sec</th>
                                </tr>

                                <?php
                                foreach ($std_check_sc as $row) {
                                    echo "<tr>";
                                    echo "<td >" . $row[0] . "</td>";
                                    echo "<td >" . $row[1] . "</td>";
                                    echo "<td >" . $row[2] . "</td>";
                                    echo "<td >" . $row[3] . "</td>";
                                    echo "<td >" . $row[4] . "</td>";
                                    echo "<td >" . $row[5] . "</td>";
                                    echo "</tr>";
                                    echo "<table>";
                                    // echo "</div>";
                                }


                                echo "<div>";
                                echo '<br><br>';
                                echo '<p align="center" style="color:red">Individual question details </p>';
                                ?>

                                <table align="center">
                                    <tr>
                                        <th>question_no</th>
                                        <th>question_description</th>
                                        <th>your_answer</th>
                                        <th>your_point</th>
                                        <th>your_decision</th>
                                        <th>correct_answer</th>
                                    </tr>

                            <?php
                            $ins_check_sc = std_check_each_question_ans($_SESSION['Student_id'], $_POST["course_id"], $_POST["exam_name"]);
                            foreach ($ins_check_sc as $row) {
                                echo "<tr>";
                                echo "<td >" . $row[0] . "</td>";
                                echo "<td >" . $row[1] . "</td>";
                                echo "<td >" . $row[2] . "</td>";
                                echo "<td >" . $row[3] . "</td>";
                                echo "<td >" . $row[4] . "</td>";
                                echo "<td >" . $row[5] . "</td>";
                                echo "</tr>";
                            }
                            echo "<table>";
                            echo "</div>";
                        }
                    }
                            ?>



                        <?php
                        if ($_POST["std_option"] == "Take Exam") {
                            echo '<br';
                            $_SESSION["course_id"] = $_POST["course_id"];
                            $_SESSION["exam_name"] = $_POST["exam_name"];
                            $course_exam_id = fetch_course_exam_id__if_student_takes_it($_SESSION['Student_id'], $_POST["course_id"], $_POST["exam_name"]);
                            $_SESSION["course_exam_id"] = $course_exam_id;
                            if (empty($course_exam_id)) {
                                echo '<p style="text-align: center;">May be there is no such exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                                    '</strong> present or you have been not registered for this course. <br> </p>';
                            } elseif (empty(check_if_exam_window_is_open_or_not__in__Exams_table($_SESSION["course_exam_id"]))) {
                                echo '<p align="center"> Dear Student <strong> ' . $_SESSION["actual_username"] . '</strong>, the 
                                        exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] . '</strong> is not opened yet or already closed.<br> </p>';
                            } elseif (!empty(check_the_student_id__in__takes_table($_SESSION['Student_id'], $_SESSION["course_exam_id"]))) {
                                echo '<p align="center"> Dear Student <strong> ' . $_SESSION["actual_username"] . '</strong>, you have already 
                                    taken this exam, <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] . '</strong>.<br> </p>';
                            }


                            // TODO                             
                            else {
                                insert_into_takes_table($_SESSION['Student_id'], $_SESSION["course_exam_id"]);


                                echo '<p style="text-align: center;">Here are the questions for <strong> ' . $_POST["course_id"] . ' ' . $_POST["exam_name"] .
                                    '</strong>. <br> </p>';
                                echo '<p style="text-align: center;"> (Click submit after you finish) <br> </p>';
                                echo '<br>';
                                echo '<div style="text-align: left; justify-content: left;" class="exam_question_box">';
                                echo '<form method=post action=6.student_operations.php>';
                                $questions = fetch_each_questions($course_exam_id);
                                foreach ($questions as $ques) {
                                    echo $ques[0] . '<br>';


                                    // echo $ques[1] . '<br><br>';
                                    foreach (for_student_taking_exam__fetch_each_options_by_question_id($ques[1]) as $option) {
                                        // echo '<input type="radio" name="' . $ques[1] . '" id="' . $option[0] . '" value= "' . $option[0] . '" required> ';
                                        // echo '<label for="' . $option[0] . '"
                                        //         <span>' . $option[1] . '</span>
                                        //       </label>' . '<br>';
                                        echo '<input type="radio" name="' . $ques[1] . '" value= "' . $option[0] . '" required> ';
                                        echo $option[1] . '<br>';
                                    }
                                    echo '<br><br>';
                                }
                                echo '<p align="center"> <input type="submit" value="Submit" name="submit_button" style="font-weight: bold;font-size: 1em;"/></p>';
                                echo '</form>';
                                echo '</div>';
                            }
                        }
                    }

                        ?>


                        <?php
                        if (isset($_POST["register_course"])) {
                        ?>
                            <form method=post action=5.student_homepage.php>
                                <p align="center"> <input type="submit" value="Go Back" name="homepage" style="font-size: 1em;"> </p>
                            </form>
                            <br>
                        <?php
                            if (!isset($_SESSION["username"])) {
                                header("LOCATION:1.login.php");
                            }
                            if ($_POST["register_course"] == "Register New Course") {
                                if (empty(check_if_course_present__in__course_table($_POST["course_id"]))) {
                                    echo '<p align="center">There is no such course, <strong> ' . $_POST["course_id"] .
                                        '</strong> presents. Please check the course_id again.<br> </p>';
                                    // echo '<p align="center">Please check the course_id again. <br> </p>';
                                } elseif (!empty(check_if_course_already_registered__in__register_table($_SESSION["Student_id"], $_POST["course_id"]))) {
                                    echo '<p align="center"> Dear Student <strong> ' . $_SESSION["actual_username"] . '</strong>, you have already 
                                    registered for this course, <strong> ' . $_POST["course_id"] . '</strong>.<br> </p>';
                                    // echo '<p align="center">Please check the course_id again. <br> </p>';
                                } else {
                                    std_register_class($_SESSION["Student_id"], $_POST["course_id"]);
                                    echo '<br>';
                                    echo '<p align="center" style="color:red"> Dear Student ' . $_SESSION["actual_username"] . ', you have been 
                        successfully registered for the ' . $_POST["course_id"] . '</p>';

                                    // print_r($_SESSION);
                                    // print_r($_POST);
                                }
                            }
                        }
                        ?>



                        <?php
                        if (isset($_POST["submit_button"])) {
                        ?>
                            <form method=post action=5.student_homepage.php>
                                <p align="center"> <input type="submit" value="Go Back" name="homepage" style="font-size: 1em;"> </p>
                            </form>
                            <br>
                        <?php
                            if (!isset($_SESSION["username"])) {
                                header("LOCATION:1.login.php");
                            }
                            if ($_POST["submit_button"] == "Submit") {
                                update_into_takes_table__finish_timestamp($_SESSION["Student_id"], $_SESSION["course_exam_id"]);
                                foreach (array_keys($_POST) as $x) {
                                    if ($x != 'submit_button')
                                        //   echo $x .":". $_POST[$x]. "<br>";
                                        insert_into_student_answers_table($_SESSION["Student_id"], $_POST[$x]);
                                }
                                echo '<br>';
                                echo '<p align="center" style="color:red"> Dear Student ' . $_SESSION["actual_username"] . ', thanks for
                                submitting the <strong>' . $_SESSION["course_id"] . ' ' . $_SESSION["exam_name"] . '</strong> exam.</p>';

                                // print_r($_SESSION);
                                // print_r($_POST);
                            }
                        }
                        ?>
                </div>
        </div>
    </main>
</body>

</html>