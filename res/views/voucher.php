<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (!isset($_POST["code"])) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    $query = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE code='" . $con->real_escape_string(strtoupper($_POST["code"])) . "' AND order_id='0'");

    if ($query->num_rows == 1) {
        $query = $query->fetch_assoc();
        die("valid: " . $query["value"]);
    } else {
        die("invalid");
    }

?>
