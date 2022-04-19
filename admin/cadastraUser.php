<?php
$user2 = "admin";
$password2 = md5("admin");

include "../php/db_connect.php";

//insert user into admin   
$sql = "INSERT INTO admin (user, password) VALUES ('$user2', '$password2')";
$result = $mysqli->query($sql);

