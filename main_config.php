<?php
define('dbserver', 'localhost');
define('dbuser', 'root');
define('dbpassword', '');
define('dbname', 'notifiyr');

/* Attempt to connect to MySQL database */
$conn = mysqli_connect('localhost','root', '', 'notifiyr');

// Check connection
if(!$conn){
    die("ERROR: Could not connect." . mysqli_connect_error());
}
?>