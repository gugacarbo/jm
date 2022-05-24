<?php
header('Content-Type: application/json; charset=utf-8');


setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if (isset($_GET["interval"])) {
    $labels = array();
    $interval = $_GET["interval"];

    $data = array();

    switch ($interval) {
        case "trimester":
            $date = date('Y-m-1 0:0:0');

            for ($m = 0; $m <= 2; $m++) {

                $mon = intval(date('m', strtotime($date))) == intval(date('m', strtotime('-' . $m . ' months', strtotime($date)))) ? date("d") :  date('t', strtotime('-' . $m . ' months', strtotime($date)));
                for ($i = 1; $i <= $mon; $i++) {
                    $data[intval(date('m', strtotime('-' . $m . ' months', strtotime($date))))][$i] = 0;

                    if ($i % 6  == 0 || $i == 1) {
                        $labels[2 - $m][$i] = $i;
                    } else if ($i == 31) {
                        $labels[2 - $m][$i - 1] = $i;
                    }
                }

                $labels[2 - $m][1] =  utf8_encode(strftime('%B', strtotime('-' . $m . ' months', strtotime($date)))) . " 1";
            }
            //print_r($labels);
            $labels = array_merge($labels[0], $labels[1], $labels[2]);
            count($labels) < intval(date('d', strtotime("d"))) ? $labels[count($labels) - 1] = intval(date('d', strtotime("d"))) : '';
            $date = date('Y-m-d 0:0:0', strtotime('-2 months', strtotime($date)));

            break;


        case "year":
            $date = date('Y-0-1 0:0:0');
            for ($i = 1; $i <= date("m", strtotime(date('Y-m-d 0:0:0'))); $i++) {
                $data[$i] = 0;
                $labels[$i] = utf8_encode(strftime("%B", strtotime("+" . $i . " month", strtotime($date))));
            }
            $date = date('Y-1-1 0:0:0');
            break;


        default:
            $date = date('Y-m-1 0:0:0');
            for ($i = 1; $i <= date("d", strtotime(date('Y-m-d 0:0:0'))); $i++) {
                $data[$i] = 0;
                $labels[$i] = $i;
            }
            $labels[1] =  utf8_encode(strftime('%B', strtotime($date)));
            $labels[count($labels)] = "Hoje";
            break;
    }

    $timestamp = strtotime($date);
    include("../config/db_connect.php");
    $stmt = $mysqli->prepare('SELECT * FROM vendas WHERE buyDate >= ? AND status >= 3 AND status <= 4');
    //echo $date;
    $stmt->bind_param('s', $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        switch ($interval) {
            case "trimester":

                $data[intval(date('m', strtotime($row['buyDate'])))][intval(date('d', strtotime($row['buyDate'])))] += floatval($row['totalAmount']);
                break;
            case "year":
                $data[intval(date('m', strtotime($row['buyDate'])))] += floatval($row['totalAmount']);
                break;
            default:
                $data[intval(date("d", strtotime($row['buyDate'])))] += floatval($row['totalAmount']);
                break;
        }
    }


    if ($interval == "trimester") {
        //print_r($data);
        $dateR = array();
        foreach (array_reverse($data) as $key => $month) {
            $addBack = -1;
            foreach (array_chunk(($month), 5, true) as $key => $reduced) {
                if(count($reduced) < 5) {
                    $addBack = array_sum($reduced);
                }else{   
                    $dataR[] = array_sum($reduced);
                }
                //print_r($reduced);
            }
            if($addBack >= 0){
                $dataR[count($dataR) - 1] += $addBack;
                //echo $dataR[count($dataR) - 1] . "ax \n";
            }
        }
        //print_r(($dataR));
        //print_r($labels);

        $data =($dataR);
        /*
        $data_ = array_merge(...$data);
        //$data = array_merge(...$dataR);*/
    } else {
    }
    die(json_encode(array('status' => 200, 'data' => $data, 'labels' => $labels)));
}
