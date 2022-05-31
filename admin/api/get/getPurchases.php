<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');


include_once '../config/db_connect.php';

class Purchases extends dbConnect
{
    private $text_, $status_, $filter_;

    public function __construct($filter, $text, $status,  $order_)
    {
        $this->text_ = $text;
        $this->status_ = $status;
        $this->filter_ = $filter;
        $this->order_ = $order_;
    }

    public function getPurchases()
    {

        $text = $this->text_;
        $status = $this->status_;
        $filter = $this->filter_;
        $order = $this->order_;


        $params = [];
        $labels = "";
        $min = 0;
        $orderBy = "";
        $sql = "SELECT * FROM vendas";

        if ($text != "") {
            $sql = "SELECT a.* FROM vendas as a INNER JOIN client as b ON b.id = a.clientId AND (b.name LIKE ? OR b.lastName LIKE ? OR a.code LIKE ?) ";
            array_push($params, "%" . $text . "%");
            array_push($params, "%" . $text . "%");
            array_push($params, "%" . $text . "%");
            $labels .= "sss";
        } else {
        }

        if (isset($status) && $status != "" && $status > 0 && $status <= 9) {
            switch ($status) {
                case '1':
                    if ($labels != "") {
                        $sql .= " AND (a.status = 1 OR a.status = 2) ";
                    } else {
                        $sql .= " WHERE (status = 1 OR status = 2) ";
                    }
                    break;
                case '2':
                    if ($labels != "") {
                        $sql .= " AND (a.status = 1 OR a.status = 2)  ";
                    } else {
                        $sql .= " WHERE status = 1 OR status = 2  ";
                    }
                    break;
                case '3':
                    if ($labels != "") {
                        $sql .= " AND (a.status = 3 OR a.status = 4) ";
                    } else {
                        $sql .= " WHERE status = 3 OR status = 4";
                    }
                    break;
                case '4':
                    if ($labels != "") {
                        $sql .= " AND (a.status = 3 OR a.status = 4) ";
                    } else {
                        $sql .= " WHERE status = 3 OR status = 4";
                    }
                    break;
                case '5':
                    if ($labels != "") {
                        $sql .= " AND a.status = 5 ";
                    } else {
                        $sql .= " WHERE status = 5";
                    }
                    break;
                case '7':
                    if ($labels != "") {
                        $sql .= " AND a.status = 7 ";
                    } else {
                        $sql .= " WHERE status = 7 ";
                    }
                    break;

                default:
                    if ($labels != "") {
                        $sql .= " AND (a.status = 5 OR a.status = 6 OR a.status = 8 OR a.status = 9)";
                    } else {
                        $sql .= " WHERE (status = 5 OR status = 6 OR status = 8 OR status = 9) ";
                    }

                    break;
            }
        }
        if ($filter) {
            $filter = $filter;

            if ($order) {
                $orderBy = $order == "true" ? "DESC" : "ASC";
            } else {
                $orderBy = "ASC";
            }


            switch ($filter) {
                case "name":
                    if ($labels != "") {
                        $sql .= " ORDER BY SUBSTR(b.name, 1, 1 ) " . $orderBy;
                    } else {
                        $sql .= " JOIN client ON vendas.clientId = client.id ORDER BY SUBSTR(client.name, 1, 1 ) " . $orderBy;
                    }
                    break;
                case "totalAmount":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.category " . $orderBy;
                    } else {
                        $sql .= " ORDER BY category " . $orderBy;
                    }
                    break;
                case "buyDate":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.buyDate " . $orderBy;
                    } else {
                        $sql .= " ORDER BY buyDate " . $orderBy;
                    }
                    break;
                case "id":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.id " . $orderBy;
                    } else {
                        $sql .= " ORDER BY id " . $orderBy;
                    }
                    break;
                case "price":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.totalAmount " . $orderBy;
                    } else {
                        $sql .= " ORDER BY totalAmount " . $orderBy;
                    }
                    break;
                case "status":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.status " . $orderBy;
                    } else {
                        $sql .= " ORDER BY status " . $orderBy;
                    }
                    break;
                case "sended":
                    if ($labels != "") {
                        $sql .= " ORDER BY a.trackingCode " . $orderBy  . " , FIELD(a.status, 3, 4, 2, 1,5,6,7,8,9)";
                    } else {
                        $sql .= " ORDER BY trackingCode " . $orderBy . " , FIELD(status, 3, 4, 2, 1,5,6,7,8,9)";
                    }
                    break;
            }
        }

        $mysqli =  $this->connect();

        //echo $sql;
        $stmt = $mysqli->prepare($sql);
        if ($labels != "") {
            $stmt->bind_param($labels, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if ($result->num_rows) {
            $products = array();
            while ($row = $result->fetch_assoc()) {
                $stmt = $mysqli->prepare("SELECT * FROM client WHERE id = ?");
                $stmt->bind_param("s", $row["clientId"]);
                $stmt->execute();
                $result2 = $stmt->get_result();
                $stmt->close();
                $client = $result2->fetch_assoc();
                $row["client"] = $client;
                $products[] = $row;
            }
            return (json_encode($products,  JSON_UNESCAPED_UNICODE));
        } else {
            return (json_encode(array()));
        }
    }
}



if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    die(json_encode(array('status' => 403)));
}else{

    
    isset($_GET['filter']) ? $filter = $_GET['filter'] : $filter = "";
    isset($_GET['text']) ? $text = $_GET['text'] : $text = "";
    isset($_GET['getStatus']) ? $status = $_GET['getStatus'] : $status = "";
isset($_GET['order']) ? $order = $_GET['order'] : $order = "";
$vendas = new Purchases($filter, $text, $status, $order);
die(($vendas->getPurchases()));
}
