<?php
$pdo = new PDO("mysql:host=localhost;dbname=notifiyr;charset=utf8mb4", 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false
  ]);
  if($_SERVER["REQUEST_METHOD"] == "POST"){
    //get incoming data
    $f = file_get_contents('php://input');
    $jsonobj = stripslashes($f);

    $obj = json_decode($jsonobj);
    // print_r(array($data));
    $wherein = str_repeat(',?', count($obj) - 1);
    $stmt = $pdo->prepare("SELECT cccode, header, body FROM messages WHERE cccode IN(? $wherein )");
    $stmt->execute($obj);
    $array_push = $stmt->fetchAll(PDO::FETCH_ASSOC);
}    $return = array("resoponse" => array("check" => "ahhhhhhhh"), "Mlist" => $array_push);

echo json_encode($return);

//  $jsonobj = '[ "ascaasease", "ascaesease", "ilovecashs"]';
?>