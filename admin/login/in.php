<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}



include "../api/config/db_config.php";
class login extends dbConfig
{
    private $username, $password, $maxTry = 3, $timeLimit = 0.5 /*Min */;
    public function __construct($username, $password)
    {

        $this->username = $username;
        $this->password = md5($password);
    }


    public function try()
    {
        $mysqli = $this->dbCon();


        $stmt = $mysqli->prepare("SELECT admin, password, loginTry,name FROM ds_adm_user WHERE user = ?");

        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows == 0) {
            header("Location: logout.php?error=Usuário ou senha inválidos.");
            die(json_encode(array('status' => 401, 'message' => 'Usuário ou  senha incorretos.')));
        } else {
            $row = $result->fetch_assoc();

            $loginTries = json_decode($row["loginTry"]);

            $Ip = $_SERVER['REMOTE_ADDR'];
            $Data = date("Y/m/d");
            $Hora = date("H:i:s");

            if (isset($loginTries->$Ip)) {
                $try = $loginTries->$Ip->try;

                if ($try >= $this->maxTry) {
                    $time = $loginTries->$Ip->hora;

                    $time = strtotime($time);
                    $timeout = ($this->timeLimit * 60);
                    $timeout = isset($_SESSION['dif']) ? ($_SESSION['dif'] > 0 ? $_SESSION['dif'] : 1) * $timeout : $timeout;
                    $interval = time() - $time;

                    if ($interval < $timeout) {
                        header("Location: logout.php?error=Usuário ou senha inválidos. Você tentou muitas vezes.");
                        die(json_encode(array('status' => 401, 'message' => 'Você excedeu o número de tentativas de login.')));
                    } else {
                        $loginTries->$Ip = array(
                            'try' => 1,
                            'data' => $Data,
                            'hora' => $Hora
                        );
                    }
                } else {
                    $loginTries->$Ip = array('try' => $try + 1, 'data' => $Data, 'hora' => $Hora);
                }
            } else {
                $loginTries->$Ip = array(
                    'try' => 1,
                    'data' => $Data,
                    'hora' => $Hora
                );
            }

            $loginTries = json_encode($loginTries);

            $stmt = $mysqli->prepare("UPDATE ds_adm_user SET loginTry = ? WHERE user = ?");
            $stmt->bind_param("ss", $loginTries, $this->username);
            $stmt->execute();
            $stmt->close();


            if ($row['password'] == $this->password) {

                $loginTries = json_decode($loginTries);
                $loginTries->$Ip = array(
                    'try' => 0,
                    'data' => $Data,
                    'hora' => $Hora
                );
                $loginTries = json_encode($loginTries);

                $stmt = $mysqli->prepare("UPDATE ds_adm_user SET loginTry = ? WHERE user = ?");
                $stmt->bind_param("ss", $loginTries, $this->username);
                $stmt->execute();
                $stmt->close();

                session_destroy();
                session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
                session_cache_expire(60);
                session_start();

                $_SESSION['username'] = $row['name'];
                $_SESSION['user'] = $this->username;
                $_SESSION['admin'] = $row['admin'];

                header("Location: ../index.php");
                die(json_encode(array('status' => 200)));
            } else {
                header("Location: index.php?error=Usuário ou senha inválidos.");
                die(json_encode(array('status' => 401, 'message' => 'Usuário ou  senha incorretos.')));
            }
        }
    }

    private function dbCon()
    {
        $this->mysqli = new mysqli($this->JMserver, $this->JMuser, $this->JMpassword, $this->JMdbname);

        if (!$this->mysqli) {
            die(json_encode(array('status' => 500, 'message' => 'Error, try again later.')));
            return ((array('status' => 500, 'message' => 'Error, try again later.')));
        } else {
            return $this->mysqli;
        }
    }
}


$_SESSION["loginTry"] = 0;


if (isset($_SESSION["loginTry"]) && $_SESSION["loginTry"] >= 5) {

    //!Taxa do aumento de espera para a próxima tentativa
    $difficult = isset($_SESSION["dif"]) ? $_SESSION["dif"] : 0;

    $interval = time() -  $_SESSION["lastLoginTry"];

    $timeToTry = 60 * pow(2, $difficult);

    if ($interval > $timeToTry) {

        $_SESSION["loginTry"] = 0;
        $_SESSION["dif"] = $difficult + 1;
    }

    $timeToTry = $timeToTry > 0 ? $timeToTry : 0;
    header("Location: logout.php?error=Muitas tentativas, tente novamente em " . ($timeToTry - $interval) . " segundos.");
    die(json_encode(array('status' => 401, 'message' => 'Too many login attempts. Please try again in ' . ($timeToTry - $interval) . ' seconds.')));
} else if (!isset($_POST['username']) && !isset($_POST['password'])) {
    header("Location: logout.php");
} else {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $login = new login($username, $password);
    $_SESSION["loginTry"] = isset($_SESSION["loginTry"]) ? $_SESSION["loginTry"] + 1 : 1;
    $_SESSION["lastLoginTry"] = time();
    $mysqli = $login->try();
}
