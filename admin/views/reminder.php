<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    $query = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = 60");
    while ($order = $query->fetch_assoc()) {
        $order = query_expand_json($order);
        $mail->clearAddresses();
        $mail->addAddress($order["generalfields"]["email"], patternmatch(CONFIG_RAW["general"]["email_name"], $order));
        $mail->Subject = TRANSLATION["order_reminder"];
        $mail->Body = patternmatch(file_get_contents(RESDIRABS . "/data/emails/reminder.php"), $order);
        if(!$mail->send()) {
           die("error");
        }
    }

    $_SESSION["alert"] = ["success", "mails_send"];

    header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
    exit;

?>
