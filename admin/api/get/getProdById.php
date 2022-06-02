
<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once  '../config/db_connect.php';
class Prods extends dbConnect
{
    private $mysqli;

    public function __construct()
    {
        $this->mysqli = $this->connect();
    }
    public function getById($id_)
    {
        $id = preg_replace('/[a-z\|\,\;\@\:"]+/', '', $id_);
        $id = mysqli_real_escape_string($this->mysqli, $id);

        $stmt = $this->mysqli->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $stmt->close();
            return (array('status' => 400, 'message' => 'Produto não encontrado'));
        }
        $itens = $result->fetch_assoc();
        $stmt->close();



        $materialId = $itens['material'];
        $categoryId = $itens['category'];


        $stmt = $this->mysqli->prepare("SELECT * FROM material WHERE id = ?");
        $stmt->bind_param("s", $materialId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $material["name"] = "N/A";
        } else {
            $material = $result->fetch_assoc();
            $stmt->close();
        }

        $stmt = $this->mysqli->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->bind_param("s", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows <= 0) {
            $category["name"] = "N/A";
        } else {
            $category = $result->fetch_assoc();
            $stmt->close();
        }


        $ret = $itens;
        $ret["imgs"] = (json_decode($itens["imgs"]));
        $ret["options"] = (json_decode($itens["options"]));
        return ($ret);
    }
}


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
        $r = new Prods();
        die(json_encode($r->getById($_GET['id']), JSON_UNESCAPED_UNICODE));
    } else {
        die(json_encode(array('status' => 400)));
    }
}
