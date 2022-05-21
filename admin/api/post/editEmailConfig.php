<?php

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["adminMail"]) && isset($_POST["contactMail"]) && isset($_POST["automaticMail"]) && isset($_POST["automaticPass"])) {
    include "../config/db_connect.php";

    $configs = [
        "adminMail" => $_POST["adminMail"],
        "contactMail" => $_POST["contactMail"],
        "automaticMail" => $_POST["automaticMail"],
        "automaticMailPass" => $_POST["automaticPass"],
        "sendToAdminMail" => isset($_POST["sendToAdminMail"]) ? json_encode($_POST["sendToAdminMail"]) : "[]"
    ];
    foreach ($configs as $key => $value) {
        if ($key == "automaticMailPass" && $value == "") {
            continue;
        }
        $sql = "UPDATE generalconfig SET value = ? WHERE config = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
        $stmt->close();
    }
    die(json_encode(array('status' => 200)));
} else {
    die(json_encode(array("status" => 400, "message" => "Bad Request")));
}
