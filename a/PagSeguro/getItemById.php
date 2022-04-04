<?php
    function getbyId($id){
    $item['id'] = $id;
    $item['description'] = "2".$id;
    $item['amount'] = "3.34";
    $item['quantity'] = "2";
    $item['weight'] = "500";
    return($item);
}
?>