<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }


    ?>
<?php if (sizeof($expl) >= 3) { ?>
    <?php
        $order = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($expl[2]));
        if ($order->num_rows != 1) {
            header("Location: " . ADMIN . "/orders");
            exit;
        }
        $order = $order->fetch_assoc();

    ?>
    <?php if (sizeof($expl) > 3) { ?>
        <?php

            $newstatus = explode(",", $order["status"]);
            $timestamps = ["paid" => (!is_null($order["paid_timestamp"]) ? "'" . $order["paid_timestamp"] . "'" : "NULL"), "delivered" => (!is_null($order["delivered_timestamp"]) ? "'" . $order["delivered_timestamp"] . "'" : "NULL"), "cancelled" => (!is_null($order["cancelled_timestamp"]) ? "'" . $order["cancelled_timestamp"] . "'" : "NULL")];
            $undourl = ADMIN . "/orders/" . $order["id"] . "/";

            if ($expl[3] == "mark-paid") {
                $newstatus[] = "paid";
                $timestamps["paid"] = "CURRENT_TIMESTAMP";
                $_SESSION["alert"] = ["success", "mark_paid", $undourl . "mark-unpaid"];
            } elseif ($expl[3] == "mark-unpaid") {
                $timestamps["paid"] = "NULL";
                if (in_array("paid", $newstatus)) {
                    unset($newstatus[array_search("paid", $newstatus)]);
                }
                $_SESSION["alert"] = ["success", "mark_unpaid", $undourl . "mark-paid"];
            } elseif ($expl[3] == "mark-delivered") {
                if (in_array("paid", $newstatus)) {
                    $newstatus[] = "delivered";
                    $timestamps["delivered"] = "CURRENT_TIMESTAMP";
                    $_SESSION["alert"] = ["success", "mark_delivered", $undourl . "mark-undelivered"];
                } else {
                    $_SESSION["alert"] = ["danger", "mark_paid_first"];
                }
            } elseif ($expl[3] == "mark-undelivered") {
                $timestamps["delivered"] = "NULL";
                if (in_array("delivered", $newstatus)) {
                    unset($newstatus[array_search("delivered", $newstatus)]);
                }
                $_SESSION["alert"] = ["success", "mark_undelivered", $undourl . "mark-delivered"];
            } elseif ($expl[3] == "cancel") {
                $newstatus[] = "cancelled";
                $timestamps["cancelled"] = "CURRENT_TIMESTAMP";
                $_SESSION["alert"] = ["success", "admin_cancelled", $undourl . "undo-cancellation"];
            } elseif ($expl[3] == "undo-cancellation") {
                $timestamps["cancelled"] = "NULL";
                if (in_array("cancelled", $newstatus)) {
                    unset($newstatus[array_search("cancelled", $newstatus)]);
                }
                $_SESSION["alert"] = ["success", "admin_undo_cancellation", $undourl . "cancel"];
            }
            $newstatus = array_unique($newstatus);
            $newstatus = implode(",", $newstatus);
            $con->query("UPDATE " . DBPREFIX . "orders SET status='" . $newstatus . "', paid_timestamp = " . $timestamps["paid"] . ", delivered_timestamp = " . $timestamps["delivered"] . ", cancelled_timestamp = " . $timestamps["cancelled"] . " WHERE id = " . $order["id"]);
            header("Location: " . ADMIN . "/orders");
            exit;
        ?>
    <?php } else { ?>
        <h1>
            <?=TRANSLATION["details"]?>
            <span class="d-block d-sm-inline-block">
                <?php if (in_array("cancelled", explode(",", $order["status"])) || in_array("delivered", explode(",", $order["status"]))) { ?>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_paid"]?>"><i class="ion-md-card text-muted icon-disabled"></i></span>
                <?php } elseif (in_array("paid", explode(",", $order["status"]))) { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/mark-unpaid" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_unpaid"]?>"><i class="ion-md-card text-muted red-on-hover"></i></a>
                <?php } else { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/mark-paid" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_paid"]?>"><i class="ion-md-card text-muted dark-on-hover"></i></a>
                <?php } ?>
                <?php if (in_array("cancelled", explode(",", $order["status"])) || !in_array("paid", explode(",", $order["status"]))) { ?>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_delivered"]?>"><i class="ion-md-cube text-muted icon-disabled"></i></span>
                <?php } elseif (in_array("delivered", explode(",", $order["status"]))) { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/mark-undelivered" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_undelivered"]?>"><i class="ion-md-cube text-muted red-on-hover"></i></a>
                <?php } else { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/mark-delivered" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_delivered"]?>"><i class="ion-md-cube text-muted dark-on-hover"></i></a>
                <?php } ?>
                <?php if (!in_array("cancelled", explode(",", $order["status"]))) { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/cancel" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["cancel"]?>"><i class="ion-md-close-circle-outline text-muted red-on-hover"></i></a>
                    <a href="<?=RELPATH?>/o/<?=$order["ordernr"]?>/<?=$order["secret"]?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["overview"]?>"><i class="ion-md-browsers text-muted dark-on-hover"></i></a>
                <?php } else { ?>
                    <a href="<?=ADMIN?>/orders/<?=$order["id"]?>/undo-cancellation" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["undo_cancellation"]?>"><i class="ion-md-checkmark-circle-outline text-muted dark-on-hover"></i></a>
                    <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["overview"]?>"><i class="ion-md-browsers text-muted icon-disabled"></i></span>
                <?php } ?>
                <a class="text-muted dark-on-hover" href="<?=ADMIN?>/orders" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
            </span>
        </h1>
        <table class="table table-hover shadow">
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["order_nr"]?></th>
                <td>
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["order_nr"]?></p>
                    <pre class="badge badge-primary mb-0"><?=$order["ordernr"]?></pre>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["amount"]?> / <?=TRANSLATION["total_price"]?></th>
                <td>
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["amount"]?> / <?=TRANSLATION["total_price"]?></p>
                    <?=$order["amount"]?> / <?=money($order["total_price"])?>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["status"]?></th>
                <td class="text-break">
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["status"]?></p>
                    <i class="ion-md-basket text-primary"></i> <?=sprintf(TRANSLATION["ordered_at"], date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($order["timestamp"])))?>
                    <?php

                            if (in_array("paid", explode(",", $order["status"]))) {
                                ?>
                                    <br /><i class="ion-md-card text-success"></i> <?=sprintf(TRANSLATION["paid_at"], date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($order["paid_timestamp"])))?>
                                <?php
                            }

                            if (in_array("delivered", explode(",", $order["status"]))) {
                                ?>
                                    <br /><i class="ion-md-cube text-info"></i> <?=sprintf(TRANSLATION["delivered_at"], date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($order["delivered_timestamp"])))?>
                                <?php
                            }

                            if (in_array("cancelled", explode(",", $order["status"]))) {
                                ?>
                                    <br /><i class="ion-md-close-circle-outline text-danger"></i> <?=sprintf(TRANSLATION["cancelled_at"], date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($order["cancelled_timestamp"])))?>
                                <?php
                            }

                    ?>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["general"]?></th>
                <td class="text-break">
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["general"]?></p>
                    <dl>
                        <?php
                            $generalfields = json_decode($order["general_fields"], true);
                            foreach (CONFIG["fields"]["general"] as $field) {
                                ?>
                                    <dt><?=$field["label"]?></dt>
                                    <dd><?=($field["type"]=="email"?"<a href=\"mailto:" . $generalfields[$field["name"]] . "\">" . $generalfields[$field["name"]] . "</a>":$generalfields[$field["name"]])?></dd>
                                <?php
                            }
                        ?>
                    </dl>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["type"]?></th>
                <td class="text-break">
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["type"]?></p>
                    <dl>
                        <dt><?=TRANSLATION["type"]?></dt>
                        <dd><?=find_in_json(CONFIG["types"], "name", $order["type"])["title"]?></dd>
                        <?php
                            $typefields = json_decode($order["type_fields"], true);
                            foreach (CONFIG["fields"]["types"][$order["type"]] as $field) {
                                ?>
                                    <dt><?=$field["label"]?></dt>
                                    <dd><?=($field["type"]=="email"?"<a href=\"mailto:" . $typefields[$field["name"]] . "\">" . $typefields[$field["name"]] . "</a>":$typefields[$field["name"]])?></dd>
                                <?php
                            }
                        ?>
                    </dl>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["payment"]?></th>
                <td class="text-break">
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["payment"]?></p>
                    <dl>
                        <?php $payment = find_in_json(CONFIG["payment"], "name", $order["payment"]); ?>
                        <dt><?=TRANSLATION["payment"]?></dt>
                        <?php if ($order["payment"] == "voucher") { ?>
                            <dd><?=TRANSLATION["voucher"]?></dd>
                        <?php } else { ?>
                            <dd><?=$payment["title"]?></dd>
                            <?php
                                $paymentfields = json_decode($order["payment_fields"], true);
                                if (array_key_exists($payment["fieldset"], CONFIG["fields"]["payment"])) {
                                    foreach (CONFIG["fields"]["payment"][$payment["fieldset"]] as $field) {
                                        ?>
                                            <dt><?=$field["label"]?></dt>
                                            <dd><?=($field["type"]=="email"?"<a href=\"mailto:" . $paymentfields[$field["name"]] . "\">" . $paymentfields[$field["name"]] . "</a>":$paymentfields[$field["name"]])?></dd>
                                        <?php
                                    }
                                }
                            }
                            $voucher = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE order_id = " . $order["id"]);
                            if ($voucher->num_rows == 1) {
                                $voucher = $voucher->fetch_assoc();
                        ?>
                                <dt><?=TRANSLATION["voucher"]?></dt>
                                <dd>
                                    <?=sprintf(TRANSLATION["used_voucher_value"], money($voucher["value"]))?><br />
                                    <a href="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>"><?=TRANSLATION["goto_voucher"]?></a>
                                </dd>
                        <?php } ?>
                    </dl>
                </td>
            </tr>
            <tr>
                <th class="d-none d-sm-block"><?=TRANSLATION["delivery"]?></th>
                <td class="text-break">
                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["delivery"]?></p>
                    <dl>
                        <?php $delivery = find_in_json(CONFIG["delivery"], "name", $order["delivery"]); ?>
                        <dt><?=TRANSLATION["delivery"]?></dt>
                        <dd><?=$delivery["title"]?></dd>
                        <?php
                            $deliveryfields = json_decode($order["delivery_fields"], true);
                            if (array_key_exists($delivery["fieldset"], CONFIG["fields"]["delivery"])) {
                                foreach (CONFIG["fields"]["delivery"][$delivery["fieldset"]] as $field) {
                                    ?>
                                        <dt><?=$field["label"]?></dt>
                                        <dd><?=($field["type"]=="email"?"<a href=\"mailto:" . $deliveryfields[$field["name"]] . "\">" . $deliveryfields[$field["name"]] . "</a>":$deliveryfields[$field["name"]])?></dd>
                                    <?php
                                }
                            }
                        ?>
                    </dl>
                </td>
            </tr>
        </table>
    <?php } ?>
<?php } else { ?>
    <h1>
        <?=TRANSLATION["orders"]?>
        <span class="d-block d-sm-inline-block">
            <span id="ord_btn_umr">
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["search"]?>" id="ord_btn_src"><i class="ion-md-search"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["filter"]?>" id="ord_btn_flt"><i class="ion-md-funnel"></i></a>
            </span>
            <span id="ord_btn_mrk" style="display: none;">
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["details"]?>" id="ord_btn_det"><i class="ion-md-eye text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_paid"]?>" id="ord_btn_paa" style="display: none;"><i class="ion-md-card text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_unpaid"]?>" id="ord_btn_pau" style="display: none;"><i class="ion-md-card text-muted red-on-hover"></i></a>
                <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_paid"]?>" style="display: none;" id="ord_btn_pad"><i class="ion-md-card text-muted icon-disabled"></i></span>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_delivered"]?>" id="ord_btn_dea" style="display: none;"><i class="ion-md-cube text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_undelivered"]?>" id="ord_btn_deu" style="display: none;"><i class="ion-md-cube text-muted red-on-hover"></i></a>
                <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["mark_delivered"]?>" style="display: none;" id="ord_btn_ded"><i class="ion-md-cube text-muted icon-disabled"></i></span>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["cancel"]?>" id="ord_btn_cla" style="display: none;"><i class="ion-md-close-circle-outline text-muted red-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["undo_cancellation"]?>" id="ord_btn_clu" style="display: none;"><i class="ion-md-checkmark-circle-outline text-muted dark-on-hover"></i></a>
                <a target="_blank" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["overview"]?>" style="display: none;" id="ord_btn_ova"><i class="ion-md-browsers text-muted dark-on-hover"></i></a>
                <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["overview"]?>" style="display: none;" id="ord_btn_ovd"><i class="ion-md-browsers text-muted icon-disabled"></i></span>
            </span>
        </span>
    </h1>
    <div class="d-flex flex-column flex-sm-row flex-wrap">
        <div class="form-group p-0 shadow col-12 col-sm-6 col-md-4 col-xl-2 mr-2" id="ord_tlb_src" style="display: none;">
            <input type="text" class="form-control" placeholder="<?=TRANSLATION["search"]?>..." id="ord_src_txt" />
        </div>
        <div class="form-group p-0 shadow mr-1 ord_tlb_flt" style="display: none;">
            <select class="form-control" id="ord_flt_sta">
                <option value="all" selected><?=TRANSLATION["status"]?>: <?=TRANSLATION["all_exept_cancelled"]?></option>
                <option value="ordered"><?=TRANSLATION["status"]?>: <?=TRANSLATION["ordered"]?></option>
                <option value="paid"><?=TRANSLATION["status"]?>: <?=TRANSLATION["paid"]?></option>
                <option value="delivered"><?=TRANSLATION["status"]?>: <?=TRANSLATION["delivered"]?></option>
                <option disabled></option>
                <option value="cancelled"><?=TRANSLATION["status"]?>: <?=TRANSLATION["cancelled"]?></option>
            </select>
        </div>
        <div class="form-group p-0 shadow mr-1 ord_tlb_flt" style="display: none;">
            <select class="form-control" id="ord_flt_typ">
                <option value="all" selected><?=TRANSLATION["type"]?>: <?=TRANSLATION["all"]?></option>
                <?php foreach(CONFIG["types"] as $type) { ?>
                    <option value="<?=$type["name"]?>"><?=TRANSLATION["type"]?>: <?=$type["title"]?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group p-0 shadow mr-1 ord_tlb_flt" style="display: none;">
            <select class="form-control" id="ord_flt_pay">
                <option value="all" selected><?=TRANSLATION["payment"]?>: <?=TRANSLATION["all"]?></option>
                <option value="voucher"><?=TRANSLATION["payment"]?>: <?=TRANSLATION["voucher"]?></option>
                <?php foreach(CONFIG["payment"] as $type) { ?>
                    <option value="<?=$type["name"]?>"><?=TRANSLATION["payment"]?>: <?=$type["title"]?></option>
                <?php } ?>
            </select>
        </div>
        <div class="form-group p-0 shadow mr-1 ord_tlb_flt" style="display: none;">
            <select class="form-control" id="ord_flt_del">
                <option value="all" selected><?=TRANSLATION["delivery"]?>: <?=TRANSLATION["all"]?></option>
                <?php foreach(CONFIG["delivery"] as $type) { ?>
                    <option value="<?=$type["name"]?>"><?=TRANSLATION["delivery"]?>: <?=$type["title"]?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="table-responsive shadow">
        <table class="table table-hover table-sm mb-4" id="ord_tbl">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"><?=TRANSLATION["amount"]?></th>
                    <th scope="col"><?=TRANSLATION["total_price"]?></th>
                    <?php

                        $fields = [];

                        foreach (CONFIG["fields"]["general"] as $general_field) {
                            if (array_key_exists("show_in_overview", $general_field) && $general_field["show_in_overview"]) {
                                $fields["general_fields"][$general_field["name"]] = $general_field["label"];
                            }
                        }

                        if (!array_key_exists("overview_hide_type", CONFIG["general"]) || !CONFIG["general"]["overview_hide_type"]) {
                            $fields["type"] = TRANSLATION["type"];
                        }
                        foreach (CONFIG["fields"]["types"] as $type) {
                            foreach ($type as $type_field) {
                                if (array_key_exists("show_in_overview", $type_field) && $type_field["show_in_overview"]) {
                                    $fields["type_fields"][$type_field["name"]] = $type_field["label"];
                                }
                            }
                        }

                        if (!array_key_exists("overview_hide_payment", CONFIG["general"]) || !CONFIG["general"]["overview_hide_payment"]) {
                            $fields["payment"] = TRANSLATION["payment"];
                        }
                        foreach (CONFIG["fields"]["payment"] as $payment) {
                            foreach ($payment as $payment_field) {
                                if (array_key_exists("show_in_overview", $payment_field) && $payment_field["show_in_overview"]) {
                                    $fields["payment_fields"][$payment_field["name"]] = $payment_field["label"];
                                }
                            }
                        }

                        if (!array_key_exists("overview_hide_delivery", CONFIG["general"]) || !CONFIG["general"]["overview_hide_delivery"]) {
                            $fields["delivery"] = TRANSLATION["delivery"];
                        }
                        foreach (CONFIG["fields"]["delivery"] as $delivery) {
                            foreach ($delivery as $delivery_field) {
                                if (array_key_exists("show_in_overview", $delivery_field) && $delivery_field["show_in_overview"]) {
                                    $fields["delivery_fields"][$delivery_field["name"]] = $delivery_field["label"];
                                }
                            }
                        }

                        foreach ($fields as $field) {
                            if (is_array($field)) {
                                foreach ($field as $subfield) {
                                    echo "<th scope=\"col\">" . $subfield . "</th>";
                                }
                            } else {
                                echo "<th scope=\"col\">" . $field . "</th>";
                            }
                        }

                    ?>
                </tr>
            </thead>
            <tbody style="display:none;">
                <?php

                    $orders = $con->query("SELECT * FROM " . DBPREFIX . "orders");
                    if ($orders->num_rows == 0) {
                        ?>
                        <tr>
                            <td colspan="<?=4+sizeof($fields, COUNT_RECURSIVE)?>" class="text-center"><i><?=TRANSLATION["no_elements"]?></i></td>
                        </tr>
                        <?php
                    } else {
                        while($row = $orders->fetch_assoc()) {
                            if (in_array("cancelled", explode(",", $row["status"]))) {
                                $row["status"] = "cancelled";
                            } elseif (in_array("delivered", explode(",", $row["status"]))) {
                                $row["status"] = "delivered";
                            } elseif (in_array("paid", explode(",", $row["status"]))) {
                                $row["status"] = "paid";
                            } else {
                                $row["status"] = "ordered";
                            }
                            ?>
                            <tr class="row-checkbox" data-id="<?=$row["id"]?>" data-type="<?=$row["type"]?>" data-payment="<?=$row["payment"]?>" data-delivery="<?=$row["delivery"]?>" data-status="<?=$row["status"]?>" data-ordernr="<?=$row["ordernr"]?>" data-secret="<?=$row["secret"]?>">
                                <th class="text-center">
                                    <input type="radio" name="ord_lst_rdo" class="ckb-lst" data-id="<?=$row["id"]?>" id="ord_lst_<?=$row["id"]?>" value="<?=$row["id"]?>" />
                                    <label class="ckb-lst-lbl" for="ord_lst_<?=$row["id"]?>"></label>
                                </th>
                                <td class="text-centers">
                                    <span class="d-none"><?=$row["ordernr"]?></span>
                                    <?php
                                        if ($row["status"] == "cancelled") {
                                            ?>
                                                <i class="ion-md-close-circle-outline text-danger" id="ord_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=sprintf(TRANSLATION["cancelled_at"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($row["cancelled_timestamp"])))?>"></i>
                                            <?php
                                        } elseif ($row["status"] == "delivered") {
                                            ?>
                                                <i class="ion-md-cube text-info" id="ord_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=sprintf(TRANSLATION["delivered_at"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($row["delivered_timestamp"])))?>"></i>
                                            <?php
                                        } elseif ($row["status"] == "paid") {
                                            ?>
                                                <i class="ion-md-card text-success" id="ord_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=sprintf(TRANSLATION["paid_at"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($row["paid_timestamp"])))?>"></i>
                                            <?php
                                        } else {
                                            ?>
                                                <i class="ion-md-basket text-primary" id="ord_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=sprintf(TRANSLATION["ordered_at"], date(TRANSLATION["date_time_format"]["date_long"], strtotime($row["timestamp"])))?>"></i>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td><?=$row["amount"]?></td>
                                <td><?=money($row["total_price"])?></td>
                                <?php

                                    foreach ($fields as $key => $field) {
                                        if (is_array($field)) {
                                            $field_json = json_decode($row[$key], true);
                                            foreach ($field as $subkey => $subfield) {
                                                if (isset($field_json[$subkey])) {
                                                    echo "<td>" . $field_json[$subkey] . "</td>";
                                                } else {
                                                    echo "<td>&ndash;</td>";
                                                }
                                            }
                                        } else {
                                            if ($key == "type") {
                                                $row[$key] = find_in_json(CONFIG["types"], "name", $row[$key])["title"];
                                            } elseif ($key == "payment" && $row[$key] == "voucher") {
                                                $row[$key] = TRANSLATION["voucher"];
                                            } elseif ($key == "payment" || $key == "delivery") {
                                                $row[$key] = find_in_json(CONFIG[$key], "name", $row[$key])["title"];
                                            }
                                            echo "<td>" . $row[$key] . "</td>";
                                        }
                                    }

                                ?>
                            </tr>
                            <?php
                        }
                    }

                ?>
            </tbody>
        </table>
        <script>
            var selec = 0;
            var linkurl = "<?=ADMIN?>/orders/";
            var linkurl_overview = "<?=RELPATH?>/o/";

            function checkbox_toggle(id) {
                if (id == selec) {
                    $("#ord_lst_" + selec).prop("checked", false);
                    selec = 0;
                    $("#ord_btn_mrk").hide();
                    $("#ord_btn_det").prop("href", "");
                    $("#ord_btn_umr").show();
                } else {
                    selec = id;
                    order = $("#ord_tbl>tbody").find("[data-id='" + selec + "']");
                    $("#ord_lst_" + selec).prop("checked", true);
                    $("#ord_btn_umr").hide();
                    $("#ord_btn_det").prop("href", linkurl + selec);
                    if (order.data("status") == "paid" || order.data("status") == "delivered" || order.data("status") == "cancelled") {
                        $("#ord_btn_paa").hide();
                        $("#ord_btn_paa").prop("href", "");
                        if (order.data("status") == "paid") {
                            $("#ord_btn_pau").prop("href", linkurl + selec + "/mark-unpaid");
                            $("#ord_btn_pau").show();
                            $("#ord_btn_pad").hide();
                        } else {
                            $("#ord_btn_pau").hide();
                            $("#ord_btn_pau").prop("href", "");
                            $("#ord_btn_pad").show();
                        }
                    } else {
                        $("#ord_btn_pad").hide();
                        $("#ord_btn_pau").hide();
                        $("#ord_btn_pau").prop("href", "");
                        $("#ord_btn_paa").prop("href", linkurl + selec + "/mark-paid");
                        $("#ord_btn_paa").show();
                    }
                    if (order.data("status") == "delivered" || order.data("status") == "cancelled") {
                        $("#ord_btn_dea").hide();
                        $("#ord_btn_dea").prop("href", "");
                        if (order.data("status") == "delivered") {
                            $("#ord_btn_deu").prop("href", linkurl + selec + "/mark-undelivered");
                            $("#ord_btn_deu").show();
                            $("#ord_btn_ded").hide();
                        } else {
                            $("#ord_btn_deu").hide();
                            $("#ord_btn_deu").prop("href", "");
                            $("#ord_btn_ded").show();
                        }
                    } else {
                        $("#ord_btn_deu").hide();
                        $("#ord_btn_deu").prop("href", "");
                        if (order.data("status") == "paid") {
                            $("#ord_btn_ded").hide();
                            $("#ord_btn_dea").prop("href", linkurl + selec + "/mark-delivered");
                            $("#ord_btn_dea").show();
                        } else {
                            $("#ord_btn_dea").hide();
                            $("#ord_btn_dea").prop("href", "");
                            $("#ord_btn_ded").show();
                        }
                    }
                    if (order.data("status") == "cancelled") {
                        $("#ord_btn_cla").hide();
                        $("#ord_btn_cla").prop("href", "");
                        $("#ord_btn_clu").prop("href", linkurl + selec + "/undo-cancellation");
                        $("#ord_btn_clu").show();
                        $("#ord_btn_ova").hide();
                        $("#ord_btn_ova").prop("href", "");
                        $("#ord_btn_ovd").show();
                    } else {
                        $("#ord_btn_clu").hide();
                        $("#ord_btn_cla").prop("href", linkurl + selec + "/cancel");
                        $("#ord_btn_cla").show();
                        $("#ord_btn_ovd").hide();
                        $("#ord_btn_ova").prop("href", linkurl_overview + order.data("ordernr") + "/" + order.data("secret"));
                        $("#ord_btn_ova").show();
                    }
                    $("#ord_btn_mrk").show();
                }
            }

            function filter() {
                status = $("#ord_flt_sta").val();
                if (status == "all" || !$(".ord_tlb_flt").is(":visible")) {
                    $("#ord_tbl>tbody>tr").filter(function() {
                        return $(this).data("status") != "cancelled";
                    }).addClass("filter");
                }
                if ($(".ord_tlb_flt").is(":visible")) {
                    if (status != "all") {
                        $("#ord_tbl>tbody>tr").filter(function() {
                            return $(this).data("status") == status;
                        }).addClass("filter");
                    }
                    type = $("#ord_flt_typ").val();
                    if (type != "all") {
                        $("#ord_tbl>tbody>tr.filter").filter(function() {
                            console.log($(this).data("type") + " <> " + type);
                            if ($(this).data("type") == type) {
                                return true;
                            } else {
                                $(this).removeClass("filter");
                                return false;
                            }
                        }).addClass("filter");
                    }
                    pay = $("#ord_flt_pay").val();
                    if (pay != "all") {
                        $("#ord_tbl>tbody>tr.filter").filter(function() {
                            if ($(this).data("payment") == pay) {
                                return true;
                            } else {
                                $(this).removeClass("filter");
                                return false;
                            }
                        }).addClass("filter");
                    }
                    del = $("#ord_flt_del").val();
                    if (del != "all") {
                        $("#ord_tbl>tbody>tr.filter").filter(function() {
                            if ($(this).data("delivery") == del) {
                                return true;
                            } else {
                                $(this).removeClass("filter");
                                return false;
                            }
                        }).addClass("filter");
                    }
                }
                query = $("#ord_src_txt").val();
                if (query.trim().length > 0 && $("#ord_tlb_src").is(":visible")) {
                    $("#ord_tbl>tbody>tr.filter").filter(function() {
                        if ($(this).text().toLowerCase().search(query.toLowerCase()) > -1) {
                            return true;
                        } else {
                            $(this).removeClass("filter");
                            return false;
                        }
                    }).addClass("filter");
                }
                if ($("#ord_tbl>tbody>tr.filter:not(#search-noresults)").length == 0) {
                    $("#ord_tbl>tbody>tr").each(function() {
                        $(this).hide();
                    });
                    if ($("#search-noresults").val() == null) {
                        $("#ord_tbl>tbody").append("<tr id=\"search-noresults\"><td colspan=\"<?=4+sizeof($fields, COUNT_RECURSIVE)?>\" class=\"text-center\"><i><?=TRANSLATION["no_elements"]?></i></td></tr>");
                    }
                    $("#search-noresults").show();
                } else {
                    $("#search-noresults").remove();
                    $("#ord_tbl>tbody>tr").each(function() {
                        if ($(this).hasClass("filter")) {
                            $(this).removeClass("filter");
                            $(this).fadeIn();
                        } else {
                            $(this).fadeOut();
                        }
                    });
                }
            }

            $(document).ready(function() {
                if ($("input[name=ord_lst_rdo]:checked").val() != null) {
                    checkbox_toggle($("input[name=ord_lst_rdo]:checked").val());
                }
                $("#ord_tbl>tbody>tr").filter(function() {
                    return $(this).data("status") == "cancelled";
                }).hide();
                $("#ord_tbl>tbody").fadeIn();
            });

            $(".ckb-lst, .row-checkbox").click(function() {
                checkbox_toggle($(this).data("id"))
            });

            $("#ord_btn_src").click(function() {
                $("#ord_tlb_src").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#ord_btn_flt").click(function() {
                $(".ord_tlb_flt").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#ord_flt_sta, #ord_flt_typ, #ord_flt_pay, #ord_flt_del").change(function() {
                filter();
            });

            $("#ord_src_txt").on("input", function() {
                filter();
            });
        </script>
    </div>
<?php } ?>
