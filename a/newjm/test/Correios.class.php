<?php
class Correios
{
    public $servico,
        $cepOrigem,
        $cepDestino,
        $peso,
        $formato = '1',
        $comprimento,
        $altura,
        $largura,
        $diametro,
        $maoPropria = 'N',
        $valordeclarado = '0',
        $avisoRecebimento = 'N',
        $retorno = 'xml';

    public function calc()
    {
        $cURL = curl_init(
            sprintf(
                '//ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdServico=%s&sCepOrigem=%s&sCepDestino=%s&nVlPeso=%s&nCdFormato=%s&nVlComprimento=%s&nVlAltura=%s&nVlLargura=%s&nVlDiametro=%s&sCdMaoPropria=%s&nVlValorDeclarado=%s&sCdAvisoRecebimento=%s&StrRetorno=%s',
                $this->servico,
                $this->cepOrigem,
                $this->cepDestino,
                $this->peso,
                $this->formato,
                $this->comprimento,
                $this->altura,
                $this->largura,
                $this->diametro,
                $this->maoPropria,
                $this->valordeclarado,
                $this->avisoRecebimento,
                $this->retorno
            )
        ); // Define a opção que diz que você quer receber o resultado encontrado 
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true); // Executa a consulta, conectando-se ao site e salvando o resultado na variável $string 
        $string = curl_exec($cURL);
        print_r($string);
        // Encerra a conexão com o site 
        curl_close($cURL);

        $xml = simplexml_load_string($string);
       /*  if ($xml->{'Erro'} != '') {
            $this->error = array($xml->cServico->Erro, $xml->cServico->MgsErrro);
            return false;
        } else {*/
            return $xml;
        }
    /*}

    public function error()
    {
        if (is_null($this->error)) {
            return false;
        } else {
            return $this->error;
        }
    }*/
}
?>