<?php
header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["category"]) && isset($_POST["SelectType"]) && isset($_POST["select"])) {
    include "../config/db_connect.php";
    $category = $_POST["category"];
    $type = $_POST["SelectType"];

    $stmt = "SELECT * from categories WHERE id = ?";
    $stmt = $mysqli->prepare($stmt);
    $stmt->bind_param("i", $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    if ($result->num_rows == 0) {
        die(json_encode(array("status" => 400, "error" => "Category does not exist")));
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

    if ($type == "id") {
        $select = json_encode($_POST["select"]);
    } else if ($type = "auto") {
        $select["type"] = ($_POST["select"]);   
        $select = json_encode($select);
    }

    $sql = ($update == 1) ? "UPDATE `carousel` SET `SelectType`=?,`select`=? WHERE `category` = ?" : "INSERT INTO carousel (`category`, `SelectType`, `select`) VALUES (?, ?, ?)";
     if($update == 1){
        $params = [$type, $select, $category];
     }else {
      $selectType["type"] = $select;
      if ($type == "auto") {
        $params = [$category, $type, ($selectType["type"])];
      }else{
        $ids = array();
        $select = json_decode($select);

        foreach ($select as $key => $value) {
            $ids["'".$key."'"] = $value;
        }
        $params = [$category, $type, json_encode($ids)];
      }
    }
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sss", ...$params);
    if($stmt->execute()){
        die(json_encode(array("status" => 201, "error")));
    }else{
        die(json_encode(array("status" => 201, "error" => "Carousel not updated")));
    }
}
