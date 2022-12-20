<?php

function connectDB()
{
    $config = parse_ini_file("db.ini");
    $dbh = new PDO($config['dsn'], $config['username'], $config['password']);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

//return number of rows matching the given user, passwd and usertype.
function authenticate($user, $passwd, $usertype)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT count(*) FROM Application_accounts " .
            "where account_id = :user and password = sha2(:passwd,256) and role_type= :usertype ");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":passwd", $passwd);
        $statement->bindParam(":usertype", $usertype);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}
// return the user name based on the userid.
function fetch_name($user)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT name FROM Application_accounts " . "where account_id = :user ");
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function first_time_login($user)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select first_time_login from Application_accounts where account_id =:user");
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

// TODO this thing needs to be done.
function update_password($user, $new_passwd)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE Application_accounts SET password=sha2(:new_passwd,256), first_time_login=50 WHERE account_id=:user");
        $statement->bindParam(":user", $user);
        // $statement->bindParam(":old_passwd", $old_passwd);
        $statement->bindParam(":new_passwd", $new_passwd);
        $result = $statement->execute();
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function profile($user, $usertype)
{
    try {
        $dbh = connectDB();
        if ($usertype == 'Instructor') {
            $statement = $dbh->prepare("select * from Instructors where account_id=:user");
        } else {
            $statement = $dbh->prepare("select * from Students where account_id=:user");
        }
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


# &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
# Functions for Instructor-
function get_instructor_courses($user)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select course_id, title, credit from Courses where instructor_id in (select instructor_id from Instructors where account_id =:user)");
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_instructor_course_exams($user)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select course_id, exam_name, opening_date, closing_date, total_points,creation_timestamp from Exams 
                                    where instructor_id in (select instructor_id from Instructors where account_id =:user)");
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function instructor_check_scores($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select course_id, count(distinct student_id) as 'total student', exam_name, count(takes_id) as 'exam completed by',
        ifnull(min(marks),0) as 'minimum score', ifnull(max(marks),0) as 'maximum score', ifnull(avg(marks),0) as 'average score' from
        (select instructor_id,e.course_exam_id,e.course_id,e.exam_name, r.student_id, t.takes_id,t.student_id as e_t_student,
        grade_points, CAST(SUBSTRING_INDEX(grade_points,'/',1) AS unsigned) as marks
        from Register r right join Exams e on e.course_id=r.course_id  and instructor_id in (select instructor_id from Instructors where account_id =:user)
        left join Takes t on e.course_exam_id=t.course_exam_id and r.student_id=t.student_id   
        where e.course_id= :course_id and exam_name=:exam_name) a
        group by course_id, exam_name");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function instructor_CS_individual_students($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select t.student_id,name, start_timestamp, finish_timestamp, CAST(SUBSTRING_INDEX(grade_points,'/',1) AS unsigned) as score 
        from Takes t join Students s on t.student_id = s.student_id and t.course_exam_id in 
        (select course_exam_id from Exams where course_id= :course_id and exam_name= :exam_name and Instructor_id in 
        (select instructor_id from Instructors where account_id = :user))");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}



// below functions for extracting the exam questions for review purpose-
function fetch_course_exam_id($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select course_exam_id from Exams where course_id=:course_id and exam_name=:exam_name 
        and instructor_id = (select instructor_id from Instructors where account_id = :user)");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        foreach ($row as $r) {
            return $r[0];
        }
        // return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function fetch_each_questions($course_exam_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select CONCAT(question_no, ': ', question_description, '  (', question_points, ' points)') 
        as question, course_exam_question_id from Questions where course_exam_id = :course_exam_id");
        $statement->bindParam(":course_exam_id", $course_exam_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        // foreach ($row as $r) {
        //     return $r[0];
        // }
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function fetch_each_options_by_question_id($course_exam_question_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("with options as (
            select course_exam_question_choice_id, choice_no, choice_description, correct_choice_no
            from Multiple_choices mc left join Questions q on mc.course_exam_question_id=q.course_exam_question_id 
            where q.course_exam_question_id =:course_exam_question_id
            )
            select course_exam_question_choice_id,
            CONCAT('   ', choice_no, ': ', choice_description, ' ', if(choice_no=correct_choice_no,'(Correct)','')) as options
            from options;");
        $statement->bindParam(":course_exam_question_id", $course_exam_question_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}




# &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
# Functions for Students-


// return the student id based on the accountid.
function fetch_std_id($user)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("SELECT student_id FROM Students " . "where account_id = :user ");
        $statement->bindParam(":user", $user);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_std_courses($std_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select c.course_id,title, credit, name from Courses c join Instructors i on c.instructor_id = i.instructor_id 
        join Register r on r.course_id=c.course_id and student_id = :std_id");
        $statement->bindParam(":std_id", $std_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_std_exam_scores($std_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select r.course_id,exam_name, opening_date, closing_date, total_points,
        ifnull(start_timestamp,''), ifnull(finish_timestamp,''), ifnull(CAST(SUBSTRING_INDEX(grade_points,'/',1) AS unsigned),'') as score  
        from Register r join Exams e on r.course_id=e.course_id and r.student_id = :std_id 
        left join Takes t on t.course_exam_id=e.course_exam_id and r.student_id = t.student_id");
        $statement->bindParam(":std_id", $std_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function get_std_not_registerd_courses($std_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select * from Courses where course_id not in (select course_id from Register where student_id= :std_id )");
        $statement->bindParam(":std_id", $std_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function check_if_course_present__in__course_table($class_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select count(*) from Courses where course_id=:class_id");
        $statement->bindParam(":class_id", $class_id);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function check_if_course_already_registered__in__register_table($user, $class_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select count(*) from Register where student_id=:user and course_id=:class_id");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":class_id", $class_id);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function std_register_class($user, $class_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("Insert into Register(student_id, course_id) values (:user, :class_id)");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":class_id", $class_id);
        $result = $statement->execute();
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function std_check_scores($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select CAST(SUBSTRING_INDEX(grade_points,'/',1) AS unsigned) as total_score, 
        CAST(SUBSTRING_INDEX(grade_points,'/',-1) AS unsigned) as out_of, 
        (CAST(SUBSTRING_INDEX(grade_points,'/',1) AS unsigned)/CAST(SUBSTRING_INDEX(grade_points,'/',-1) AS unsigned))*100 as percentage,
        start_timestamp, finish_timestamp, TIMESTAMPDIFF(SECOND, start_timestamp, finish_timestamp) AS 'duration_in_sec' from Takes
        where student_id = :user and course_exam_id in (select course_exam_id from Exams where course_id =:course_id and exam_name=:exam_name)");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}



function std_check_each_question_ans($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("with cte as (
                select question_no, question_description,correct_choice_no, course_exam_question_id from Questions where course_exam_id in ( 
                select course_exam_id from Exams where course_id =:course_id and exam_name =:exam_name)
                )
                select question_no,question_description,choice_no as 'your_answer',points as 'your_point',decision,
                correct_choice_no as 'correct_answer' from cte left join Multiple_choices mc on cte.course_exam_question_id=mc.course_exam_question_id 
                left join Student_answer sa on mc.course_exam_question_choice_id=sa.course_exam_question_choice_id 
                where student_id=:user;");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function fetch_course_exam_id__if_student_takes_it($user, $course_id, $exam_name)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select e.course_exam_id from Exams e join Register r on e.course_id=r.course_id and 
        e.course_id =:course_id and e.exam_name = :exam_name and r.student_id =:user");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_id", $course_id);
        $statement->bindParam(":exam_name", $exam_name);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        foreach ($row as $r) {
            return $r[0];
        }
        // return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}



function for_student_taking_exam__fetch_each_options_by_question_id($course_exam_question_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("with options as (
            select course_exam_question_choice_id, choice_no, choice_description, correct_choice_no
            from Multiple_choices mc left join Questions q on mc.course_exam_question_id=q.course_exam_question_id 
            where q.course_exam_question_id =:course_exam_question_id
            )
            select course_exam_question_choice_id,
            CONCAT('   ', choice_no, ': ', choice_description) as options
            from options");
        $statement->bindParam(":course_exam_question_id", $course_exam_question_id);
        $result = $statement->execute();
        $row = $statement->fetchAll();
        return $row;
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function check_the_student_id__in__takes_table($user, $course_exam_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select count(*) from Takes where student_id=:user and course_exam_id=:course_exam_id");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_exam_id", $course_exam_id);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function check_if_exam_window_is_open_or_not__in__Exams_table($course_exam_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("select count(*) from Exams where opening_date <= now() and closing_date >=now() and 
        course_exam_id=:course_exam_id");
        $statement->bindParam(":course_exam_id", $course_exam_id);
        $result = $statement->execute();
        $row = $statement->fetch();
        $dbh = null;
        return $row[0];
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}

function insert_into_takes_table($user, $course_exam_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("Insert into Takes(student_id, course_exam_id,start_timestamp) values (:user, :course_exam_id, now())");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_exam_id", $course_exam_id);
        $result = $statement->execute();
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function update_into_takes_table__finish_timestamp($user, $course_exam_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("UPDATE Takes SET finish_timestamp=now() WHERE student_id = :user and course_exam_id = :course_exam_id");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_exam_id", $course_exam_id);
        $result = $statement->execute();
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}


function insert_into_student_answers_table($user, $course_exam_question_choice_id)
{
    try {
        $dbh = connectDB();
        $statement = $dbh->prepare("Insert into Student_answer(student_id, course_exam_question_choice_id) 
        values (:user, :course_exam_question_choice_id)");
        $statement->bindParam(":user", $user);
        $statement->bindParam(":course_exam_question_choice_id", $course_exam_question_choice_id);
        $result = $statement->execute();
    } catch (PDOException $e) {
        print "Error!" . $e->getMessage() . "<br/>";
        die();
    }
}
