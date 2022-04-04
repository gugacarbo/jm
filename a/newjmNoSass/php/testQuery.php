<?php

$vowels = array("=", "/", "=", ":", ";");

$mainQuery = "SELECT * FROM produtos WHERE";

$filter = [];
$filter_val;

if (isset($_GET['nome'])) {
    $nome = str_replace($vowels, "" ,$_GET['nome']);
    $filter['nome'] = "nome = '" . $nome . "'";
}

if (isset($_GET['categoria'])) {
    $cat_name = $_GET['categoria'];
    $sql = "SELECT id FROM  categorias WHERE nome = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cat_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = $result->fetch_assoc();		
    $filter_id = $res['id'];					
    $filter_cat = intval($filter_id);


    $categoria = str_replace($vowels, "" , $filter_sql['id']);

    $filter['categoria'] = "categoria = '" . $categoria . "'";

}

if (isset($_GET['precoMin']) && isset($_GET['precoMax'])) {
    $precoMin = str_replace($vowels, "" ,$_GET['precoMin']);
    $precoMax = str_replace($vowels, "" ,$_GET['precoMax']);

    $filter['preco'] = "preco >= " . $precoMin . " AND preco <= " . $precoMax;


} else if (isset($_GET['precoMin'])) {
    $precoMin = str_replace($vowels, "" ,$_GET['precoMin']);
    $filter['preco'] = "preco >= " . $precoMin;
} else if (isset($_GET['precoMax'])) {
    $precoMax = str_replace($vowels, "" ,$_GET['precoMax']);
    $filter['preco'] = "preco <= " . $precoMax;
}


    $filterNum = count($filter);
    
    if ($filterNum > 0) {
    $query = "";
    $filterCount = 0;
    
    foreach ($filter as $filter) {
        $Lquery = $query;
        $query = $Lquery . " " . $filter;
        
        $filterCount++;

        if ($filterCount < $filterNum) {
            $query = $query . " AND ";
        }
    }


} else {
    $query = "1";
}

echo $query;
$mainQuery = $mainquery . $query;

echo $mainQuery;