<?php
include 'opendb.php';

if (isset($_GET['function'])) {
    if ($_GET['function'] == 'getSectionRoster') {
        getSectionRoster($_GET['section']);
    } else if ($_GET['function'] == 'getStudentGrades') {
        getStudentGrades($_GET['section']);
    } else if ($_GET['function'] == 'getAttendance') {
        getAttendance($_GET['section']);
    } else if ($_GET['function'] == 'getGradeDist') {
        getGradeDist($_GET['section']);
    } else if ($_GET['function'] == 'getStudentHistogram') {
        getStudentHistogram($_GET['userName']);
    } else if ($_GET['function'] == 'getResponseValue') {
        getResponseValue($_GET['qID']);
    } else if ($_GET['function'] == 'getStudentResponses') {
        getStudentResponses();
    } else if ($_GET['function'] == 'getReviewSets') {
        getReviewSets($_GET['sectionID']);
    } else if ($_GET['function'] == 'validateLogin') {
        validateLogin($_GET['userName'],$_GET['password']);
    }
}

if(isset($_POST['function'])){
    if ($_POST['function'] == 'insertConfidence') {
        insertConfidence($_POST['userName'], $_POST['answerval'], $_POST['confidencelevel']);
    } else if ($_POST['function'] == 'insertStudentResponse'){
        insertStudentResponse($_POST['userName'], $_POST['qID'], $_POST['isCorrectval'], $_POST['date']);
    } else if ($_POST['function'] == 'resetResponseTable') {
        resetResponseTable();
    } else if ($_POST['function'] == 'updatePassword') {
        updatePassword($_POST['password'],$_POST['userName']);
    }
}

function getResponseValue($qID) {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("SELECT choice FROM Choices where QID = '%s' AND is_correct = '1'", $qID);
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

function insertStudentResponse($userName, $qID, $isCorrectval, $date) {
    //die('test');
    $conn = $GLOBALS['conn'];
    //die('test');
    $sql = sprintf("INSERT INTO Responses (userName, QID, is_correct, date) VALUES ('%s', %s, '%s', '%s')", $userName, $qID, $isCorrectval, $date);
    //die('test');
    try{
        //die('works');
        $result = $conn->query($sql);
        // $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        // echo $json;
    } catch (PDOException $e) {
        //die('doesnt work');
        echo $e->getMessage();
        die();
    }
}

function insertConfidence($userName, $answerval, $confidencelevel) {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("INSERT INTO student_response (username, stud_ans, conf_level) VALUES ('%s', '%s', %s)", $userName, $answerval, $confidencelevel);
    try{
        $result = $conn->query($sql);
        // $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        // echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

// populate response table
function getStudentResponses() {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("SELECT * FROM student_response");
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

// delete all entries from response table
function resetResponseTable() {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("DELETE FROM student_response");
    try{
        $result = $conn->query($sql);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

function updatePassword($password, $userName) {
    $conn = $GLOBALS['conn'];
    $pass = md5($password); //trying to save as hash
    $sql = sprintf("UPDATE Students SET password = '%s' WHERE userName = '%s'", $pass, $userName);
    try{
        $result = $conn->query($sql);
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

function validateLogin($userName, $password) {
    $conn = $GLOBALS['conn'];
    $pass = md5($password);
    $sql = sprintf("SELECT userName, password FROM Students WHERE userName = '%s' and password = '%s'", $userName, $pass);
    try {
        $result = $conn-> query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}


// Can be used to get the roster of a section
// Use specific section id or pass in All to get all students
function getSectionRoster($secID) {
    $conn = $GLOBALS['conn'];
    if ($secID == "All") {
        $sql = "SELECT * FROM Students";
    } else {
        $sql = sprintf("SELECT * FROM Students WHERE sectionID = '%s'", $secID);
    }
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
// Can be used to get the roster with attendance of a section
// Use specific section id or pass in All to get all students
function getAttendance($secID) {
    $conn = $GLOBALS['conn'];
    if ($secID == "All") {
        $sql = "SELECT * FROM Attendance_Grades";
    } else {
        $sql = sprintf("SELECT * FROM Attendance_Grades WHERE sectionID = '%s'", $secID);
    }
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
//gets distribution graph on Grade Dist page
function getGradeDist($section) {
    $conn = $GLOBALS['conn'];
    if ($section == "All") { //CANNOT SPLIT UP SQL STRING INTO DIFFERENT LINES
        $sql = "SELECT (FLOOR(percentageCorrect/10)*10) AS bucket, COUNT(*) AS numStudents FROM Participation_Grades GROUP BY bucket";
    } else {
        $sql = sprintf("SELECT (FLOOR(percentageCorrect/10)*10) AS bucket, COUNT(*) AS numStudents FROM Participation_Grades  WHERE sectionID = '%s' GROUP BY bucket", $section);
    }
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
//gets table of students on Grade Dist page
function getStudentGrades($section) {
    $conn = $GLOBALS['conn'];
    if ($section == "All") {
        $sql = "SELECT * FROM Participation_Grades";
    } else {
        $sql = sprintf("SELECT * FROM Participation_Grades WHERE sectionID = '%s'", $section);
    }
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
//gets graph of an individual student's participation grade history
function getStudentHistogram($userName) {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("SELECT date, dailyPercentage FROM Student_Grade_Hist WHERE userName = '%s'", $userName);
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}
// Isn't linked yet. Need to figure out where session ID is even stored/calculated
function checkSessionId($title) {
    $conn = $GLOBALS['conn'];
    $sql = sprintf("SELECT * FROM students WHERE title = '%s'", $title);
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

function getReviewSets($sectionID) {
    $conn = $GLOBALS['conn'];
    // $sql = sprintf("SELECT * FROM Sets RIGHT JOIN Sets.setID = Review.setID AND Revuew.sectionID = %s", $sectionID);
    $sql = sprintf("SELECT * FROM Sets RIGHT JOIN Review ON Sets.setID = Review.setID WHERE Review.sectionID = '%s'", $sectionID);
    try{
        $result = $conn->query($sql);
        $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        echo $json;
    } catch (PDOException $e) {
        echo $e->getMessage();
        die();
    }
}

include 'closedb.php';
?>
