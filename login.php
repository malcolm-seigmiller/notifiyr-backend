<?php

require_once "main_config.php";


error_reporting(E_ALL); ini_set('display_errors', 1);
session_start(); // Starting Session

if (isset($_SESSION['loggedIn'])) {
    header('Location: dashboard');
    exit();
}
$email = $email_err = "";
$password = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_SESSION['loggedIn'])) {
        header('Location: dashboard');
        exit();
    }
    
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }

    if(empty($email_err) && empty($password_err)){
        // Prepare a select statement

        $sql = "SELECT plan, cccode, email, verified, password FROM users WHERE email = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = $email;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){         

                    mysqli_stmt_bind_result($stmt, $plan, $cccode, $email, $verified, $hashed_password);

                    if(mysqli_stmt_fetch($stmt)){

                        if($verified == 1){
                            if(password_verify($password, $hashed_password)){

                                session_start();
                            
                                $_SESSION["loggedIn"] = 1;
                                $_SESSION["plan"] = $plan;
                                $_SESSION["cccode"] = $cccode;
                                $_SESSION["email"] = $email;                            
                                
                                header("location: dashboard");
                            } else{
                                $password_err = "The password you entered was not valid.";
                            }
                        }else{
                            $email_err = "please verify your account, check your email inbox for the verification link";
                        }   
                    }
                } else{
                    $email_err = "No account found with that email.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($conn);
}
//SELECT cccode, plan , password FROM users WHERE email=? AND password=? LIMIT 1
//$password = password_hash($password, PASSWORD_DEFAULT);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>login to Notifiyr</title>
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
        <div class="row">
            <div class="col-md-8">
                <a href="signup">Click here for a free account</a>
            </div>
        </div>
        <div class="row justify-content-center log">
            <div class="col-md-6 col-md-offset-3"align="center">
                <h2>login to Notifiyr</h2>
                <br>
                <br>
                <form method="POST">
                    <span class="help-block"><?php echo $email_err; ?></span>
                    <input class="form-control" name="email" placeholder="Email..."><br>
                    <span class="help-block"><?php echo $password_err; ?></span>
                    <input class="form-control" name="password" placeholder="Password..." type="password"><br>
                    <input type="submit" class="btn btn-primary" value="Log In">
                </form>
            </div>
        </div>
    </div>
</body>
</html>