<?php
//connect to database
include 'db_connect.php';
    $stmt = $mysqli->prepare("SELECT * FROM carousel");
    $stmt->execute();
    $result_ = $stmt->get_result();
    if ($result_->num_rows > 0) {
        $data = array();
        while($row = $result_->fetch_assoc()) {
            $data[] = $row;
        }
    }
    if(!$data){
        die(json_encode(array('status' => 'error')));
    }else{   
        die (json_encode($data));
    }