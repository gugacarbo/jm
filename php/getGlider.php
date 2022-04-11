<?php
//connect to database
include 'db_connect.php';

//get All gliders from db with no filter
    $res = $mysqli->query("SELECT * FROM carousel");
    $itens = $res->fetch_all(MYSQLI_ASSOC);
    $mysqli->close();
    if(!$itens){
        echo json_encode(array('status' => 'error'));
    }else{   
        die (json_encode($itens));
    }