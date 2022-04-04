<?php
//connect to database
include 'db_connect.php';

//get All gliders from db with no filter
    $res = $mysqli->query("SELECT * FROM carousel");
    $itens = $res->fetch_all(MYSQLI_ASSOC);
    echo json_encode($itens);