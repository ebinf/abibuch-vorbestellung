<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");

    if (sizeof($expl) <= 2) {
        echo json_encode(["error" => "bad request"]);
        exit;
    }

    if ($expl[2] == "get-secret") {
        if (!isset($_POST["login_token"])) {
            echo json_encode(["error" => "token missing"]);
        } else {
            $valid = $con->query("SELECT id, secret FROM " . DBPREFIX . "api WHERE token = '" . $con->real_escape_string($_POST["login_token"]) . "'");
            if ($valid->num_rows == 1) {
                $valid = $valid->fetch_assoc();
                if (empty(trim($valid["secret"]))) {
                    $secret = bin2hex(cst_random_bytes(64));
                    $secret_hash = password_hash($secret, PASSWORD_DEFAULT);
                    $con->query("UPDATE " . DBPREFIX . "api SET secret = '" . $con->real_escape_string($secret_hash) . "' WHERE id = " . $valid["id"]);
                    echo json_encode(["secret" => $secret]);
                } else {
                    echo json_encode(["error" => "token already used"]);
                }
            } else {
                echo json_encode(["error" => "wrong token"]);
            }
        }
        exit;
    }

    if ($expl[2] == "authenticate") {
        if (!isset($_POST["login_token"]) || !isset($_POST["login_secret"])) {
            echo json_encode(["error" => "token or secret missing"]);
        } else {
            $valid = $con->query("SELECT id, secret FROM " . DBPREFIX . "api WHERE token = '" . $con->real_escape_string($_POST["login_token"]) . "'");
            if ($valid->num_rows != 1) {
                echo json_encode(["error" => "token or secret wrong"]);
                exit;
            }
            $valid = $valid->fetch_assoc();
            if (password_verify($_POST["login_secret"], $valid["secret"])) {
                if (password_needs_rehash($valid["secret"], PASSWORD_DEFAULT)) {
                    $newHash = password_hash($_POST["login_secret"], PASSWORD_DEFAULT);
                    $con->query("UPDATE " . DBPREFIX . "api SET secret = '" . $con->real_escape_string($newHash) . "' WHERE id = " . $valid["id"]);
                }
                $_SESSION["api_login_id"] = $valid["id"];
                echo json_encode(["message" => "login successful"]);
            } else {
                echo json_encode(["error" => "token or secret wrong"]);
            }
        }
        exit;
    }

    if (!isset($_SESSION["api_login_id"]) && !(isset($_POST["token"]) && isset($_POST["secret"]))) {
        echo json_encode(["error" => "not authenticated"]);
        exit;
    }

    if(isset($_SESSION["api_login_id"])) {
        $apiuser = $con->query("SELECT * FROM " . DBPREFIX . "api WHERE id = " . $con->real_escape_string($_SESSION["api_login_id"]));
        if ($apiuser->num_rows != 1) {
            echo json_encode(["error" => "token or secret wrong"]);
            exit;
        }
        $apiuser = $apiuser->fetch_assoc();
    } else {
        $apiuser = $con->query("SELECT * FROM " . DBPREFIX . "api WHERE token = '" . $con->real_escape_string($_POST["token"]) . "'");
        if ($apiuser->num_rows != 1) {
            echo json_encode(["error" => "token or secret wrong"]);
            exit;
        }
        $apiuser = $apiuser->fetch_assoc();
        if (password_verify($_POST["secret"], $apiuser["secret"])) {
            if (password_needs_rehash($apiuser["secret"], PASSWORD_DEFAULT)) {
                $newHash = password_hash($_POST["secret"], PASSWORD_DEFAULT);
                $con->query("UPDATE " . DBPREFIX . "api SET secret = '" . $con->real_escape_string($newHash) . "' WHERE id = " . $apiuser["id"]);
            }
            $_SESSION["api_login_id"] = $apiuser["id"];
        } else {
            echo json_encode(["error" => "token or secret wrong"]);
            exit;
        }
    }

    if ($expl[2] == "authentication-test") {
        echo json_encode(["message" => "authentication successful"]);
        exit;
    }

    if ($expl[2] == "orders") {
            $orders = $con->query("SELECT * FROM " . DBPREFIX . "orders");
            if ($orders->num_rows == 0) {
                echo json_encode([]);
            } else {
                $output = [];
                while ($row = $orders->fetch_assoc()) {
                    foreach ($row as $key => $field) {
                        if (strpos($key, "_fields") != false) {
                            $row[$key] = json_decode($field, true);
                        }
                    }
                    $output[] = $row;
                }
                echo json_encode($output, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
            }
        exit;
    }

    if ($expl[2] == "order") {
        if (sizeof($expl) == 4 || (sizeof($expl) == 5 && $expl[3] == "ordernr")) {
            if ($expl[3] == "ordernr") {
                $order = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE ordernr = " . $con->real_escape_string($expl[4]));
            } else {
                $order = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($expl[3]));
            }
            if ($order->num_rows == 0) {
                echo json_encode(["error" => "order not found"]);
            } else {
                $order = $order->fetch_assoc();
                $output = [];
                foreach ($order as $key => $field) {
                    if (strpos($key, "_fields") != false) {
                        $output[$key] = json_decode($field, true);
                    } else {
                        $output[$key] = $field;
                    }
                }
                echo json_encode($output, JSON_NUMERIC_CHECK | JSON_UNESCAPED_UNICODE);
            }
        } elseif (sizeof($expl) == 5) {
            $order = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($expl[3]));
            if ($order->num_rows == 0) {
                echo json_encode(["error" => "order not found"]);
            }
            $order = $order->fetch_assoc();
            $newstatus = explode(",", $order["status"]);
            $timestamps = ["paid" => (!is_null($order["paid_timestamp"]) ? "'" . $order["paid_timestamp"] . "'" : "NULL"), "delivered" => (!is_null($order["delivered_timestamp"]) ? "'" . $order["delivered_timestamp"] . "'" : "NULL"), "cancelled" => (!is_null($order["cancelled_timestamp"]) ? "'" . $order["cancelled_timestamp"] . "'" : "NULL")];

            if ($expl[4] == "mark-paid") {
                $newstatus[] = "paid";
                $timestamps["paid"] = "CURRENT_TIMESTAMP";
                echo json_encode(["message" => "sucessfully marked paid"]);
            } elseif ($expl[4] == "mark-unpaid") {
                $timestamps["paid"] = "NULL";
                if (in_array("paid", $newstatus)) {
                    unset($newstatus[array_search("paid", $newstatus)]);
                }
                echo json_encode(["message" => "sucessfully marked unpaid"]);
            } elseif ($expl[4] == "mark-delivered") {
                if (in_array("paid", $newstatus)) {
                    $newstatus[] = "delivered";
                    $timestamps["delivered"] = "CURRENT_TIMESTAMP";
                    echo json_encode(["message" => "sucessfully marked delivered"]);
                } else {
                    echo json_encode(["error" => "order has to be paid first"]);
                }
            } elseif ($expl[4] == "mark-undelivered") {
                $timestamps["delivered"] = "NULL";
                if (in_array("delivered", $newstatus)) {
                    unset($newstatus[array_search("delivered", $newstatus)]);
                }
                echo json_encode(["message" => "sucessfully marked undelivered"]);
            } elseif ($expl[4] == "cancel") {
                $newstatus[] = "cancelled";
                $timestamps["cancelled"] = "CURRENT_TIMESTAMP";
                echo json_encode(["message" => "sucessfully cancelled"]);
            } elseif ($expl[4] == "undo-cancellation") {
                $timestamps["cancelled"] = "NULL";
                if (in_array("cancelled", $newstatus)) {
                    unset($newstatus[array_search("cancelled", $newstatus)]);
                }
                echo json_encode(["message" => "sucessfully undone cancellation"]);
            }
            $newstatus = array_unique($newstatus);
            $newstatus = implode(",", $newstatus);
            $con->query("UPDATE " . DBPREFIX . "orders SET status='" . $newstatus . "', paid_timestamp = " . $timestamps["paid"] . ", delivered_timestamp = " . $timestamps["delivered"] . ", cancelled_timestamp = " . $timestamps["cancelled"] . " WHERE id = " . $order["id"]);
            exit;
        } else {
            echo json_encode(["error" => "bad request"]);
        }
        exit;
    }

?>
