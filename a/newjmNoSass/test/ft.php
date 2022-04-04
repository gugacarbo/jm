<?php 

ini_set('default_charset', 'UTF-8');
 //incluo a classe correios 
 include('Correios.class.php');
  //instancio a classe correios 
  $frete = new Correios(); 
  $frete->servico = '40010';
  //tipo de servicos, ou seja, sedex, pac, sedex 10, esses codigos voce encontra no PDF que mencionei acima 
  
  
  $frete->cepOrigem = '12970000';//cep de origem, ou seja, de onde parte 
  $frete->cepDestino = '12942770';//cep destino, ou seja, para onde vai ser mandado 
  $frete->peso = '2';//peso em kilogramas 
  $frete->comprimento = '80';//em cm 
  $frete->altura = '20';//em cm 
  $frete->largura = '20';//em cm 
  $frete->diametro = '91';//em cm 
  
  //chamo meu metodo para calcular 
  $calc = $frete->calc(); //verifica se foi calculado, se sim damos um var_dump pra mostrar na tela o xml retornado 
  print_r($calc);
  if(!$calc){ 
      //$error = $frete->error(); 
      //echo $error[1]; 
      echo ("erro"); 
    }else{ 
        var_dump($calc); 
    
    }
