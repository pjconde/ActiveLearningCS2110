<?php
    include 'opendb.php';

    // this is the stuff to make the question
    if(isset($_POST['function'])){
      if($_POST['function'] == 'insertQuestion') {
        if (!$_POST['id']) {
          insertQuestion(rand(), $_POST['text'], $_POST['unit'], $_POST['answers']);
        } else {
          insertQuestion($_POST['id'], $_POST['text'], $_POST['unit'], $_POST['answers']);
        }
      } elseif ($_POST['function'] == 'insertQSRelation') {
          insertQSRelation($_POST['qid'], $_POST['sid']);
      } elseif ($_POST['function'] == 'insertSet') {
          insertSet(rand(), $_POST['title'], $_POST['questions']);
      } elseif ($_POST['function'] == 'deleteSet') {
          deleteSet($_POST['id']);
      } elseif ($_POST['function'] == 'deleteQSRelation') {
          deleteQSRelation($_POST['setID'], $_POST['questions']);
      } elseif ($_POST['function'] == 'deleteQ') {
          deleteQuestion($_POST['qid']);
      } elseif ($_POST['function'] == 'removeReviewable') {
          removeReviewable($_POST['sectionID'], $_POST['setID']);
      } elseif ($_POST['function'] == 'makeReviewable') {
          makeReviewable($_POST['sectionID'], $_POST['setID']);
      } elseif ($_POST['function'] == 'insertStudents') {
          insertStudents($_POST['usernames'], $_POST['sectionID']);
      } elseif ($_POST['function'] == 'deleteStudent') {
          deleteStudent($_POST['usernames']);
      } elseif ($_POST['function'] == 'changeQuestion') {
          changeQuestion($_POST['curQ']);
      } elseif ($_POST['function'] == 'endSession') {
         endSession();
     }
    }

    if(isset($_GET['function'])) {
        if($_GET['function'] == 'getQuestions') {
            getQuestions();
        } elseif($_GET['function'] == 'getSet') {
            getSet($_GET['title']);
        } elseif($_GET['function'] == 'getAllSets') {
            getAllSets();
        } elseif($_GET['function'] == 'getSetQuestions') {
            getSetQuestions($_GET['id']);
        } elseif($_GET['function'] == 'getQText') {
            getQText($_GET['qid']);
        } elseif($_GET['function'] == 'getQResponse') {
            getQResponse($_GET['id']);
        } elseif ($_GET['function'] == 'getSections') {
            getSections();
        } elseif ($_GET['function'] == 'getSessionID') {
            getSessionID();
        } elseif ($_GET['function'] == 'checkSessionID') {
            checkSessionID($_GET['id']);
        } elseif ($_GET['function'] == 'deleteSessionID') {
            deleteSessionID();
        } elseif ($_GET['function'] == 'generateSessionID') {
            generateSessionID();
        }
    }

// ---------- INSERTION QUERIES ------------------------------------------------------------ //

    // function to insert a new Set
    // the $questions param is the id of the questions to add
    // $questions defaults to null in case no questions added to set
    function insertSet($id, $title, $questions = null) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("INSERT INTO Sets (setID, set_title) VALUES (%s, '%s')", $id, $title);
        try {
            $result = $conn->query($sql);
            if ($questions != null) {
                for ($i = 0; $i < sizeof($questions); $i++) {
                    insertQSRelation($questions[$i], $id);
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to insert a new question
    // answers should be passed in as an array with the first choice being which one is correct
    // $set defaults to null incase no set is given
    function insertQuestion($id, $text, $unit, $answers) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("INSERT INTO Question (QID, Q_Text, unitName) VALUES (%s, '%s', '%s')", $id, $text, $unit);
        try{
            $result = $conn->query($sql);
            insertResponse($id, $answers[0], 1);
            for ($i = 1; $i < sizeof($answers); $i++) {
                if ($answers[$i] != "") {
                  insertResponse($id, $answers[$i]);
                }
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to insert a response for a question
    // mainly a helper function
    // $correct defaults to 0 unless told otherwise
    function insertResponse($qid, $choice, $correct = 0) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("INSERT INTO Choices (QID, choice, is_correct) VALUES (%s, '%s', %s)", $qid, $choice, $correct);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to insert question and set relationship
    function insertQSRelation($qid, $sid) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("INSERT INTO Question_in_set (setID, QID) VALUES (%s, %s)", $sid, $qid);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Function to add reviewable to Review table
    function makeReviewable($sectionID, $setID) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("INSERT INTO Review (setID, sectionID) VALUES (%s, '%s')", $setID, $sectionID);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to insert student usernames into the db by the professor
    function insertStudents($usernames, $sectionID) {
        $conn = $GLOBALS['conn'];
        for ($i = 0; $i < sizeof($usernames); $i++) {
            $name = $usernames[$i]['value'];
            $sql = sprintf("INSERT INTO Students (userName, sectionID) VALUES ('%s', '%s')", $name, $sectionID);
            try{
                $result = $conn->query($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    // function to change the question currently in session
    // for use in sse
    function changeQuestion($curQ) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM ProfCurQ");
        try{
            $result = $conn->query($sql);
            $sql = sprintf("INSERT INTO ProfCurQ (curQ) VALUES (%s)", $curQ);
            try{
                $result = $conn->query($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // for use in sse
    function endSession() {
        // $conn = $GLOBALS['conn'];
        // $sql = sprintf("DELETE FROM ProfCurQ");
        // try{
        //     $result = $conn->query($sql);
        // } catch (PDOException $e) {
        //     echo $e->getMessage();
        //     die();
        // }
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM ProfCurQ");
        try{
            $result = $conn->query($sql);
            $sql = sprintf("INSERT INTO ProfCurQ (curQ) VALUES -1");
            try{
                $result = $conn->query($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

// ---------- DELETION QUERIES ------------------------------------------------------------ //

    // function to delete a Set
    function deleteSet($id) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM Sets WHERE setID = %s", $id);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to delete a delete a Question
    // Cascade of deletion pending to see
    function deleteQuestion($id) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM Question WHERE QID = '%s'", $id);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to delete a response for a question
    // Used for when deleting one response in specific
    function deleteResponse($qid, $choice) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM Choices WHERE QID = %s AND choice = '$s'", $id, $choice);
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // function to delete question and set relationship
    // Used when taking a question out of a set
    function deleteQSRelation($sid, $questions) {
        $conn = $GLOBALS['conn'];
        for ($i = 0; $i < sizeof($questions); $i++) {
            $qid = $questions[$i];
            $sql = sprintf("DELETE FROM Question_in_set WHERE QID = %s AND setID = '%s'", $qid, $sid);
            try{
                $result = $conn->query($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    // function to delete a student from the db
    function deleteStudent($usernames) {
        $conn = $GLOBALS['conn'];
        for ($i = 0; $i < sizeof($usernames); $i++) {
            $username = $usernames[$i];
            $sql = sprintf("DELETE FROM Students WHERE userName = '%s'", $username);
            try{
                $result = $conn->query($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    // Function to remove review access from certain section
    // Takes in the sectionID and setID
    // If sectionID is set to "All" removes every instance of the setID
    function removeReviewable($sectionID, $setID) {
        $conn = $GLOBALS['conn'];
        if ($sectionID == 'All') {
            $sql = sprintf("DELETE FROM Review WHERE setID = %s", $setID);
        } else {
            $sql = sprintf("DELETE FROM Review WHERE sectionID = '%s'", $sectionID);
        }
        try{
            $result = $conn->query($sql);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }

    }

// ---------- GET QUERIES ------------------------------------------------------------ //

    function getQuestions() {
        $conn = $GLOBALS['conn'];
        $sql = "SELECT * FROM Question";
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function getSet($title) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("SELECT * FROM Sets WHERE set_title = '%s'", $title);
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function getAllSets() {
      $conn = $GLOBALS['conn'];
      $sql = sprintf("SELECT * FROM Sets");
      try{
          $result = $conn->query($sql);
          $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
          echo $json;
      } catch (PDOException $e) {
          echo $e->getMessage();
          die();
      }
    }

    function getSetQuestions($id) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("SELECT Question.QID, Question.Q_Text, Question.unitName FROM Question, Question_in_set WHERE Question.QID = Question_in_set.QID AND Question_in_set.setID = '%s'", $id);
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function getQText($qid) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("SELECT Question.Q_Text FROM Question WHERE Question.QID = %s", $qid);
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function getQResponse($qid) {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("SELECT * FROM Choices WHERE QID = %s", $qid);
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }
    // Weird session ID stuff

    // Generates a pseudorandom session ID between 0 and 99999
    function generateSessionID() {
        $randomID = mt_rand(0, 100000);
        $conn = $GLOBALS['conn'];
        $sql = sprintf("DELETE FROM SessionID");
        try{
            $result = $conn->query($sql);
            $sql = sprintf("INSERT INTO SessionID(sessionID) VALUES (%s)", $randomID);
            try{
                $result = $conn->query($sql);
                //$json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
                //echo $json;
            } catch (PDOException $e) {
                echo $e->getMessage();
                die();
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    function getSections() {
        $conn = $GLOBALS['conn'];
        $sql = sprintf("SELECT * FROM Sections");
        try{
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            echo $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Gets current session ID
    function getSessionID() {
      $conn = $GLOBALS['conn'];
      $sql = sprintf("SELECT * FROM SessionID");
      try {
          $result = $conn->query($sql);
          $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
          echo $json;
      } catch (PDOException $e) {
          echo $e->getMessage();
          die();
      }
    }

    // Checks whether the given session ID is valid. Returns session ID if valid, null otherwise
    function checkSessionID($id) {
      $conn = $GLOBALS['conn'];
      $sql = sprintf("SELECT * FROM SessionID where sessionID=%s", $id);
      try {
          $result = $conn->query($sql);
          $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
          echo $json;
      } catch (PDOException $e) {
          echo $e->getMessage();
          die();
      }
    }

    // Deletes session ID from the table
    function deleteSessionID() {
      $conn = $GLOBALS['conn'];
      $sql = sprintf("DELETE FROM SessionID");
      try {
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
