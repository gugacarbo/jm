<?php
include('db_connect.php');

$id = $_GET['id'];
$query = "SELECT * FROM `roupas` WHERE `id` = " .$id;
$prods = "";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        foreach($row as $key => $col){
            $col_array[$key] = utf8_encode($col);
         }
         $row_array[] =  $col_array;
    }
    echo json_encode($row_array);
} else {
    echo "{}";
}
$conn->close();
die();
?>
