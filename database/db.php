<?php

function dbConn() {
    $server = "localhost";
    $database = "radovi";
    $username = "root";
    $password = "";


    $conn = new mysqli($server, $username, $password, $database);
    if ($conn->connect_error) {
        die($conn->connect_error);
    }

    return $conn;

}

  function escape($string)
  {
      global $conn;
      return mysqli_real_escape_string($conn, $string);
  }
  
  function query($query)
  {
      global $conn;
      return mysqli_query($conn, $query);
  }
  
  function confirm($result)
  {
      global $conn;
      if (!$result) {
          die("QUERY FAILED " . mysqli_error($conn));
      }

 }
?>

