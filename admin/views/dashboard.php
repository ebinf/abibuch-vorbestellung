<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) > 2) {
        header("Location: " . ADMIN . "/" . $menu);
        exit;
    }

    $orders = ["all" => 0, "paid" => 0, "delivered" => 0];
    $products = ["all" => 0, "paid" => 0, "delivered" => 0];
    $prices = ["all" => 0, "paid" => 0, "delivered" => 0];
    $vouchers = ["all" => 0, "used" => 0, "unused" => 0];
    $vouchers_prices = ["all" => 0, "used" => 0, "unused" => 0];
    $query = $con->query("SELECT * FROM " . DBPREFIX . "orders");
    while ($row = $query->fetch_assoc()) {
        $row["status"] = explode(",", $row["status"]);
        if (!in_array("cancelled", $row["status"])) {
            $orders["all"] += 1;
            $products["all"] += $row["amount"];
            $prices["all"] += $row["total_price"];
            if (in_array("paid", $row["status"])) {
                $orders["paid"] += 1;
                $products["paid"] += $row["amount"];
                $prices["paid"] += $row["total_price"];
            }
            if (in_array("delivered", $row["status"])) {
                $orders["delivered"] += 1;
                $products["delivered"] += $row["amount"];
                $prices["delivered"] += $row["total_price"];
            }
        }
    }
    $query = $con->query("SELECT * FROM " . DBPREFIX . "vouchers");
    while ($row = $query->fetch_assoc()) {
        $vouchers["all"] += 1;
        $vouchers_prices["all"] += $row["value"];
        if ($row["order_id"] != 0) {
            $vouchers["used"] += 1;
            $vouchers_prices["used"] += $row["value"];
        } else {
            $vouchers["unused"] += 1;
            $vouchers_prices["unused"] += $row["value"];
        }
    }

?>
<h1><?=TRANSLATION["dashboard"]?></h1>

<div class="col-12 m-0 d-flex flex-wrap justify-content-center">
    <div class="card text-white bg-primary mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$orders["all"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["orders"]?></h5>
                <p class="card-text"><?=$products["all"]?> <?=($products["all"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"])?><br /><?=money($prices["all"])?></p>
            </div>
        </div>
    </div>
    <div class="card text-white bg-success mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$orders["paid"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["paid"]?></h5>
                <p class="card-text"><?=$products["paid"]?> <?=($products["all"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"])?><br /><?=money($prices["paid"])?></p>
            </div>
        </div>
    </div>
    <div class="card text-white bg-info mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$orders["delivered"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["delivered"]?></h5>
                <p class="card-text"><?=$products["delivered"]?> <?=($products["all"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"])?><br /><?=money($prices["delivered"])?></p>
            </div>
        </div>
    </div>
    <div class="card bg-secondary mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$vouchers["all"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["vouchers"]?></h5>
                <p class="card-text"><?=money($vouchers_prices["all"])?></p>
            </div>
        </div>
    </div>
    <div class="card text-white bg-warning mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$vouchers["used"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["voucher_used"]?></h5>
                <p class="card-text"><?=money($vouchers_prices["used"])?></p>
            </div>
        </div>
    </div>
    <div class="card text-white bg-danger mb-3 col-12 col-md-5 col-lg-3 mr-3">
        <div class="card-body d-flex align-items-center">
            <h1 class="card-title m-0 mr-1"><?=$vouchers["unused"]?></h1>
            <div class="d-flex flex-column">
                <h5 class="card-title mb-0"><?=TRANSLATION["voucher_unused"]?></h5>
                <p class="card-text"><?=money($vouchers_prices["unused"])?></p>
            </div>
        </div>
    </div>
</div>
