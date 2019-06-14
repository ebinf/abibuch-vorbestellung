<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    #$query = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = 2 OR id = 60");
    $query = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE delivery = 'akademische_feier' AND status = 'ordered,paid'");
    while ($ordersg = $query->fetch_assoc()) {
        $order = query_expand_json($ordersg);
        $mail->clearAddresses();
        $mail->addAddress($order["general_fields"]["email"], patternmatch(CONFIG_RAW["general"]["email_name"], $order));
        $mail->Subject = TRANSLATION["order_reminder"];
        $mail->Body = patternmatch(file_get_contents(RESDIRABS . "/data/emails/reminder.php"), $order);
        if(!$mail->send()) {
           die("error");
        }
        echo $order["general_fields"]["firstname"];
    }
    die();
    $_SESSION["alert"] = ["success", "emails_send"];
    header("Location: " . ADMIN);
    exit;

?>
