<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Methods: GET');


include_once '../config/db_connect.php';

class getGlider extends dbConnect
{
    public function __construct()
    {
        $mysqli = $this->Conectar();
        $stmt = $mysqli->prepare("SELECT * FROM carousel");
        $stmt->execute();
        $result_ = $stmt->get_result();
        $data = array();
        if ($result_->num_rows > 0) {
            $data = array();

            while ($row = $result_->fetch_assoc()) {
                $catId = $row['category'];
                $stmt2 = $mysqli->prepare("SELECT * FROM categories WHERE id = ?");
                $stmt2->bind_param("i", $catId);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $stmt2->close();
                $carousel["id"] = $row['id'];
                $carousel["category"] = $catId;
                $carousel["name"] = $result2->fetch_assoc()['name'];

                if ($row["SelectType"] == "id") {
                    $ids = [];


                    foreach (json_decode($row["select"]) as $key => $id) {
                        $stmt3 = $mysqli->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt3->bind_param("i", $id);
                        $stmt3->execute();
                        $result3 = $stmt3->get_result();
                        $stmt3->close();
                        $product = $result3->fetch_assoc();
                        unset($product['cost']);
                        $product["imgs"] = json_decode($product["imgs"]);
                        $ids[] = $product;
                    }

                    $carousel["prod_ids"] = ($ids);
                    $carousel["SelectType"] = $row["SelectType"];
                    $carousel["select"] = $row["select"];

                    $data[] = $carousel;
                } else if ($row["SelectType"] == "auto") {


                    $selectType = $row["select"];
                    $selectType = (json_decode($selectType, true));
                    $selectType =  $selectType["type"];

                    switch ($selectType) {
                        case "price":
                            $stmt3 = $mysqli->prepare("SELECT * FROM products WHERE category = ? AND totalQuantity > 0 ORDER BY price ASC LIMIT 7");
                            $stmt3->bind_param("s", $catId);
                            $stmt3->execute();
                            $result3 = $stmt3->get_result();
                            $stmt3->close();
                            if ($result3->num_rows > 0) {
                                $i = 0;
                                $ids = [];
                                while ($row3 = $result3->fetch_assoc()) {
                                    unset($row3['cost']);
                                    $row3["imgs"] = json_decode($row3["imgs"]);

                                    $ids["" . $i . ""] = $row3;
                                    $i++;
                                }

                                $carousel["prod_ids"] = ($ids);
                                $carousel["SelectType"] = "auto";
                                $carousel["select"] = $selectType;
                                $data[] = $carousel;
                            }
                            break;
                        case "promo":
                            $stmt3 = $mysqli->prepare("SELECT * FROM products WHERE category = ? AND promo > 0 AND totalQuantity > 0 ORDER BY price ASC LIMIT 7");
                            $stmt3->bind_param("s", $catId);
                            $stmt3->execute();
                            $result3 = $stmt3->get_result();
                            $stmt3->close();

                            if ($result3->num_rows > 0) {
                                $i = 0;
                                $ids = [];

                                while ($row3 = $result3->fetch_assoc()) {
                                    unset($row3['cost']);
                                    $row3["imgs"] = json_decode($row3["imgs"]);
                                    $ids["" . $i . ""] = $row3;
                                    $i++;
                                }

                                $carousel["prod_ids"] = ($ids);
                                $carousel["SelectType"] = "auto";
                                $carousel["select"] = $selectType;
                                $data[] = $carousel;
                            }
                            break;
                    }
                }
            }

            die(json_encode($data));
        } else {
            die(json_encode(array("status" => 400, "error" => "No carousel found")));
        }
    }
}

$new = new getGlider();