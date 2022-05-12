<?php

if(isset ($_GET['email'])){
    include "db_connect.php";
    $email = $_GET['email'];
    $sql = "DELETE FROM newsletter WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    
    
    if($stmt->execute()){
        $stmt->close();
        die(json_encode(array("status" => "success", "message" => "You have been unsubscribed from the newsletter.")));

    }
}
?>