<?php
//verify and receive "user" and "password" from post method
if (isset($_POST['username']) && isset($_POST['password'])) {
    include "../php/db_connect.php";
    session_start();
    $user = $_POST['username'];
    $password = $_POST['password'];

    //select user from admin table
    $sql = "SELECT * FROM admin WHERE user = '$user'";
    $result = $mysqli->query($sql);
    $row = $result->fetch_assoc();
    $loginTry = $row['loginTry'];
    if ($row["loginTry"] >= 3) {
        $lastTryDate = $row["lastTry"];
        date_default_timezone_set('America/Sao_Paulo');
        $now = date("Y-m-d H:i:s", time());
        $diff = strtotime($now) - strtotime($lastTryDate);
        $awaitTime = 60 * 1; //5 minutes
        if ($diff >= $awaitTime) {
            $sql = "UPDATE admin SET loginTry = 0, lastTry = '$now' WHERE user = '$user'";
            $mysqli->query($sql);
            header("Location: login.php?error=1&time=0");
        }else{

            header("Location: login.php?error=1&time=" . ($awaitTime - $diff));
        }
    } else {
        //verify md5($password) == $row["password"]
        if (md5($password) == $row["password"]) {
            //verify if user is admin
            $now = date("Y-m-d H:i:s");
            if ($row["admin"] == 1) {
                //set session admin
                $_SESSION['admin'] = 1;
                $_SESSION['user'] = $user;
                //update last login date
                $sql = "UPDATE admin SET  loginTry = 0 WHERE user = '$user'";
                $result = $mysqli->query($sql);
                if (!$result) {
                    echo "Erro ao atualizar tentativas de login.";
                }else{
                    header("Location: login.php");
                }

            } else {
                //set session user
                $_SESSION['user'] = $user;
                $_SESSION['admin'] = 0;
                //update last login date and logintry = 0
                $sql = "UPDATE admin SET loginTry = 6 WHERE user = '$user'";
                $result = $mysqli->query($sql);
                if (!$result) {
                    echo "Erro ao atualizar tentativas de login.";
                }else{
                    header("Location: login.php");
                }


                //redirect to user page

            }
        } else {
            //update login try
            $loginTry = $loginTry + 1;
            $sql = "UPDATE admin SET  loginTry = '$loginTry' WHERE user = '$user'";
            $result = $mysqli->query($sql);
            if (!$result) {
                echo "Erro ao atualizar tentativas de login.";
            }
            header("Location: login.php?error=0");
        }
        $mysqli->close();
        exit;
    }
}else{
    header("Location: login.php");
}
