<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');
session_start();
if (isset($_SESSION["loginTry"]) && $_SESSION["loginTry"] >= 10) {
    $interval = time() -  $_SESSION["lastLoginTry"];
    if ($interval > 60) {
        $_SESSION["loginTry"] = 0;
    }
    die(json_encode(array('status' => 401, 'message' => 'Too many login attempts. Please try again in ' . (60 - $interval) . ' seconds.')));
} else {
    $_SESSION["loginTry"] = isset($_SESSION["loginTry"]) ? $_SESSION["loginTry"] + 1 : 1;
    $_SESSION["lastLoginTry"] = time();

    if (isset($_POST['username']) && isset($_POST['password'])) {
        include "api/config/db_config.php";
        $mysqli = new mysqli($server, $user, $password, $dbname);
        if (!$mysqli) {
            die("Falha na conexao: " . mysqli_connect_error());
        }
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $stmt = $mysqli->prepare("SELECT * FROM admin WHERE user = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            die(json_encode(array('status' => 403, 'message' => 'Invalid username or password.')));
        } else {
            $row = $result->fetch_assoc();

            $tries = $row["loginTry"] + 1;
            if ($tries < 4) {
                if ($row['password'] == $password) {
                    $_SESSION['user'] = $username;
                    $_SESSION['admin'] = $row['admin'];
                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = 0, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    header("Location: login.php");
                    die(json_encode(array('status' => 200)));
                } else {

                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = ?, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("is", $tries, $username);
                    $stmt->execute();

                    die(json_encode(array('status' => 403, 'message' => 'Invalid username or password.')));
                }
            } else {
                $interval2 = strtotime(date("Y-m-d H:i:s")) - strtotime($row["lastTry"]);
                if ($interval2 > 60) {
                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = 0, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                }
                die(json_encode(array('status' => 401, 'message' => 'Too many login attempts. Please try again in ' . (60 - $interval2) . ' seconds.')));
            }
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
    } else {
        header("Location: login.php");
    }
}
