<?php
$pdo = new PDO("mysql:host=localhost;dbname=notifiyr;charset=utf8mb4", 'mainUser', 'WxMPyQJ7ZZgRRpob', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false
]);

// Define variables and initialize with empty values
$email = $password = $confirm_password = $cccode = $plan ="";
$email_err = $password_err = $confirm_password_err = $cccode_err = $plan_err= "";

//things to check
//email passwordx2 cccode
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter a email.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = :email";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            
            $param_email = trim($_POST["email"]);
            
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $email_err = "This account already exist.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 9){
        $password_err = "Password must have atleast 9 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    $length = 6;

    //generates cccode

    function generatecccode($length,$pdo){
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
        $abc = $randomString;
        return checkcccode($abc,$pdo);
    }

    function checkcccode($abc,$pdo){
        $sql2 = "SELECT cccode FROM users WHERE cccode = :cccode";
        
        if($stmt = $pdo->prepare($sql2)){
            $stmt->bindParam(":cccode", $param_cccode, PDO::PARAM_STR);
            $param_cccode = $abc;

            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    generatecccode();
                } else{
                    $cccode = $abc;
                    return $cccode;
                }
            } else{
                $cccode_err = "something doest want to work here, come back later";
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            unset($stmt);
        }
    }
    $cccode = generatecccode($length,$pdo);

    $plan = 1;
    
    $vkey = md5(time().$cccode);

    if(empty($email_err) && empty($password_err) && empty($confirm_password_err)&& empty($cccode_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (cccode, email, password, plan ,vkey) VALUES (:cccode, :email, :password, :plan, :vkey)";
         
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":cccode", $param_cccode, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":plan", $param_plan, PDO::PARAM_INT);
            $stmt->bindParam(":vkey", $param_vkey, PDO::PARAM_STR);

            $param_cccode = $cccode;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_plan = $plan;
            $param_vkey = $vkey;

            //send email
            $message = "<a href='https://www.notifiyr.com/verify.php?vkey=$vkey'>account verification </a>";

            $to = $email;
            $subject = 'account validation';
            $from = 'admin@notifiyr.com';
     
            // To send HTML mail, the Content-type header must be set
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
     
            // Create email headers
            $headers .= 'From: '.$from."\r\n".
            'Reply-To: '.$from."\r\n" .
            'X-Mailer: PHP/' . phpversion();
    
                // Attempt to execute the prepared statement
            if($stmt->execute()){
                mail($to, $subject, $message, $headers);
                header("location: thankyou3");
            } else{
                echo "Something went wrong. Please try again later.";
            }
            // Close statement
            unset($stmt);
        }
    }
    unset($pdo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>signup to Notifiyr</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        .container { margin-top: 100px; }
        .log{
            margin: 10%;
        }

        .body{
            font: 14px sans-serif;
            margin: 20px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
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
        </br>
        </br>
        <div class="d-flex justify-content-center main">
        <div class="wrapper">
            <h2>Sign Up</h2>
            <p>Please fill this form to create an account.</p>
            <br>
            <br>
            <br>
            <br>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>email</label>
                <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
            </div>
            <p>Already have an account? <a href="login">Login here</a>.</p>
            </form>
        </div>
    </div>
    </div>
</div>
</body>
</html>