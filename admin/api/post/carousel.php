<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 2) {
    die(json_encode(array('status' => 403,
    'message' => 'Acesso negado')));
}


include_once '../config/db_connect.php';


class Carousel extends dbConnect
{
    public function __construct()
    {
    }

    public function delete($id)
    {
        $mysqli = $this->connect();
        $stmt = "DELETE FROM carousel WHERE id = ?";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            return ((array("status" => 202)));
        } else {
            $stmt->close();
            return ((array("status" => 500)));
        }
    }
    public function create($category, $type, $select)
    {
        $mysqli = $this->connect();


        $stmt = "SELECT * from categories WHERE id = ?";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("i", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows == 0) {
            return ((array("status" => 400, "error" => "Category does not exist")));
        }

        $stmt = "SELECT * FROM carousel WHERE category = ?";
        $stmt = $mysqli->prepare($stmt);
        $stmt->bind_param("i", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $update = 1;
        } else {
            $update = 0;
        }

        $select = [];

        if ($type == "id") {
            $select = json_encode($_POST["select"]);
        } else if ($type = "auto") {
            $select["type"] = ($_POST["select"]);
            $select = json_encode($select);
        }


        $sql = ($update == 1) ? "UPDATE `carousel` SET `SelectType`=?,`select`=? WHERE `category` = ?" : "INSERT INTO carousel (`category`, `SelectType`, `select`) VALUES (?, ?, ?)";


        if ($update == 1) {
            $params = [$type, $select, $category];
        } else {
            $selectType["type"] = $select;
            if ($type == "auto") {
                $params = [$category, $type, ($selectType["type"])];
            } else {
                $ids = array();
                $select = json_decode($select);

                foreach ($select as $key => $value) {
                    $ids["'" . $key . "'"] = $value;
                }
                $params = [$category, $type, json_encode($ids)];
            }
        }
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", ...$params);
        if ($stmt->execute()) {
            return ((array("status" => 201, "message" => "Carrossel criado com sucesso")));
        } else {
            die((array("status" => 500, "error" => "Erro ao criar carrossel")));
        }
    }
}





if (isset($_POST["category"]) && isset($_POST["SelectType"]) && isset($_POST["select"])) {
    $category = $_POST["category"];
    $type = $_POST["SelectType"];
    $select = $_POST["select"];
    $carousel = new Carousel();
    die(json_encode($carousel->create($category, $type, $select)));
} else if (isset($_POST["deleteGlider"])) {
    $id = $_POST["deleteGlider"];
    $carousel = new Carousel();
    die(json_encode($carousel->delete($id)));
} else {
    die(json_encode(array("status" => 400, "error" => "Parâmetros inválidos")));
}
