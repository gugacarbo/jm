<?php
//include db and take category from db and returns as json and close connection
include 'db_connect.php';
$sql = "SELECT * FROM categories";
$result = $mysqli->query($sql);
$data = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}
$mysqli->close();
die(json_encode($data, JSON_UNESCAPED_UNICODE));
?>

