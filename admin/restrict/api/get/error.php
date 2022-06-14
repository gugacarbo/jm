<?php
//header('Content-Type: application/xls');
//header('Content-Disposition: attachment; filename=info.xls');

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 4) {
    //header("Location: ../index.php");
    die(json_encode(array('status' => 403, 'message' => 'Forbidden')));
}
include_once "../../../api/config/db_connect.php";



class errorlog extends dbConnect
{

    public function __construct()
    {
  
    }
    public function getAll(){
        $mysqli = $this->connect();
        $data   = array();
        $stmt = $mysqli->prepare("SELECT * FROM error_log");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $row["message"] = json_decode($row["message"]);
            $row["message"]->server = json_decode($row["message"]->server);
            $data[] = $row;
        }

        die(json_encode(array('status' => 200, 'data' => ($data)))); 
    }
    public function getId($id){
        $mysqli = $this->connect();
        $data   = array();
        $stmt = $mysqli->prepare("SELECT * FROM error_log WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $row["message"] = json_decode($row["message"]);
            $row["message"]->server = json_decode($row["message"]->server);
            $data[] = $row;
        }

        die(json_encode(array('status' => 200, 'data' => ($data)))); 
    }


}
$errorlog = new errorlog();

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $errorlog->getId($id);
}else{
    $errorlog->getAll();
}


