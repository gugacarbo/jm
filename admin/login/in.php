<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$adminPass = "admin";
$adminHashCode = md5($adminPass);
$difficult = isset($_SESSION["dif"]) ? $_SESSION["dif"] : 0;


if (isset($_SESSION["loginTry"]) && $_SESSION["loginTry"] >= 10) {
    $interval = time() -  $_SESSION["lastLoginTry"];
    if ($interval > (60 * pow(2, $difficult))) {

        $_SESSION["loginTry"] = 0;
        $_SESSION["dif"] = isset($_SESSION["dif"]) ? $_SESSION["dif"] + 1 : 2;
    }
    header("Location: login.php?error=Muitas tentativas, tente novamente em " . ((60 * pow(2, $difficult)) - $interval) . " segundos.");
    die(json_encode(array('status' => 401, 'message' => 'Too many login attempts. Please try again in ' . ((60 * pow(2, $difficult)) - $interval) . ' seconds.')));
} else {
    $_SESSION["loginTry"] = isset($_SESSION["loginTry"]) ? $_SESSION["loginTry"] + 1 : 1;
    $_SESSION["lastLoginTry"] = time();

    if (isset($_POST['username']) && isset($_POST['password'])) {

        include "../api/config/db_config.php";
        $mysqli = new mysqli($server, $user, $password, $dbname);
        if (!$mysqli) {
            die("Falha na conexao: " . mysqli_connect_error());
        }
        $username = $_POST['username'];
        $password = md5($_POST['password']);

        $stmt = $mysqli->prepare("SELECT admin,password FROM admin WHERE user = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            header("Location: login.php?error=Usuário ou senha inválidos.");
            die(json_encode(array('status' => 403, 'message' => 'Invalid username or password.')));
        } else {
            $row = $result->fetch_assoc();

            $tries = $row["loginTry"] + 1;
            if ($tries < 4) {
                if ($row['password'] == $password) {

                    $_SESSION['user'] = $username;
                    $_SESSION['admin'] = $row['admin'];
                    $_SESSION['hashCode'] = $adminHashCode;

                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = 0, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    header("Location: login.php");
                    die(json_encode(array('status' => 200)));
                } else {

                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = ?, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("is", $tries, $username);
                    $stmt->execute();
                    header("Location: login.php?error=Usuário ou senha inválidos.");
                    die(json_encode(array('status' => 403, 'message' => 'Invalid username or password.')));
                }
            } else {
                $interval2 = strtotime(date("Y-m-d H:i:s")) - strtotime($row["lastTry"]);
                if ($interval2 > 60) {
                    $stmt = $mysqli->prepare("UPDATE admin SET loginTry = 0, lastTry = NOW() WHERE user = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                }
                header("Location: login.php?error=Muitas tentativas, tente novamente em " . ((60 * pow(2, $difficult)) - $interval2) . " segundos.");
                die(json_encode(array('status' => 401, 'message' => 'Too many login attempts. Please try again in ' . (60 - $interval2) . ' seconds.')));
            }
        }

        $username = $_POST['username'];
        $password = $_POST['password'];
    } else {
        header("Location: login.php");
    }
}
