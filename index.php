<?
include 'opendb.php';

if(! $conn ) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        $file = '/tmp/sample-app.log';
        $message = file_get_contents('php://input');
        file_put_contents($file, date('Y-m-d H:i:s') . " Received message: " . $message . "\n", FILE_APPEND);
    }
}

debug_to_console( "Hello" );
?>

<!DOCTYPE html>

<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Learning - Home</title>
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="scripts.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>

<script>
    printHeaderLanding();
</script>

<div class="welcome-title">
    <h2 class="welcome">Welcome to Active Learning for CS2110!</h2>
</div>
<div class="container-fluid login-div">
    <div class="container">
        <div class="row login-prompt">
            <div class="col-lg-5 col-md-12 col-xs-12 login-prompt-subdiv">
                Where we get the ball rolling.
                <form  action="" autocomplete="on">
                    <div style="margin-top: 15px;">
                        <a href="login.html" class="btn btn-success btn-lg login login-btn" role="button">Login</a>
                    </div>
                    <div style="margin-top: 15px;">
                        <a href="register.html" class="btn btn-success btn-lg login register-btn" role="button">Register</a>
                    </div>
                </form>
            </div>
            <div class="col-lg-7 col-md-12 col-xs-12">
                <img class="welcome-img" alt="welcome-screen" src="welcome-screen.png" height="500"></center>
            </div>
        </div>
    </div>
</div>

<footer class="welcome-footer">
</footer>

</body>
</html>

<?

include 'closedb.php';
?>
