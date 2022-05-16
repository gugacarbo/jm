<?php
if (isset($_GET['sCepDestino']) && isset($_GET['nVlPeso'])) {

    $nVlPeso = ($_GET['nVlPeso']);
    $sCepDestino =  $_GET['sCepDestino'];
    $nVlPeso = $_GET['nVlPeso'];



    $frete = getFrete($sCepDestino, $nVlPeso);
    die(json_encode($frete));
} else {
    //die('{"erro": "Parametros não recebidos"}');
}


function getfrete($sCepDestino, $nVlPeso)
{   
    $replace = array("‑", ".", " ", "," . "-");

    $sCepDestino =  str_replace($replace, "", $sCepDestino);

    $nVlPeso = floatval($nVlPeso);
    //only 3 decimal places
    $nVlPeso = round($nVlPeso, 3);
    include "db_connect.php";
    $GconfigTake = ["cepOrigemFrete", "aditionalWeight", "alturaFrete", "larguraFrete", "comprimentoFrete", "freteGratis"];
    $config = array();

    foreach ($GconfigTake as $key => $value) {

        $sql = "SELECT value FROM generalConfig WHERE config = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $stmt->bind_result($config[$value]);
        $stmt->fetch();
        $stmt->close();
    }
    $config['cepOrigemFrete'] = str_replace($replace, "", $config['cepOrigemFrete']);


    $frete = array();
    $local = getCidade($sCepDestino);

    $freteGratis = json_decode($config['freteGratis'], TRUE);

    if ($freteGratis["use"] == "true") {

        foreach ($freteGratis["cidades"] as $key => $value) {
            if (strtolower(tirarAcentos($local->localidade)) == strtolower(tirarAcentos($value))) {
                $frete['freteGratis'] = true;
            }
        }
        foreach ($freteGratis["estados"] as $key => $value) {
            if (strtolower($local->uf) == strtolower($value)) {
                $frete['freteGratis'] = true;
            }
        }
    }


    $frete["local"] = $local;
    $query = array(
        'nCdEmpresa' => '',
        'sDsSenha' => '',
        'nCdServico' => '04510', // * 04510 pac
        'sCepOrigem' => $config["cepOrigemFrete"],
        'sCepDestino' => $sCepDestino,
        'nVlPeso' => $nVlPeso + $config["aditionalWeight"],
        'nCdFormato' => '1',
        'nVlComprimento' => $config["comprimentoFrete"],
        'nVlAltura' => $config["alturaFrete"],
        'nVlLargura' => $config["larguraFrete"],
        'sCdMaoPropria' => 'n',
        'nVlValorDeclarado' => '0',
        'sCdAvisoRecebimento' => 'n',
        'nVlDiametro' => '0',
        'StrRetorno' => 'xml',
        'nIndicaCalculo' => '3'
    );

    $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?" . http_build_query($query);

    $xml = simplexml_load_file($url);

    $frete['valorPac'] = $xml->cServico->Valor;
    $frete['prazoPac'] = $xml->cServico->PrazoEntrega;
    $frete["erro"] =  $xml->cServico->Erro;


    $query['nCdServico'] = '04014'; // * 04014 sedex
    $url2 = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?" .  http_build_query($query);

    $xml2 = simplexml_load_file($url2);
    $frete['valorSedex'] = $xml2->cServico->Valor;
    $frete['prazoSedex'] = $xml2->cServico->PrazoEntrega;
    $frete["erro2"] =  $xml2->cServico->Erro;
    return $frete;
}


function getCidade($cep)
{
    $cep = str_replace(["-", ".", " "], "", $cep);
    $url = "https://viacep.com.br/ws/$cep/xml/";
    $xml = simplexml_load_file($url);
    $cidade = $xml;

    return $cidade;
}


function tirarAcentos($string)
{
    return preg_replace(array("/(á|à|ã|â|ä)/", "/(Á|À|Ã|Â|Ä)/", "/(é|è|ê|ë)/", "/(É|È|Ê|Ë)/", "/(í|ì|î|ï)/", "/(Í|Ì|Î|Ï)/", "/(ó|ò|õ|ô|ö)/", "/(Ó|Ò|Õ|Ô|Ö)/", "/(ú|ù|û|ü)/", "/(Ú|Ù|Û|Ü)/", "/(ñ)/", "/(Ñ)/"), explode(" ", "a A e E i I o O u U n N"), $string);
}


