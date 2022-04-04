<?php
include('php/db_connect.php');
$cats = $_GET['cat'];

$queryCat = "";

foreach($cats as $k => $cat){
    //echo($cat);
    $qc = ($cat == 1 ? "`categoria` = " . $k . " || " : "");
    $queryCat .= $qc;
}

$queryCat .= "id = -1";


$query = "SELECT * FROM `roupas`  WHERE " . ($queryCat) ;
//echo $query;
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
