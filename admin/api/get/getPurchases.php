<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user']) || !isset($_SESSION['admin'])) {
    //die(json_encode(array('status' => 403)));
} else {
    $params = [];
    $labels = "";
    $min = 0;
    $orderBy = "";
    $sql = "SELECT * FROM vendas";

    if (isset($_GET["text"]) && $_GET["text"] != "") {
        $sql = "SELECT a.* FROM vendas as A INNER JOIN client as B ON b.id = a.clientId AND (b.name LIKE ? OR b.lastName LIKE ? OR a.code LIKE ?) ";
        array_push($params, "%" . $_GET["text"] . "%");
        array_push($params, "%" . $_GET["text"] . "%");
        array_push($params, "%" . $_GET["text"] . "%");
        $labels .= "sss";
    } else {
    }

    if (isset($_GET["getStatus"]) && $_GET["getStatus"] != "" && $_GET["getStatus"] > 0 && $_GET["getStatus"] <= 9) {
        $status = $_GET["getStatus"];

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
                    $sql .= " AND a.status = 3 ";
                } else {
                    $sql .= " WHERE status = 3 ";
                }
                break;
            case '4':
                if ($labels != "") {
                    $sql .= " AND a.status = 4 ";
                } else {
                    $sql .= " WHERE status = 4 ";
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
    if ($_GET["filter"]) {
        $filter = $_GET["filter"];

        if ($_GET["order"]) {
            $orderBy = $_GET["order"] == "true" ? "DESC" : "ASC";
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


    include '../config/db_connect.php';
    $stmt = $mysqli->prepare($sql);
    //echo $sql;
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
        die(json_encode($products,  JSON_UNESCAPED_UNICODE));
    } else {
        die(json_encode(array()));
    }
}
