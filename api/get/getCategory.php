<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


include_once '../config/db_connect.php';

class categories extends dbConnect
{

    public function __construct()
    {
        $mysqli = $this->Conectar();
        $stmt = $mysqli->prepare("SELECT * FROM categories");
        $stmt->execute();
        $result_ = $stmt->get_result();
        if ($result_->num_rows > 0) {
            $data = array();
            while ($row = $result_->fetch_assoc()) {
                $data[] = $row;
            }
        } else {
            $data = array();
        }
        $mysqli->close();
        return(json_encode($data, JSON_UNESCAPED_UNICODE));
    }
}

die((new categories())->__construct());