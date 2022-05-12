
<?php

if(isset($_GET["id"])) {
    $id = $_GET["id"];
    $id = intval($id);
    $sql = "DELETE FROM products WHERE id = ?";
    include '../db_connect.php';
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    if($stmt->affected_rows > 0) {
        $stmt->close();
        die(json_encode(array("status" => "success")));
    } else {
        $stmt->close();
        die(json_encode(array("status" => "error")));
    }
}else{
    die(json_encode(array("status" => "error")));
}