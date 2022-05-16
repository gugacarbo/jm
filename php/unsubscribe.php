<?php
session_start();
if(isset ($_GET['email'])){
    include "db_connect.php";
    if (isset($_SESSION['unsubTry']) && $_SESSION['unsubTry'] > 2){
        $lastTry = date($_SESSION['unsubLastTry']);
        $interval = strtotime(date('Y-m-d H:i:s')) - strtotime($lastTry);
        if ($interval > 60) {
            $_SESSION['unsubTry'] = 0;
        }

        die(json_encode(array('error' => 'Tente Novamente Mais Tarde')));
    } else {
        $_SESSION['unsubTry'] = isset($_SESSION['unsubTry']) ? $_SESSION['unsubTry'] + 1 : 1;
        $_SESSION['unsubLastTry'] = date("Y-m-d H:i:s");
        $email = $_GET['email'];
        $sql = "DELETE FROM newsletter WHERE email = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $email);
        if($stmt->execute()){
            $stmt->close();
            die(json_encode(array("status" => "success", "message" => "You have been unsubscribed from the newsletter.")));
        }
    }


}
