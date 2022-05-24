<?php
header('Content-Type: application/json; charset=utf-8');

include "../config/db_connect.php";

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status >= 3 AND status <= 4");
$stmt->execute();
$stmt->bind_result($totalAprovadas);
$stmt->fetch();
$stmt->close();
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 6 || status = 5");
$stmt->execute();
$stmt->bind_result($totalCanceladas);
$stmt->fetch();
$stmt->close();
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status >=3 AND status <= 4 AND trackingCode = ''");
$stmt->execute();
$stmt->bind_result($totalAguardandoEnvio);
$stmt->fetch();
$stmt->close();
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 1 || status = 2");
$stmt->execute();
$stmt->bind_result($totalAguardandoPagamento);
$stmt->fetch();
$stmt->close();


$stmt = $mysqli->prepare("SELECT COUNT(*) FROM checkout_data WHERE payload = '{}' and buy_date > DATE_SUB(NOW(), INTERVAL 2 DAY)");
$stmt->execute();
$stmt->bind_result($totalNaoPagos);
$stmt->fetch();
$stmt->close();


$stmt = $mysqli->prepare("SELECT COUNT(*) FROM nofinalizedpurchases WHERE 1");
$stmt->execute();
$stmt->bind_result($totalNaoPagos2);
$stmt->fetch();
$stmt->close();

$totalNaoPagos = $totalNaoPagos + $totalNaoPagos2;



die(json_encode(array('status' => 200, 'Aprovadas' => $totalAprovadas, 'Canceladas' => $totalCanceladas, 'AguardandoEnvio' => $totalAguardandoEnvio, 'NaoPagos' => $totalNaoPagos, 'AguardandoPagamento' => $totalAguardandoPagamento)));
