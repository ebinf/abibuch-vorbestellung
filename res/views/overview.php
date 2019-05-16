<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    $query = $query->fetch_assoc();
    $query = query_expand_json($query);

    if (in_array("cancelled", explode(",", $query["status"]))) {
        header("Location: " . RELPATH . "/cancelled");
        exit;
    }

    $voucher = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE order_id = '" . $query["id"] . "'");
    if ($voucher->num_rows == 1) {
        $voucher = $voucher->fetch_assoc();
    } else {
        unset($voucher);
    }
?>
<div class="col-12 col-sm-8 p-3 bg-light border-0 cont">
    <h1 class="d-none d-md-block display-3"><?=sprintf(TRANSLATION["title"], globalfield($query["general_fields"], "firstname"))?></h1>
    <h1 class="d-block d-md-none display-4"><?=sprintf(TRANSLATION["title"], globalfield($query["general_fields"], "firstname"))?></h1>
    <h2><?=TRANSLATION["overview"]?></h2>

    <div class="w-100 bg-primary text-light p-2 mb-3">
        <?php

            if (in_array("paid", explode(",", $query["status"]))) {
                ?>
                    <h3 class="text-light m-0"><?=TRANSLATION["payment"]?>: <span class="text-success"><?=TRANSLATION["paid"]?></span> <small>(<?php printf(TRANSLATION["paid_extra"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($query["paid_timestamp"]))); ?>)</small></h3>
                <?php
                if (in_array("delivered", explode(",", $query["status"]))) {
                    ?>
                        <h3 class="text-light m-0"><?=TRANSLATION["delivery"]?>: <span class="text-success"><?=TRANSLATION["delivered"]?></span> <small>(<?php printf(TRANSLATION["delivered_extra"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($query["paid_timestamp"]))); ?>)</small></h3>
                    <?php
                } else {
                    ?>
                        <h3 class="text-light m-0"><?=TRANSLATION["delivery"]?>: <span class="text-danger"><?=TRANSLATION["undelivered"]?></span></h3>
                    <?php
                }
            } else {
                ?>
                    <h3 class="text-light"><?=TRANSLATION["payment"]?>: <span class="text-danger"><?=TRANSLATION["unpaid"]?></span></h3>
                    <p class="m-0"><?php printf(TRANSLATION["pay_till"], '<span class="badge badge-secondary">' . date(TRANSLATION["date_time_format"]["date_long"], strtotime(CONFIG["general"]["pay_till"])) . '</span>'); ?></p>
                <?php
            }

        ?>
    </div>
    <div class="w-100 p-2 mb-3">
        <?php

            if (!in_array("paid", explode(",", $query["status"])) && file_exists("./data/payment/" . find_in_json(CONFIG["payment"], "name", $query["payment"])["info"] . ".php")) {
                $paymentinfo = include("./data/payment/" . find_in_json(CONFIG["payment"], "name", $query["payment"])["info"] . ".php");

                echo patternmatch($paymentinfo, $query);
            } elseif (in_array("paid", explode(",", $query["status"])) && !in_array("delivered", explode(",", $query["status"])) && file_exists("./data/delivery/" . find_in_json(CONFIG["delivery"], "name", $query["delivery"])["info"] . ".php")) {
                $deliveryinfo = include("./data/delivery/" . find_in_json(CONFIG["delivery"], "name", $query["delivery"])["info"] . ".php");

                echo patternmatch($deliveryinfo, $query);
            }

        ?>
    </div>
    <div class="w-100 position-fixed d-block d-sm-none text-center" style="left: 0; right: 0; bottom: 0; z-index: 1;"><h2><i class="ion-ios-arrow-down" aria-hidden="true" onclick="$('html, body').animate({ scrollTop: $('#secondView').offset().top }, 1000);" style="text-shadow: #ffffff 0 0 5px;"></i></h2></div>
</div>
<div class="p-2 col-12 col-sm-4 bg-primary text-light border-0" style="z-index: 5;" id="secondView">
    <h3 class="text-light"><?=TRANSLATION["order"]?></h3>
    <table class="w-100">
        <tr>
            <td class="text-right pr-1" style="min-width: 9%; width: 9%;"><?=$query["amount"]?></td>
            <td><?=($query["amount"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"])?></td>
            <td class="text-right"><?=money(CONFIG["product"]["price"] * $query["amount"])?></td>
        </tr>
        <?php

            if (find_in_json(CONFIG["payment"], "name", $query["payment"])["price"] != 0) {
                ?>
                    <tr>
                        <td></td>
                        <td><?=TRANSLATION["payment"]?></td>
                        <td class="text-right"><?=money(find_in_json(CONFIG["payment"], "name", $query["payment"])["price"])?></td>
                    </tr>
                <?php
            }

            if (find_in_json(CONFIG["delivery"], "name", $query["delivery"])["price"] != 0) {
                ?>
                    <tr>
                        <td></td>
                        <td><?=TRANSLATION["delivery"]?></td>
                        <td class="text-right"><?=money(find_in_json(CONFIG["delivery"], "name", $query["delivery"])["price"])?></td>
                    </tr>
                <?php
            }

            if (isset($voucher) && $voucher["value"] != 0) {
                ?>
                    <tr>
                        <td></td>
                        <td><?=TRANSLATION["voucher"]?></td>
                        <td class="text-right"><?=money(-1 * $voucher["value"])?></td>
                    </tr>
                <?php
            }

        ?>
        <tr class="border-top">
            <td></td>
            <td><?=TRANSLATION["total"]?></td>
            <td class="text-right"><?=money($query["total_price"])?></td>
        </tr>
    </table>
    <br />
    <h3 class="text-light"><?=TRANSLATION["payment"]?></h3>
    <p><?php
        if ($query["payment"] == "voucher") {
            echo TRANSLATION["voucher"];
        } else {
            $payment = find_in_json(CONFIG_RAW["payment"], "name", $query["payment"]);
            echo $payment["title"];
            if (strlen($payment["data"]) > 0) {
                echo "<br />";
                echo patternmatch($payment["data"], $query);
            }
        }
    ?></p>
    <h3 class="text-light"><?=TRANSLATION["delivery"]?></h3>
    <p><?php
        $delivery = find_in_json(CONFIG_RAW["delivery"], "name", $query["delivery"]);
        echo $delivery["title"];
        if (strlen(patternmatch($delivery["data"], $query)) > 0) {
            echo "<br />";
            echo patternmatch($delivery["data"], $query);
        }
    ?></p>
    <h3 class="text-light"><?=TRANSLATION["email"]?></h3>
    <p><?=globalfield($query["general_fields"], "email")?></p>
    <hr class="border-light" />
    <?php
        if (in_array("paid", explode(",", $query["status"]))) {
            ?>
    <p class="lead"><?=TRANSLATION["cancel_paid_lead"]?></p>
    <a class="btn btn-secondary float-right" href="mailto:<?=CONFIG["general"]["contact_email"]?>?subject=<?=rawurlencode("[" . $query["ordernr"] . "] " . TRANSLATION["cancel_paid_subject"])?>"><?=TRANSLATION["cancel_paid"]?></a>
</div>
            <?php
        } else {
            ?>
            <p class="lead"><?=TRANSLATION["cancel_lead"]?></p>
    <button type="button" class="btn btn-secondary float-right" data-toggle="modal" data-target="#cancelModal"><?=TRANSLATION["cancel"]?></button>
</div>
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel"><?=TRANSLATION["cancel"]?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?=TRANSLATION["cancel_sure"]?></p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="<?=RELPATH?>/cancel">
                    <input type="hidden" name="ordernr" value="<?=$query["ordernr"]?>" />
                    <input type="hidden" name="secret" value="<?=$query["secret"]?>" />
                    <input type="hidden" name="token" value="<?=md5($query["timestamp"])?>" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?=TRANSLATION["abort"]?></button>
                    <input type="submit" class="btn btn-primary" value="<?=TRANSLATION["cancel"]?>" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>
