<?php
$dbhost = 'activelearning.cegro0qmukef.us-east-2.rds.amazonaws.com:3306';
$dbuser = 'jsondeli191';
$dbpass = 'deli191!';
$dbname = 'ActiveLearning';

try{
  $conn = new PDO('mysql:host='.$dbhost.';dbname='.$dbname, $dbuser, $dbpass);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
  echo $e->getMessage();
  die();
}
?>
