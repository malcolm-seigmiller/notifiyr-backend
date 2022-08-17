<?php
require_once "dashboardConfig.php";

session_start();

if (!isset($_SESSION['loggedIn'])) {
    header('Location: login');
    exit();
}
$plan = $_SESSION['plan'];
$cccode = $_SESSION['cccode'];
$header = $header_err = "";
$content = $content_err = "";
$bv = "";
$userplan = "";

switch($plan):
    case 0:
        unset($_SESSION['loggedIn']);
        session_destroy();
        header('Location: login');
        exit();
        break;
    case 1:
        $userplan = "you are using a free subscription";
        break;
    case 2:
        $userplan = "you are using a platinum subscription";
        break;
    case 3:
        $userplan = "you are using a premium subscription";
        break;
endswitch;


if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["header"]))){
        $header_err = "please enter a header";
    }else{
        $header = trim($_POST["header"]);
    }

    if(empty(trim($_POST["content"]))){
        $content_err = "please enter a body";
    }else{
        $content = trim($_POST["content"]);
    }

    switch($plan):
        case 1:
            $a = strlen($content);
            if ($a <1024){
                //$sql = "SELECT Email FROM subs WHERE cccode = '$cccode' LIMIT 2001";
                //$list = mysqli_query($conn, $sql);
                //^i nee these for email/smtp functionality^
                // $header = "A content creator has sent you a message";
            }else{
                $content_err = 'you have reached charicter limit    MAX: 1024';
            }
            break;
        case 2:
            $a = strlen($content);
            if ($a <1024){
                //$sql = "SELECT Email FROM subs WHERE cccode = '$cccode' LIMIT 500001";
                //$list = mysqli_query($conn, $sql);
                //^i nee these for email/smtp functionality^
            }else{
                $content_err = 'you have reached charicter limit    MAX: 1024';
            }
            break;
        case 3:
            $a = strlen($content);
            if ($a <2048){
                //$sql = "SELECT Email FROM subs WHERE cccode = '$cccode' LIMIT 2000001";
                //$list = mysqli_query($conn, $sql);
                //^i nee these for email/smtp functionality^
            }else{
                $content_err = 'you have reached charicter limit    MAX: 2048';
            }
            break;
    endswitch;


    if(empty($header_err) && empty($content_err)){
        $sql3 = "DELETE FROM messages where cccode = ?";
        $stmt4 = $conn->prepare($sql3);
        $stmt4->bind_param("s", $cccode);
        $stmt4->execute();

        $sql = "INSERT INTO messages(cccode , header , body) VALUES(?, ?, ?)";

        if($stmt = mysqli_prepare($conn, $sql)){

            mysqli_stmt_bind_param($stmt, "sss", $param_cccode, $param_hearder, $param_body);

            $param_cccode = $cccode;
            $param_hearder = $header;
            $param_body = $content;

            $sql2 = "SELECT email FROM subscribers WHERE cccode = ? AND verified = 1";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("s", $cccode);
            $stmt2->execute();
            $result = $stmt2->get_result(); // get the mysqli result
            // $user = $result->fetch_assoc(); // fetch data
            //place in loop
            $msg = wordwrap($content,70);

            while($row = $result->fetch_assoc()) {
                // echo "<tr><td>".$row["id"]."</td><td>".$row["firstname"]." ".$row["lastname"]."</td></tr>";
//                mail($row["email"],$header,$msg);
                //im not sure this will work
                $to = $row["email"];
                $subject = $header;
                $from = 'notification@notifiyr.com';
                $message = $msg;
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                $headers .= 'From: '.$from."\r\n".
                    'Reply-To: '.$from."\r\n" .
                    'X-Mailer: PHP/' . phpversion();

                if(mail($to, $subject, $message, $headers)){
                    //doing it like this may be inefficient
                    //echo 'Your mail has been sent successfully.';
                } else{
                    header("location: index");
                }
            }

            if(mysqli_stmt_execute($stmt)){
                header("location: thankyou2");
                //maybe change this to itself?

            }else{
                echo "Something went wrong. Please try again later.";
            }
        }
        mysqli_stmt_close($stmt);

    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>notifiyr</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <style type="text/css">

    .body{
        margin: 20px;
        font: 14px sans-serif;
    }

    .col-md-9 {
        border: 1px solid gray;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
    }

    .img {
        -webkit-border-radius: 50px;
        -moz-border-radius: 50px;
        border-radius: 50px;
    }

    .list-item {
        list-style: none;
        background: #0088cc;
        padding: 8px;
        border: 1px solid white;
    }

    .list-item a {
        color: #fff;
    }

    .btn:hover {
        -webkit-transition: all .3s ease-in-out;
        -moz-transition: all .3s ease-in-out;
        -ms-transition: all .3s ease-in-out;
        -o-transition: all .3s ease-in-out;
        transition: all .3s ease-in-out;

        -webkit-transform: scale(1.05);
        -moz-transform: scale(1.05);
        -ms-transform: scale(1.05);
        -o-transform: scale(1.05);
        transform: scale(1.05);
        }
        
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">
            <a class="navbar-brand" href="index">Notifiyr</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="signup">signup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=login>login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="comingsoon">coming soon</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row-fulid log-row">
            <div class="col-md-12">
                <a href="logout" style="float: right;">Log Out</a>
            </div>
        </div>
        <br>
        <br>
        <div class="row justify-content-center">
            <div class="col-md-3">
                <div class="jumbotron text-center" style="margin-bottom:0">
                <p><?php echo "your cc code is ".($cccode) ?></p>
                </div>
                </br>
                <div class="row justify-content-center">
                    <p><?php echo $userplan ?></p>
                </div>
            </div>
            <div class="col-md-8">
                <h2>notifiyr dashboard</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group">
                        <label for="head">header</label>
                        <input type="text" name="header" class="form-control" value="<?php echo $header; ?>">
                        <span class="help-block"><?php echo $header_err; ?></span>
                    </div>
                    <div class="form-group">
                        <label for="content">what do you want to say to the world?</label>
                        <textarea class="form-control" name="content" rows="5" id="content" value='<?php echo $content; ?>'></textarea>
                        <span class="help-block"><?php echo $content_err; ?></span>
                    </div>
                    <div class="pull-left">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <br>
                    <p>to send messages just enter what you want your subcribers too see and hit submit</p>
                    <span class="help-block"><?php echo $bv; ?></span>
                </form>
            </div>
        </div>
        <div class="row justify-content-center">
        </div>
    </div>
</body>
</html>