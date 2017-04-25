<?PHP

session_start();
// define a username and password for test you can change this to a query from database or anything else that fetch a username and password
$username = sprintf("SELECT userName FROM Students WHERE userName = '%s' and password = '%s'", username, password);
$password = 'demopass';
if (isset($_POST)) { // if ajax request submitted
    $post_username = $_POST['username']
    ; // the ajax post username
    $post_password = $_POST['password']

    ; // the ajax post password
    if ($username == $post_username && $password == $post_password) { // if the username and password are right
        $_SESSION['username']


            = $post_username; // define a session variable
        echo $post_username; // return a value for the ajax query
    }
}

//// define variables and set to empty values
//$userName = $password = "";
//
//if ($_SERVER["REQUEST_METHOD"] == "POST") {
//  $userName = test_input($_POST["userName"]);
//  $password = test_input($_POST["password"]);
//}
//
//function test_input($data) {
//  $data = trim($data);
//  $data = stripslashes($data);
//  $data = htmlspecialchars($data);
//  return $data;
//}

?>