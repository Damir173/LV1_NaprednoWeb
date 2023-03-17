<?php

$server = "localhost";
$database ="radovi";
$username = "root";
$password = "";


  $con = mysqli_connect($server, $username, $password, $database);
  if (mysqli_connect_errno())
    {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
    }   



?>