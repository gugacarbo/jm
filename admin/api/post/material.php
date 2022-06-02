<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: POST');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 2) {
    die(json_encode(array('status' => 403,
    'message' => 'Acesso negado')));
}


include_once '../config/db_connect.php';
//include "mailTracking.php";


class material extends dbConnect
{
    public function __construct()
    {
    }
    public function create($name)
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM material WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return ((array("status" => 400, "message" => "Material já existe")));
        } else {
            $stmt = $mysqli->prepare("INSERT INTO material (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $id = $mysqli->insert_id;
            if ($stmt->affected_rows > 0) {
                return ((array("status" => 200, "message" => "Material adicionada com sucesso", "id" => $id)));
            } else {
                return ((array("status" => 500, "message" => "Erro ao adicionar categoria")));
            }
        }
    }
    public function edit($old, $new)
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("UPDATE material SET name = ? WHERE name = ?");
        $stmt->bind_param("ss", $new, $old);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return ((array("status" => 200, "message" => "Material alterado com sucesso")));
        } else {
            return ((array("status" => 500, "message" => "Erro ao alterar material")));
        }
    }
    public function delete($delMat)
    {
        $mysqli = $this->connect();

        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM products WHERE material = ?");
        $stmt->bind_param("s", $delMat);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->fetch_row()[0] > 0){
            $stmt->close();
            return ((array("status" => 400, "message" => "Material não pode ser excluído")));
        }
        $stmt->close();

        $stmt = $mysqli->prepare("DELETE FROM material WHERE id = ?");
        $stmt->bind_param("s", $delMat);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return((array("status" => 202, "message" => "Material removido com sucesso")));
        } else {
            return((array("status" => 500, "message" => "Erro ao remover material")));
        }
    }
}

if (isset($_POST["newMat"])) {
    $cat = new material();
    if (isset($_POST["oldMat"])) {
        die(json_encode($cat->edit($_POST["oldMat"], $_POST["newMat"])));
    } else {
        die(json_encode($cat->create($_POST["newMat"])));
    }
} else if (isset($_POST["delMat"])) {
    $cat = new material();
    die(json_encode($cat->delete($_POST["delMat"])));
} else {
    die(json_encode(array('status' => 400)));
}
