<?php
header('Content-Type: application/json; charset=utf-8');


setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));
    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])  || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}



include_once '../config/db_connect.php';

class Chart extends dbConnect
{
    private $startDate_, $interval_, $labels_, $data_;
    public function __construct()
    {
    }

    public function homeChart($interval)
    {
        $this->interval_ = $interval;



        switch ($interval) {
            case 'month':
                $this->Month();
                break;

            case 'year':
                $this->Year();
                break;

            case 'trimester':
                $this->Trimester();

                break;
            default:
                return array(
                    "status" => 400
                );
        }




        return (array(
            "status" => 200,
            "labels" => $this->labels_,
            "data" => $this->data_
        ));
    }


    //* Faturamento
    //- Mês
    private function Month()
    {
        $this->startDate_ =  date("Y-m-d", strtotime("-1 month", strtotime(date('Y-m-d'))));
        $this->homeChartData();
        $monthData = [];
        $monthLabels = [];
        $data = $this->data_;
        $labels = $this->labels_;

        for ($dt = $this->startDate_; $dt <= date("Y-m-d"); $dt = date("Y-m-d", strtotime("+1 day", strtotime($dt)))) {
            $monthData[(int)date("Y", strtotime(($dt)))][(int)date("m", strtotime(($dt)))][(int)date("d", strtotime(($dt)))] = 0;
            $monthLabels[(int)date("Y", strtotime(($dt)))][(int)date("m", strtotime(($dt)))][(int)date("d", strtotime(($dt)))] = date("Y-m-d", strtotime($dt));
        }


        //> Labels
        foreach ($data as $y => $year) {
            foreach ($year as $m => $month) {
                foreach ($month as $d => $day) {
                    $monthData[$y][$m][$d] += $day;
                }
            }
            $monthData = (array_merge(...$monthData[$y]));
            $monthLabels = (array_merge(...$monthLabels[$y]));
        }


        $this->labels_ = $monthLabels;
        $this->data_ = $monthData;
    }


    //* Faturamento
    //> Ano
    private function Trimester()
    {
        $this->startDate_ = date("Y-m-d", strtotime("-3 month", strtotime(date('Y-m-d'))));
        
        $this->homeChartData();
        $monthData = [];
        $monthLabels = [];
        $data = $this->data_;
        $labels = $this->labels_;

        for ($dt = $this->startDate_; $dt <= date("Y-m-d"); $dt = date("Y-m-d", strtotime("+1 day", strtotime($dt)))) {
            $monthData[(int)date("Y", strtotime(($dt)))][(int)date("m", strtotime(($dt)))][(int)date("d", strtotime(($dt)))] = 0;
            $monthLabels[(int)date("Y", strtotime(($dt)))][(int)date("m", strtotime(($dt)))][(int)date("d", strtotime(($dt)))] = date("Y-m-d", strtotime($dt));
        }


        //> Labels
        foreach ($data as $y => $year) {
            foreach ($year as $m => $month) {
                foreach ($month as $d => $day) {
                    $monthData[$y][$m][$d] += $day;
                }
            }
            $monthData = (array_merge(...$monthData[$y]));
            $monthLabels = (array_merge(...$monthLabels[$y]));
        }



        $newData = [];
        foreach (array_chunk($monthData, 3) as $chunked) {
            $newData[] = array_sum($chunked);
        }

        foreach (array_chunk($monthLabels, 3) as $chunked) {
            isset($chunked[2]) ? $newLabel[] = $chunked[2] : $newLabel[] = $chunked[0];
        }



        $this->labels_ = $newLabel;
        $this->data_ = $newData;
    }

    //* Faturamento
    //> Ano
    private function Year()
    {
        if (intval(date('m')) < 3) {

            $this->startDate_ = date("Y-m-01", strtotime("-3 month", strtotime(date('Y-m-01'))));
        } else {
            $this->startDate_ = date("Y-01-01");
        }

        $this->homeChartData();
        $monthData = [];
        $labels = [];
        $data = $this->data_;
        foreach ($data as $y => $year) {
            foreach ($year as $m => $month) {
                $monthData[$y][$m] = array_sum($data[$y][$m]);
                $labels[$y][$m] = date(($y < 10 ? "0" . $y : $y) . "-" . ($m < 10 ? "0" . $m : $m) . "-01 00:00:00");
            }
        }

        $FData = [];
        $FLabels = [];
        foreach ($monthData as $y => $year) {
            foreach ($year as $m => $month) {
                $FData[] = $monthData[$y][$m];
                $FLabels[] = $labels[$y][$m];
            }
        }
        $this->labels_ = $FLabels;
        $this->data_ = $FData;
    }


    //* Faturamento
    //? Dados
    private function homeChartData()
    {
        $mysqli = $this->connect();
        $stmt = $mysqli->prepare('SELECT * FROM vendas WHERE paymentDate >= ? AND status >= 3 AND status <= 4');
        //echo $date;
        $stmt->bind_param('s', $this->startDate_);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = array();
        $label = array();
        while ($row = $result->fetch_assoc()) {

            $day = intval(date('d', strtotime($row['buyDate'])));
            $month = intval(date('m', strtotime($row['buyDate'])));
            $year = intval(date('Y', strtotime($row['buyDate'])));
            isset($data[$year][$month][$day]) ? $data[$year][$month][$day] += floatval($row['totalAmount']) : $data[$year][$month][$day] =  floatval($row['totalAmount']);
            isset($label[$year][$month][$day]) ? $label[$year][$month][$day] += floatval($row['totalAmount']) : $label[$year][$month][$day] = date("Y-m-d 00:00:00", strtotime($row['buyDate']));
        }
        $stmt->close();
        $mysqli->close();
        if (sizeof($data) > 0) {
            foreach ($data as $y => $year) {
                ksort($data[$y]);
                ksort($label[$y]);
            }
        } else {
        }
        ksort($data);
        ksort($label);

        $this->labels_ = $label;
        $this->data_ = $data;
    }
}



if (isset($_GET["interval"])) {
    $interval = $_GET["interval"];
    $chart = new Chart();

    die(json_encode($chart->homeChart($interval)));
}
