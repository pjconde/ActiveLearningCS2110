<?php
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');

    //SSE work-around
    //to be called from questions.html when prof changes questions
    //for now is connected to roster.html on section dropdown until sessionID is fixed

    include 'opendb.php';

    // initial calling function (repeatedly)
    checkExistingData();

    // Get data from database
    function getData () {
      	$conn = $GLOBALS['conn'];
      	// fetching data from database
        $sql = sprintf("SELECT curQ FROM ProfCurQ");
        try {
            $result = $conn->query($sql);
            $json = json_encode($result->fetchAll(PDO::FETCH_ASSOC));
            return $json;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // Update session and send message
    function checkExistingData () {
      	session_start();
      	// if session old id is exist & not empty
      	if ( isset( $_SESSION['oldId'] ) && $_SESSION['oldId'] != "" ) {

            $data = getData();
            //do not remove true from below statement
            $el = json_decode($data, true);

            // if old session NOT matched with
      	    // the newly inserted data from database
            // means database table entry was changed
            // try to limit this table (ProfCurQ) to one entry
      	 		if ( $_SESSION['oldId'] != $el[0] ) {

                // call send message function
                sendMsg($data);
                // then we store newly inserted data into session
                // if el < 0 then session is over, next iteration will destroy
                if ($el[0] > 0) {
            	 		$_SESSION['oldId'] = $el[0];
                }

            } else {
                // if data not change and el>0 then nothing happens
                // if data not change and el<0 then session is over
                if ($el[0] < 0) {
                  session_destroy();
                }
            }

         // this is for initial start of event source
      	 } else {

              $data = getData();
              $el = json_decode($data, true);

              if ( $el[0] > 0 ) {
                  // create first session
                  $_SESSION['oldId'] = $el[0];
                  sendMsg( $data );
              }
         }
    }

    // create event source
    // send to client side
    function sendMsg ( $data ) {
    	// never remove "data: " from front of below statement
      // never remove "\n\n" from back of below statement
      // sends json result data to student answer page (currently testing on answer.html)
    	echo "data: " . $data . "\n\n";
    	ob_flush();
    	flush();
    }

    // for testing, bypassing functions, need to reformat client end to work
    // $time = date('r');
    // echo "data: The server time is: {$time}\n\n";
    // flush();

    include 'closedb.php';
?>
