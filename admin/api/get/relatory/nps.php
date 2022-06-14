<?php

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');

if (session_status() === PHP_SESSION_NONE) {
    session_name(md5("JM".$_SERVER['REMOTE_ADDR']));

    session_start();
}


if (!isset($_SESSION['user']) || !isset($_SESSION['admin']) || ($_SESSION['admin']) < 1) {
    die(json_encode(array('status' => 403)));
}


include_once '../../config/db_connect.php';

class nps extends dbConnect
{
    public function __construct()
    {
    }
    public function getFinances()
    {
        $mysqli = $this->connect();

        $returnData = array();

        //! Canceladas
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM vendas WHERE status = 6");
        $stmt->execute();
        $stmt->bind_result($returnData['totalCanceladas']);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT * FROM vendas WHERE status = 6 AND MONTH(lastUpdate) = MONTH(CURRENT_DATE())");
        $stmt->execute();
        $listCanceladas = $stmt->get_result();
        $stmt->close();

        $returnData['listCanceladas'] = $listCanceladas->fetch_all(MYSQLI_ASSOC);


        $returnData['CanceladasMes'] = count($returnData['listCanceladas']);
        //!


        //> Newsletter
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM newsletter");
        $stmt->execute();
        $stmt->bind_result($returnData['totalNewsletter']);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM newsletter WHERE MONTH(date) = MONTH(CURRENT_DATE())");
        $stmt->execute();
        $stmt->bind_result($returnData['newsletterMes']);
        $stmt->fetch();
        $stmt->close();
        //>


        //| NPS
        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating WHERE rate BETWEEN 0 AND 3");
        $stmt->execute();
        $stmt->bind_result($rateDetractor);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating WHERE rate = 5");
        $stmt->execute();
        $stmt->bind_result($ratePromoter);
        $stmt->fetch();
        $stmt->close();

        $stmt = $mysqli->prepare("SELECT COUNT(*) FROM rating ");
        $stmt->execute();
        $stmt->bind_result($totalrates);
        $stmt->fetch();
        $stmt->close();

        $returnData['nps'] = intval((($ratePromoter / $totalrates) - ($rateDetractor / $totalrates)) * 100);
        //|


        //- Date Chart

        $stmt = $mysqli->prepare("SELECT * FROM client");
        $stmt->execute();
        $listClient = $stmt->get_result();
        $stmt->close();

        $returnData['totalClient'] = $listClient->num_rows;

        $male = array();
        $female  = array();

        for ($i = 15; $i <= 70; $i++) {
            $male[$i] = 0;
            $female[$i] = 0;
        }

        while ($client = $listClient->fetch_assoc()) {
            $date = new DateTime($client['bornDate']);
            $now = new DateTime();
            $age = $now->diff($date)->y;
            if ($client['gender'] == "F") {
                if ($age > 70) {
                    $female[70]++;
                } else if ($age < 15) {
                    $female[15]++;
                } else {
                    $female[$age]++;
                }
            } else if ($client['gender'] == "M") {
                if ($age > 70) {
                    $male[70]++;
                } else if ($age < 15) {
                    $male[15]++;
                } else {
                    $male[$age]++;
                }
            } else {
                if ($age > 70) {
                    $female[70] += 0.5;
                    $male[70] += 0.5;
                } else if ($age < 15) {
                    $female[15] += 0.5;
                    $male[15] += 0.5;
                } else {
                    $female[$age] += 0.5;
                    $male[$age] += 0.5;
                }
            }
        }

        for ($i = 15; $i <= 70; $i++) {
            $male[$i] = $male[$i] / $returnData['totalClient'] * -100;
            $female[$i] = $female[$i] / $returnData['totalClient'] * 100;
        }

        foreach (array_chunk($male, 5) as $chunked) {
            $newMale[] = floatval(number_format(array_sum($chunked), 2, '.', ''));
        }
        foreach (array_chunk($female, 5) as $chunked) {
            $newFemale[] = floatval(number_format(array_sum($chunked), 2, '.', ''));
        }
        $male = array_reverse($newMale);
        $female = array_reverse($newFemale);

        $returnData['genderChart'] = [
            'male' => $male,
            'female' => $female
        ];



        $stmt = $mysqli->prepare("SELECT * FROM ds_relatory ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $row = $result->fetch_assoc();
        $row['indicators'] = json_decode($row['indicators']);

        $returnData['monthNps'] = $returnData['nps'] -  (float) $row['indicators']->nps;
        $returnData['monthClient'] = $returnData['totalClient'] -  (float) $row['indicators']->client;

        $stmt = $mysqli->prepare("SELECT * FROM ds_relatory");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        $visitorChart = array();
        $maillingChart = array();
        
        while ($row = $result->fetch_assoc()) {
            $row['indicators'] = json_decode($row['indicators']);
            $returnData['relatoryList'][] = $row;

            $visitorChart[] = [
                'date' => $row['date'],
                'value' => $row['indicators']->visitor
            ];
            $maillingChart[] = [
                'date' => $row['date'],
                'value' => $row['indicators']->newsletter
            ];

        }
        $returnData['visitorChart'] = [
            'visitor' => $visitorChart,
            'mailling' => $maillingChart
        ];
        die(json_encode($returnData));
    }
}

$nps = new nps();

$nps->getFinances();
