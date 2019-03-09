<?php
    require("./res/glb/global.inc.php");
    echo $header;

    $id = "1";
    $query = $con->query("SELECT * FROM orders WHERE id = '" . $id . "'");
    $query = $query->fetch_assoc();
    $query = query_expand_json($query);
?>
                <div class="col-12 col-sm-8 p-3 overflow-auto">
                    <h1 class="display-3"><?=greeting($query["general_fields"])?></h1>
                    <h2><?=$translation["overview"]?></h2>

                    <div class="w-100 bg-primary text-light p-2 mb-3">
                        <?php

                            if (strpos($query["status"], "paid") !== false) {
                                ?>
                                    <h3 class="text-light"><?=$translation["payment"]?>: <span class="text-success"><?=$translation["paid"]?></span> <small>(<?php printf($translation["paid_extra"], "01.01.2001"); ?>)</small></h3>
                                <?php
                            } else {
                                ?>
                                    <h3 class="text-light"><?=$translation["payment"]?>: <span class="text-danger"><?=$translation["unpaid"]?></span></h3>
                                    <p class="m-0"><?php printf($translation["pay_till"], '<span class="badge badge-secondary">' . $config["general"]["pay_till"] . '</span>'); ?></p>
                                <?php
                            }

                        ?>
                    </div>
                    <div class="w-100 p-2 mb-3">
                        <?php

                            if (strpos($query["status"], "paid") === false && file_exists("./res/paymentinfos/" . find_in_json($config["payment"], "name", $query["payment"])["info"] . ".php")) {
                                $paymentinfo = include("./res/paymentinfos/" . find_in_json($config["payment"], "name", $query["payment"])["info"] . ".php");

                                echo patternmatch($paymentinfo, $query);
                            }

                        ?>
                    </div>
                    <p class="text-center d-block d-sm-none"><i class="fa fa-angle-down fa-3x" aria-hidden="true"></i></p>
                </div>
                <div class="p-2 col-12 col-sm-4 bg-primary text-light overflow-auto">
                    <h3 class="text-light"><?=$translation["order"]?></h3>
                    <table class="w-100">
                        <tr>
                            <td style="text-align: right;"><?=$query["amount"]?><td>
                            <td><?=($query["amount"] == 1 ? $config["product"]["name"] : $config["product"]["name_plural"])?></td>
                            <td style="text-align: right;"><?=money($config["product"]["price"] * $query["amount"])?></td>
                        </tr>
                        <tr class="border-top">
                            <td><td>
                            <td><?=$translation["total"]?></td>
                            <td style="text-align: right;"><?=money($query["total_price"])?></td>
                        </tr>
                    </table>
                    <br />
                    <h3 class="text-light"><?=$translation["delivery"]?></h3>
                    <p><?php
                        $delivery = find_in_json($config["delivery"], "name", $query["delivery"]);
                        echo $delivery["title"];
                        if (strlen($delivery["data"]) > 0) {
                            echo "<br />";
                            echo patternmatch($delivery["data"], $query);
                        }
                    ?></p>
                    <h3 class="text-light"><?=$translation["payment"]?></h3>
                    <p><?php
                        $payment = find_in_json($config["payment"], "name", $query["payment"]);
                        echo $payment["title"];
                        if (strlen($payment["data"]) > 0) {
                            echo "<br />";
                            echo patternmatch($payment["data"], $query);
                        }
                    ?></p>
                    <h3 class="text-light"><?=$translation["email"]?></h3>
                    <p><?=globalfield($query["general_fields"], "email")?></p>
                    <hr class="bg-light" />
                    <?php
                        if (strpos($query["status"], "paid") !== false) {
                            ?>
                                <p class="lead"><?=$translation["cancel_paid_lead"]?></p>
                                <a class="btn btn-secondary float-right" href="mailto:<?=$config["general"]["contact_email"]?>"><?=$translation["cancel_paid"]?></a>
                            <?php
                        } else {
                            ?>
                                <p class="lead"><?=$translation["cancel_lead"]?></p>
                                <a class="btn btn-secondary float-right" href="#"><?=$translation["cancel"]?></a>
                            <?php
                        }
                    ?>
                </div>
<?php echo $footer; ?>
