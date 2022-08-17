<?php
require_once "updateconfig.php";
//require_once "main_config.php";

if(isset($_GET['vkey'])){
    $vkey = $_GET['vkey'];

    $sql = "SELECT verified vkey FROM users WHERE verified = 0 AND vkey = ? Limit 1"; // not sure if '' needs to go around ?
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $vkey);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $sql2 = "UPDATE users SET verified = 1 WHERE vkey = ? LIMIT 1";
        $stmt= $conn->prepare($sql2);
        $stmt->bind_param("s", $vkey);
        $stmt->execute();
        $echo ="thank you your account has beed verified";
        }else{
        $echo ="this account is already verified or doest exist";
    }
}else{
    die($echo = "something went wrong");
}
?>
<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <title>verify your account</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container-fluid">
    <div class="d-flex justify-content-center main">
        <span class="help-block"><?php echo $echo; ?></span>
    </div>
</div>
</body>
</html>