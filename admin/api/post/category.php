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
//include "mailTracking.php";


class category extends dbConnect
{
    public function __construct()
    {
    }
    public function create($name)
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT * FROM categories WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            return ((array("status" => 400, "message" => "Categoria já existe")));
        } else {
            $stmt = $mysqli->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $id = $mysqli->insert_id;
            if ($stmt->affected_rows > 0) {
                return ((array("status" => 200, "message" => "Categoria adicionada com sucesso", "id" => $id)));
            } else {
                return ((array("status" => 500, "message" => "Erro ao adicionar categoria")));
            }
        }
    }
    public function edit($old, $new)
    {
        $mysqli = $this->connect();

        $stmt = $mysqli->prepare("UPDATE categories SET name = ? WHERE name = ?");
        $stmt->bind_param("ss", $new, $old);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            return ((array("status" => 200, "message" => "Categoria alterada com sucesso")));
        } else {
            return ((array("status" => 500, "message" => "Erro ao alterar categoria")));
        }
    }
    public function delete($delCat)
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM products WHERE category = ?");
        $stmt->bind_param("s", $delCat);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->fetch_row()[0] > 0){
            $stmt->close();
            return ((array("status" => 400, "message" => "Categoria não pode ser excluída")));
        }
        $stmt->close();
        
        $stmt = $mysqli->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("s", $delCat);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            die(json_encode(array("status" => 202, "message" => "Categoria removida com sucesso")));
        } else {
            die(json_encode(array("status" => 500, "message" => "Erro ao remover categoria")));
        }
    }
}


if (isset($_POST["newCat"])) {
    $cat = new category();
    if (isset($_POST["oldCat"])) {
        die(json_encode($cat->edit($_POST["oldCat"], $_POST["newCat"])));
    } else {
        die(json_encode($cat->create($_POST["newCat"])));
    }
} else if (isset($_POST["delCat"])) {
    $cat = new category();
    die(json_encode($cat->delete($_POST["delCat"])));
} else {
    die(json_encode(array('status' => 400)));
}
