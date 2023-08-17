<?php 
function get_connection(){
  $dsn = "mysql:host=localhost;dbname=hello";
  $user = "root";
  $passwd = "";
  $conn = new PDO($dsn, $user, $passwd);
  return $conn;
}