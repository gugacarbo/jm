<?php

//Verify if isset Receive sCepDestino and nVlPeso from get
if(isset($_GET['sCepDestino']) && isset($_GET['nVlPeso'])){
    $sCepDestino = $_GET['sCepDestino'];
    $nVlPeso = $_GET['nVlPeso'];
    $frete = getFrete($sCepDestino, $nVlPeso);

    echo json_encode($frete);
}else{
    die('{"erro": "Parametros não recebidos"}');
}

//Array with  "nCdEmpresa", "sDsSenha", "nCdServico","sCepOrigem","nCdFormato","nVlComprimento","nVlAltura","nVlLargura","sCdMaoPropria","nVlValorDeclarado","sCdAvisoRecebimento","nVlDiametro","StrRetorno","nIndicaCalculo"

function getfrete($sCepDestino, $nVlPeso){
    $query = http_build_query(array(
        'nCdEmpresa' => '',
        'sDsSenha' => '',
        'nCdServico' => '04510', //sedex 04014, 04510 pac
        'sCepOrigem' => '88504000',
        'sCepDestino' => $sCepDestino,
        'nVlPeso' => $nVlPeso + 0.3,
        'nCdFormato' => '1',
        'nVlComprimento' => '15',
        'nVlAltura' => '10',
        'nVlLargura' => '10',
        'sCdMaoPropria' => 'n',
        'nVlValorDeclarado' => '0',
        'sCdAvisoRecebimento' => 'n',
        'nVlDiametro' => '0',
        'StrRetorno' => 'xml',
        'nIndicaCalculo' => '3'
    ));

    $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?".$query;
    $xml = simplexml_load_file($url);
    $frete = array();
    $frete['valorPac'] = $xml->cServico->Valor;
    $frete['prazoPac'] = $xml->cServico->PrazoEntrega;
    $frete["erro"] =  $xml->cServico->Erro;


    $queryF = http_build_query(array(
        'nCdEmpresa' => '',
        'sDsSenha' => '',
        'nCdServico' => '04014', //sedex 04014, 04510 pac
        'sCepOrigem' => '88504000',
        'sCepDestino' => $sCepDestino,
        'nVlPeso' => $nVlPeso + 0.3,
        'nCdFormato' => '1',
        'nVlComprimento' => '15',
        'nVlAltura' => '10',
        'nVlLargura' => '10',
        'sCdMaoPropria' => 'n',
        'nVlValorDeclarado' => '0',
        'sCdAvisoRecebimento' => 'n',
        'nVlDiametro' => '0',
        'StrRetorno' => 'xml',
        'nIndicaCalculo' => '3'
    ));
    $url2 = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?".$queryF;

    $xml2 = simplexml_load_file($url2);
    $frete['valorSedex'] = $xml2->cServico->Valor;
    $frete['prazoSedex'] = $xml2->cServico->PrazoEntrega;
    $frete["cidade"] = getCidade($sCepDestino);
    $frete["erro2"] =  $xml2->cServico->Erro;
    return json_encode($frete,  JSON_UNESCAPED_UNICODE);
}


//function take the city by cep
function getCidade($cep){
    $url = "http://cep.republicavirtual.com.br/web_cep.php?cep=".$cep."&formato=xml";
    $xml = simplexml_load_file($url);
    $cidade = $xml;
    return $cidade;
}