<?php
require_once "main_config.php";

$email = $email_err = "";
$cccode = $cccode_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $ip = $_SERVER['REMOTE_ADDR'];

    if(empty(trim($_POST["cccode"]))){
        $cccode_err = "Please enter a cccode.";
    } else{
        $cccode = trim($_POST["cccode"]);
    }
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    } else{
        $sql1 = "SELECT `plan` FROM `users` WHERE `cccode`=  ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("s", $cccode);
        $stmt1->execute();
        $callback = $stmt1->get_result();
        if($callback->num_rows === 0){
            $cccode_err = "Please enter a valid cccode.";
            $result = 0;
        }
        while($row = $callback->fetch_assoc()){
            $result = $row['plan'];
        }
        $stmt1->close();

        switch ($result):
            case 0:
                $cccode_err = "Please enter a valid cccode";
            break;
            case 1:
            $sql2 = "SELECT COUNT(email) as the_count FROM subscribers WHERE cccode= ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("s", $cccode);
            $stmt2->execute();
            $callbacks = $stmt2->get_result();
            $rows = $callbacks->fetch_assoc();
            $stmt2->close();
            if ($rows['the_count'] < 10000000000){
                $email = trim($_POST["email"]);
            }else{
                $email_err = "This user has reached the limmit to what their plan can allow .";
            }
            break;
            case 2:
                $sql2 = "SELECT COUNT(email) as the_count FROM subscribers WHERE cccode= ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("s", $cccode);
                $stmt2->execute();
                $callbacks = $stmt2->get_result();
                $rows = $callbacks->fetch_assoc();
                $stmt2->close();
            if ($rows['the_count'] < 500001){
                $email = trim($_POST["email"]);
            }else{
                $email_err = "This user has reached the limmit to what their plan can allow .";
            }
            break;
            case 3:
                $sql2 = "SELECT COUNT(email) as the_count FROM subscribers WHERE cccode= ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("s", $cccode);
                $stmt2->execute();
                $callbacks = $stmt2->get_result();
                $rows = $callbacks->fetch_assoc();
                $stmt2->close();
                if ($rows['the_count'] < 2000001){
                    $email = trim($_POST["email"]);
                }else{
                    $email_err = "This user has reached the limmit to what their plan can allow .";
                }
            break;
            case 4:
                $sql2 = "SELECT COUNT(email) as the_count FROM subscribers WHERE cccode= ?";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("s", $cccode);
                $stmt2->execute();
                $callbacks = $stmt2->get_result();
                $rows = $callbacks->fetch_assoc();
                $stmt2->close();
                if ($rows['the_count'] < 20000001){
                    $email = trim($_POST["email"]);
                }else{
                    $email_err = "This user has reached the limmit to what their plan can allow .";
                }
            break;
        endswitch;
    }
    if(empty($email_err) && empty($cccode_err)){
        $sql = "INSERT INTO subscribers (email, cccode, ip ,vkey) VALUES (?, ?, ?, ?)";

        $vkey = md5(time().$cccode);

        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssss", $param_email, $param_cccode, $param_ip, $vkey);

            $param_email = $email;
            $param_cccode = $cccode;
            $param_ip = $ip;
            $param_vkey = $vkey;

            //mail

            //send email
            $message = "<a href='http://www.notifiyr.com/verify2.php?vkey=$vkey'>account verification </a>";
//            $message = "<a href='http://notifiyr.com/verify2.php?vkey=$vkey'>account verification </a>";
            //change the link to the website

            $to = $email;
            $subject = 'account validation';
            $from = 'admin@notifiyr.com';
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= 'From: '.$from."\r\n".
                'Reply-To: '.$from."\r\n" .
                'X-Mailer: PHP/' . phpversion();

                //doing it like this may be inefficient
            if(mysqli_stmt_execute($stmt)){
                mail($to, $subject, $message, $headers);
                header("Location: thankyou");
            } else{
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
    <title>welcome to Notifiyr</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        body{ 
            /*font: 14px sans-serif;*/
            /*margin: 30px;*/
        }
        .main{
            margin: 20px;
            height: 300px;
        }
        .ads
        {
            height: 420px;
            width: 160px;
            margin: 25px;
            /*background-color:aquamarine ;*/
        }
        .add
        {
            height: 720px;
            width: 160px;
            margin: 25px;
            /*background-color:aquamarine ;*/
        }

        .adc{
            /*background-color: aquamarine;*/
            /*height: 90px;*/
            width: 728px;
            margin: 25px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <script data-ad-client="ca-pub-5263652425125933" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
</head>
<body>
    <div class="container-fluid">
        <nav class="navbar navbar-expand-md bg-dark navbar-dark">
            <a class="navbar-brand" href="#">Notifiyr</a>
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
        <div class="row fluid">
            <div class="col ads">
<!--                <p>aaaa</p>-->
            </div>
            <div class="col-md-6 main">
                <h2 class="display-4">welcome to Notifiyr</h2>
                <p>please enter your email as well as the cc code of your content creator.</p>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                        <label>email</label>
                        <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                        <span class="help-block"><?php echo $email_err; ?></span>
                    </div>
                    <div class="form-group <?php echo (!empty($cccode_err)) ? 'has-error' : ''; ?>">
                        <label>cccode</label>
                        <input type="cccode" name="cccode" class="form-control" value="<?php echo $cccode; ?>">
                        <span class="help-block"><?php echo $cccode_err; ?></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Submit">
                    </div>
                    <p>Already have an account? <a href="login">Login here</a>.</p>
                </form>
                <br>
                <br>
                <br>
                <hr>
            </div>
            <div class="col ads">
<!--                <p>aaaa</p>-->
            </div>

        </div>
        <div class="row fluid justify-content-center">
            <div class="col-md-6 adc">
        </div>
        </div>
        <br>
        <br>
        <div class="row fluid">
            <div class="col add">
<!--                aaaa-->
            </div>
            <div class="col-md-6 main">
                <br>
                <h1 class="display-4">What is Notifiyr</h1>
                <br>
                <p>Notfiyr is a free utility for youtube content creators to independently notify their fan base of uploads, events, and what ever they need too.</p>
                <br>
                <h1 class="display-4">How do I use Notifiyr</h1>
                <br>
                <p>If you are a content creator just creat an account then circulate your content creator code online to your community, log into the web portal, enter what you want to say to your fanbase, and any of your fans who has signed up to receive notifications either on mobile or via email will be informed</p>
                <p>if you are a fan, just enter your content creators, CC code into the CC code bar along with your email and you will receive notifications from your content creator via email or enter your Content creators CC code into the Notifiyr Mobile app</p>
                <br>
                <h1 class="display-4">have any questions?</h1>
                <br>
                <p>for general inquiries <a href="mailto:contact@notifiyr.com">contact@notifiyr.com</a></p>
                <p>for business inquiries <a href="mailto:biz@notifiyr.com">biz@notifiyr.com</a></p>
<!--                <p>for anything else <a href="mailto:pr@notifiyr.com">pr@notifiyr.com</a></p>-->
            </div>
            <div class="col add">
<!--                aaaa-->
            </div>
        </div>
<!--        <div class="jumbotron text-center" style="margin-bottom:0">-->
<!--            <p>Footer</p>-->
<!--        </div>-->
    </div>
</body>
</html>