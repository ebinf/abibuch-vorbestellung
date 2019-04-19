<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (!isset($_POST["step1"]) ||
    !isset($_POST["quantity"]) ||
    !isset($_POST["price"]) ||
    !isset($_POST["payment"]) ||
    !isset($_POST["payment_form"]) ||
    !isset($_POST["delivery"]) ||
    !isset($_POST["delivery_form"])) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if ($_POST["quantity"] < CONFIG["product"]["min"] || $_POST["quantity"] > CONFIG["product"]["max"]) {
        die("error");
    }

    $step1 = json_decode(base64_decode($_POST["step1"]), true);

    $generalfields = [];
    foreach (CONFIG["fields"]["general"] as $field) {
        if (validate_field($step1, $field)) {
            $generalfields[$field["name"]] = $step1[$field["name"]];
        } else {
            die("error");
        }
    }

    $typefields = [];
    foreach (CONFIG["fields"]["types"][$step1["type"]] as $field) {
        if (validate_field($step1, $field)) {
            $typefields[$field["name"]] = $step1[$field["name"]];
        } else {
            die("error");
        }
    }

    if ($_POST["payment"] != "voucher") {
        if (!find_in_json(CONFIG["payment"], "name", $_POST["payment"])) {
            die("error");
        }
        if (array_key_exists("types", find_in_json(CONFIG["payment"], "name", $_POST["payment"])) && !in_array($step1["type"], find_in_json(CONFIG["payment"], "name", $_POST["payment"])["types"])) {
            die("error");
        }

        if (array_key_exists("fieldset", find_in_json(CONFIG["payment"], "name", $_POST["payment"])) && !empty(find_in_json(CONFIG["payment"], "name", $_POST["payment"])["fieldset"])) {
            foreach (CONFIG["fields"]["payment"][find_in_json(CONFIG["payment"], "name", $_POST["payment"])["fieldset"]] as $field) {
                if (validate_field($_POST["payment_form"], $field) == false) {
                    die("invalid");
                }
            }
        }
    }

    if (!find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])) {
        die("error");
    }
    if (array_key_exists("types", find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])) && !in_array($step1["type"], find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["types"])) {
        die("error");
    }

    if (array_key_exists("fieldset", find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])) && !empty(find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["fieldset"])) {
        foreach (CONFIG["fields"]["delivery"][find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["fieldset"]] as $field) {
            if (validate_field($_POST["delivery_form"], $field) == false) {
                die("invalid");
            }
        }
    }

    if (isset($_POST["voucher"]) && !empty(trim($_POST["voucher"]))) {
        $voucher = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE code='" . $con->real_escape_string(strtoupper($_POST["voucher"])) . "' AND order_id='0'");
        if ($voucher->num_rows == 1) {
            $voucher = $voucher->fetch_assoc();
            $voucher = -1 * $voucher["value"];
        } else {
            die("error");
        }
    } else {
        $voucher = 0;
    }

    if ($_POST["payment"] == "voucher" && (-1 * $voucher) < (($_POST["quantity"] * CONFIG["product"]["price"]) + find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["price"])) {
        die("error");
    }

    $payment = $_POST["payment"];
    $payment_form = $_POST["payment_form"];
    if ($payment != "voucher" && $voucher != 0 && (-1 * $voucher) >= (($_POST["quantity"] * CONFIG["product"]["price"]) + find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["price"])) {
        $payment = "voucher";
        $payment_form = "";
    }

    $price = ($_POST["quantity"] * CONFIG["product"]["price"]) + find_in_json(CONFIG["payment"], "name", $_POST["payment"])["price"] + find_in_json(CONFIG["delivery"], "name", $_POST["delivery"])["price"] + $voucher;
    if ($price <= 0) {
        $price = 0;
    }

    if ($_POST["price"] != $price) {
        die("error");
    }

    $ordernr = date("Gsi") . random_int(10, 99);

    while ($con->query("SELECT * FROM " . DBPREFIX . "orders WHERE ordernr = '" . $ordernr . "'")->num_rows > 0) {
        $ordernr = date("Gsi") . random_int(10, 99);
    }

    $query = $con->query("INSERT INTO " . DBPREFIX . "orders (id, timestamp, ordernr, secret, general_fields, type, type_fields, delivery, delivery_fields, payment, payment_fields, amount, total_price, status) VALUES (
        NULL,
        CURRENT_TIMESTAMP,
        '" . $ordernr . "',
        '" . bin2hex(random_bytes(8)) . "',
        '" . $con->real_escape_string(json_encode($generalfields)) . "',
        '" . $con->real_escape_string($step1["type"]) . "',
        '" . $con->real_escape_string(json_encode($typefields)) . "',
        '" . $con->real_escape_string($_POST["delivery"]) . "',
        '" . $con->real_escape_string(json_encode($_POST["delivery_form"])) . "',
        '" . $con->real_escape_string($payment) . "',
        '" . $con->real_escape_string(json_encode($payment_form)) . "',
        '" . $con->real_escape_string($_POST["quantity"]) . "',
        '" . $con->real_escape_string($price) . "',
        'ordered'
    )");

    if ($query == false) {
        die("error");
    }

    $query = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE ordernr = '" . $ordernr . "'");
    $query = $query->fetch_assoc();
    $query = query_expand_json($query);

    if ($price == 0) {
        $con->query("UPDATE " . DBPREFIX . "orders SET paid_timestamp = CURRENT_TIMESTAMP, status = 'ordered,paid' WHERE id = " . $query["id"]);
    }

    if (isset($_POST["voucher"]) && !empty(trim($_POST["voucher"]))) {
        $con->query("UPDATE " . DBPREFIX . "vouchers SET order_id='" . $query["id"] . "' WHERE code = '" . $con->real_escape_string(strtoupper($_POST["voucher"])) . "'");
    }

    $mail->addAddress($generalfields["email"], patternmatch(CONFIG_RAW["general"]["email_name"], $query));
    $mail->Subject = TRANSLATION["order_confirmation"];
    $mail->Body = patternmatch(file_get_contents(RESDIRABS . "/data/emails/overview.php"), $query);
    if(!$mail->send()) {
       die("error");
    }

    echo "success";

?>
