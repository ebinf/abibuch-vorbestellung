<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (!isset($_POST["typ_type"])) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    $step1 = [];
    foreach ($_POST as $key => $field) {
        $step1[substr($key, 4)] = $field;
    }

    if (!find_in_json(CONFIG["types"], "name", $step1["type"])) {

    }

    foreach (CONFIG["fields"]["general"] as $field) {
        if (!validate_field($step1, $field)) {
            header("Location: " . RELPATH . "/missing");
            exit;
        }
    }
    foreach (CONFIG["fields"]["types"][$_POST["typ_type"]] as $field) {
        if (!validate_field($step1, $field)) {
            header("Location: " . RELPATH . "/missing");
            exit;
        }
    }

?>
<div class="col-12 col-sm-8 p-3 bg-light border-0 cont">
    <h1 class="display-3"><?php printf(TRANSLATION["title"], htmlspecialchars($_POST["inp_" . CONFIG["general"]["firstname"]], ENT_NOQUOTES)); ?></h1>
    <div class="w-100 bg-primary text-light p-2 mb-3">
        <table class="w-100">
            <tr>
                <td width="70px" class="mr-2">
                    <img src="./data/images/transparent.png" class="float-left" width="70px" height="70px" />
                </td>
                <td>
                    <p class="m-0"><?=CONFIG["product"]["name"]?><br />
                    <small><?=CONFIG["product"]["description"]?></small></p>
                    <p class="mt-2 m-0"><?=money(CONFIG["product"]["price"])?></p>
                </td>
                <td>
                    <form class="form-inline float-right">
                        <select class="form-control" id="sel_abi_qty">
                            <?php
                                $cnt = CONFIG["product"]["min"];
                                while ($cnt <= CONFIG["product"]["max"]) {
                                    if ($cnt == 1) {
                                        $pref = (strlen(CONFIG["product"]["unit"]["prefix"]) > 0 ? CONFIG["product"]["unit"]["prefix"] . " " : "");
                                        $suff = (strlen(CONFIG["product"]["unit"]["suffix"]) > 0 ? " " . CONFIG["product"]["unit"]["suffix"] : "");
                                    } else {
                                        $pref = (strlen(CONFIG["product"]["unit"]["prefix_plural"]) > 0 ? CONFIG["product"]["unit"]["prefix_plural"] . " " : "");
                                        $suff = (strlen(CONFIG["product"]["unit"]["suffix_plural"]) > 0 ? " " . CONFIG["product"]["unit"]["suffix_plural"] : "");
                                    }
                                    echo '<option value="' . $cnt .'">' . $pref . $cnt . $suff . '</option>';
                                    $cnt += 1;
                                }
                            ?>
                        </select>
                    </form>
                </td>
            </tr>
        </table>
    </div>
    <?php

            $vouchers = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE order_id = '0'");
            if ($vouchers->num_rows > 0) {
                $vouchers = true;
                ?>
                    <div class="form-group col-12">
                        <label for="vou_cde"><?=TRANSLATION["voucher_field"]?></label>
                        <input type="text" class="form-control text-uppercase" id="vou_cde" placeholder="XXX-XXX-XXX-XXX" maxlength="15" />
                        <div class="invalid-tooltip"><?=TRANSLATION["voucher_invalid"]?></div>
                    </div>
                <?php
            } else {
                $vouchers = false;
            }

            $buttons = "";
            $contents = "";
            $cnt = 0;
            $fields = false;
            $pay_cont = "";
            foreach(CONFIG["payment"] as $payment) {
                if (!array_key_exists("types", $payment) || in_array($step1["type"], $payment["types"])) {
                    if ($cnt == 0) {
                        $buttons .= '<a class="nav-item nav-link m-0 active" id="pay_' . $payment["name"] . '-tab" href="#pay_' . $payment["name"] . '" data-toggle="pill" aria-controls="pay_' . $payment["name"] . '" aria-selected="true">' . $payment["description"] . ($payment["price"] != 0 ? " (" . money($payment["price"], true) . ")" : "") . '</a>';
                        $contents .= '<div class="tab-pane fade show active" id="pay_' . $payment["name"] . '" role="tabpanel" aria-labelledby="pay_' . $payment["name"] . '-tab">';
                    } else {
                        $buttons .= '<a class="nav-item nav-link m-0" id="pay_' . $payment["name"] . '-tab" href="#pay_' . $payment["name"] . '" data-toggle="pill" aria-controls="pay_' . $payment["name"] . '" aria-selected="false">' . $payment["description"] . ($payment["price"] != 0 ? " (" . money($payment["price"], true) . ")" : "") . '</a>';
                        $contents .= '<div class="tab-pane fade" id="pay_' . $payment["name"] . '" role="tabpanel" aria-labelledby="pay_' . $payment["name"] . '-tab">';
                    }
                    if (strlen($payment["fieldset"]) > 0 && key_exists($payment["fieldset"], CONFIG["fields"]["payment"])) {
                        $fields = true;
                        $contents .= '<form class="form-row" id="pay_' . $payment["name"] . '_frm" onsubmit="return false;">';
                        foreach(CONFIG["fields"]["payment"][$payment["fieldset"]] as $field) {
                            $contents .= ext_field($field);
                        }
                        $contents .= '</form>';
                    }
                    $contents .= '</div>';
                    $cnt += 1;
                }
            }
            if ($cnt > 1 || $fields == true) {
                $pay_cont = '<div class="nav flex-column nav-pills w-100" aria-orientation="vertical" role="tablist" id="tab_pay_lnk">';
                $pay_cont .=  $buttons;
                $pay_cont .=  '</div>';
                $pay_cont .=  '<div class="col-10 offset-1 m-2"><div class="tab-content" id="tbs_pay">';
                $pay_cont .=  $contents;
                $pay_cont .=  '</div></div>';
            }

            $buttons = "";
            $contents = "";
            $cnt = 0;
            $fields = false;
            $del_cont = "";
            foreach(CONFIG["delivery"] as $delivery) {
                if (!array_key_exists("types", $delivery) || in_array($step1["type"], $delivery["types"])) {
                    if ($cnt == 0) {
                        $buttons .= '<a class="nav-item nav-link m-0 active" id="del_' . $delivery["name"] . '-tab" href="#del_' . $delivery["name"] . '" data-toggle="pill" aria-controls="del_' . $delivery["name"] . '" aria-selected="true">' . $delivery["description"] . ($delivery["price"] != 0 ? " (" . money($delivery["price"], true) . ")" : "") . '</a>';
                        $contents .= '<div class="tab-pane fade show active" id="del_' . $delivery["name"] . '" role="tabpanel" aria-labelledby="del_' . $delivery["name"] . '-tab">';
                    } else {
                        $buttons .= '<a class="nav-item nav-link m-0" id="del_' . $delivery["name"] . '-tab" href="#del_' . $delivery["name"] . '" data-toggle="pill" aria-controls="del_' . $delivery["name"] . '" aria-selected="false">' . $delivery["description"] . ($delivery["price"] != 0 ? " (" . money($delivery["price"], true) . ")" : "") . '</a>';
                        $contents .= '<div class="tab-pane fade" id="del_' . $delivery["name"] . '" role="tabpanel" aria-labelledby="del_' . $delivery["name"] . '-tab">';
                    }
                    if (strlen($delivery["fieldset"]) > 0 && key_exists($delivery["fieldset"], CONFIG["fields"]["delivery"])) {
                        $fields = true;
                        $contents .= '<form class="form-row" id="del_' . $delivery["name"] . '_frm" onsubmit="return false;">';
                        foreach(CONFIG["fields"]["delivery"][$delivery["fieldset"]] as $field) {
                            $contents .= ext_field($field);
                        }
                        $contents .= '</form>';
                    }
                    $contents .= '</div>';
                    $cnt += 1;
                }
            }
            if ($cnt > 1 || $fields == true) {
                if (!empty($pay_cont) || $vouchers == true) {
                    $del_cont = "<hr />";
                }
                $del_cont .= '<div class="nav flex-column nav-pills w-100" aria-orientation="vertical" role="tablist" id="tab_del_lnk">';
                $del_cont .= $buttons;
                $del_cont .= '</div>';
                $del_cont .= '<div class="col-10 offset-1 m-2"><div class="tab-content" id="tbs_del">';
                $del_cont .= $contents;
                $del_cont .= '</div></div>';
            }

            if ($vouchers == true && !empty($pay_cont)) {
                echo "<hr id=\"hor_pay_del\" />";
            }
            echo $pay_cont;
            echo $del_cont;

        ?>
        <div class="w-100 position-fixed d-block d-sm-none text-center" style="left: 0; right: 0; bottom: 0; z-index: 1;"><h2><i class="ion-ios-arrow-down" aria-hidden="true" onclick="$('html, body').animate({ scrollTop: $('#secondView').offset().top }, 1000);"></i></h2></div>
    </div>
    <div class="p-2 col-12 col-sm-4 bg-primary text-light border-0" style="z-index: 5;" id="secondView">
    <h3 class="text-light"><?=TRANSLATION["order"]?></h3>
    <table class="w-100" id="tbl_prc">
        <tr>
            <td class="text-right pr-1" style="min-width: 9%; width: 9%;" id="lbl_abi_qty"><?=CONFIG["product"]["min"]?></td>
            <td id="lbl_abi_plr"><?=(CONFIG["product"]["min"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"])?></td>
            <td class="text-right" id="lbl_abi_pre"><?=money(CONFIG["product"]["min"] * CONFIG["product"]["price"])?></td>
        </tr>
        <?php
            if (CONFIG["payment"][0]["price"] != 0) {
                ?>
                    <tr id="tbl_prc_pay">
                        <td></td>
                        <td><?=TRANSLATION["payment"]?></td>
                        <td class="text-right"><?=money(CONFIG["payment"][0]["price"])?></td>
                    </tr>
                <?php
            }
        ?>
        <?php
            if (CONFIG["delivery"][0]["price"] != 0) {
                ?>
                    <tr id="tbl_prc_del">
                        <td></td>
                        <td><?=TRANSLATION["delivery"]?></td>
                        <td class="text-right"><?=money(CONFIG["delivery"][0]["price"])?></td>
                    </tr>
                <?php
            }
        ?>
        <tr class="border-top">
            <td></td>
            <td><?=TRANSLATION["total"]?></td>
            <td class="text-right" id="lbl_ges_pre">16,00 €</td>
        </tr>
    </table>
    <br />
    <h3 class="text-light"><?=TRANSLATION["payment"]?></h3>
    <p id="lbl_bez_met"><?=CONFIG["payment"][0]["title"]?></p>
    <h3 class="text-light"><?=TRANSLATION["delivery"]?></h3>
    <p id="lbl_ver_met"><?=CONFIG["delivery"][0]["title"]?></p>
    <h3 class="text-light"><?=TRANSLATION["email"]?></h3>
    <p><?=htmlspecialchars($_POST["inp_" . CONFIG["general"]["email"]], ENT_NOQUOTES)?></p>
    <hr class="border-light" />
    <p class="lead"><?=TRANSLATION["place_order_lead"]?></p>
    <button class="btn btn-secondary float-right" id="frm_sbm"><?=TRANSLATION["place_order"]?></button>
</div>
<div id="bdy_ale"></div>
<script>
    var step1 = "<?=base64_encode(json_encode($step1))?>";
    var product_name = "<?=CONFIG["product"]["name"]?>";
    var product_name_plural = "<?=CONFIG["product"]["name_plural"]?>";
    var product_price = <?=CONFIG["product"]["price"]?>;
    var prices_product = <?=CONFIG["product"]["price"]*CONFIG["product"]["min"]?>;
    var prices_delivery = <?=CONFIG["delivery"][0]["price"]?>;
    var prices_payment = <?=CONFIG["payment"][0]["price"]?>;
    var id_payment = "<?=CONFIG["payment"][0]["name"]?>";
    var id_delivery = "<?=CONFIG["delivery"][0]["name"]?>";
    var quantity = <?=CONFIG["product"]["min"]?>;
    var voucher = 0;
    var voucher_code = "";
    var total = 0;
    var payment_old = "";
    var payment = {<?php
            $jsonjs = "";
            foreach(CONFIG["payment"] as $payment) {
                $jsonjs .= '"pay_' . $payment["name"] . '-tab": { "price": ' . $payment["price"] . ', "title": "' . $payment["title"] . '"},';
            }
            echo substr($jsonjs, 0, -1);
        ?>};
    var delivery = {<?php
            $jsonjs = "";
            foreach(CONFIG["delivery"] as $delivery) {
                $jsonjs .= '"del_' . $delivery["name"] . '-tab": { "price": ' . $delivery["price"] . ', "title": "' . $delivery["title"] . '"},';
            }
            echo substr($jsonjs, 0, -1);
        ?>};

    function money(amount) {
        amount = parseFloat(amount).toFixed(<?=TRANSLATION["money_format"]["decimals"]?>);
        amount = amount.replace(".", "<?=TRANSLATION["money_format"]["dec_point"]?>");
        var splitNum = amount.split("<?=TRANSLATION["money_format"]["dec_point"]?>");
        splitNum[0] = splitNum[0].replace(/\B(?=(\d{3})+(?!\d))/g, "<?=TRANSLATION["money_format"]["thousands_sep"]?>");
        amount = splitNum.join("<?=TRANSLATION["money_format"]["dec_point"]?>");
        return "<?=(strlen(TRANSLATION["money_format"]["prefix"]) > 0 ? TRANSLATION["money_format"]["prefix"] . "&nbsp;" : "")?>" + amount + "<?=(strlen(TRANSLATION["money_format"]["suffix"]) > 0 ? "&nbsp;" . TRANSLATION["money_format"]["suffix"] : "")?>";
    }

    function alert(type, title, text) {
        $("#bdy_ale").html('<div class="fixed-top">\
            <div class="alert alert-d ismissible alert-' + type + ' fade show">\
                <button type="button" class="close" data-dismiss="alert">&times;</button>\
                <h4 class="alert-heading">' + title + '</h4>\
                <p class="mb-0">' + text + '</p>\
            </div>\
        </div>');
    }

    function update_total_price() {
        if (prices_product + prices_delivery + voucher <= 0) {
            if (id_payment != "voucher") {
                payment_old = id_payment;
            }
            prices_payment = 0;
            id_payment = "voucher";
            $("#tab_pay_lnk").hide();
            $("#tbs_pay").hide();
            $("#tbl_prc_pay").remove();
            $("#hor_pay_del").hide();
            $("#lbl_bez_met").html("<?=TRANSLATION["voucher"]?>");
        } else {
            $("#tab_pay_lnk").show();
            $("#tbs_pay").show();
            $("#hor_pay_del").show();
            if (payment_old != "") {
                prices_payment = payment['pay_' + payment_old + '-tab']["price"];
                id_payment = payment_old;
                $("#lbl_bez_met").html(payment['pay_' + payment_old + '-tab']["title"]);
                $("#tbl_prc_pay").remove();
                if (prices_payment != 0) {
                    $("#tbl_prc tr:first").after("<tr id=\"tbl_prc_pay\"><td></td><td><?=TRANSLATION["payment"]?></td><td class=\"text-right\">" + money(prices_payment) + "</td></tr>");
                }
                payment_old = "";
            }
        }
        total = prices_product + prices_delivery + prices_payment + voucher;
        if (total < 0) {
            total = 0;
        }
        $("#lbl_ges_pre").html(money(total));
        return true;
    }

    $("#sel_abi_qty").change(function() {
        $("#lbl_abi_qty").html(this.value);
        if (this.value == 1) {
            $("#lbl_abi_plr").html(product_name);
        } else {
            $("#lbl_abi_plr").html(product_name_plural);
        }
        quantity = this.value;
        prices_product = this.value * product_price;
        $("#lbl_abi_pre").html(money(prices_product));
        update_total_price();
    });

    $('#tab_pay_lnk a').click(function() {
        prices_payment = payment[this.id]["price"];
        id_payment = this.id.substr(4, this.id.length - 8);
        $("#lbl_bez_met").html(payment[this.id]["title"]);
        $("#tbl_prc_pay").remove();
        if (prices_payment != 0) {
            $("#tbl_prc tr:first").after("<tr id=\"tbl_prc_pay\"><td></td><td><?=TRANSLATION["payment"]?></td><td class=\"text-right\">" + money(prices_payment) + "</td></tr>");
        }
        update_total_price();
    });

    $('#tab_del_lnk a').click(function() {
        prices_delivery = delivery[this.id]["price"];
        id_delivery = this.id.substr(4, this.id.length - 8);
        $("#lbl_ver_met").html(delivery[this.id]["title"]);
        $("#tbl_prc_del").remove();
        if (prices_delivery != 0) {
            $("#tbl_prc tr:last").before("<tr id=\"tbl_prc_del\"><td></td><td><?=TRANSLATION["delivery"]?></td><td class=\"text-right\">" + money(prices_delivery) + "</td></tr>");
        }
        update_total_price();
    });

    $('#vou_cde').on("input", function() {
        $("#tbl_prc_vou").remove();
        voucher = 0;
        voucher_code = "";
        if (this.value.length == 15) {
            $.post("<?=RELPATH?>/voucher",
                {code: this.value},
                function(data) {
                    var explode = data.split("valid: ");
                    if (explode.length == 2) {
                        $('#vou_cde').addClass("is-valid");
                        voucher = -1 * explode[1];
                        voucher_code = $('#vou_cde').val();
                        if (voucher != 0) {
                            $("#tbl_prc tr:last").before("<tr id=\"tbl_prc_vou\"><td></td><td><?=TRANSLATION["voucher"]?></td><td class=\"text-right\">" + money(voucher) + "</td></tr>");
                        }
                        update_total_price();
                        $('#vou_cde').blur();
                    } else {
                        $('#vou_cde').addClass("is-invalid");
                    }
                }
            );
        } else {
            if (this.value.length > 15) {
                $('#vou_cde').blur();
            }
            $('#vou_cde').removeClass("is-invalid");
            $('#vou_cde').removeClass("is-valid");
        }
        update_total_price();
    });

    $("#frm_sbm").click(function() {
        if (
            (document.getElementById("pay_" + id_payment + "_frm") == null ||
            document.getElementById("pay_" + id_payment + "_frm").reportValidity() == true) &&
            (document.getElementById("del_" + id_delivery + "_frm") == null ||
            document.getElementById("del_" + id_delivery + "_frm").reportValidity() == true)
        ) {
            if (document.getElementById("pay_" + id_payment + "_frm") != null) {
                var payment_form = $("#pay_" + id_payment + "_frm").serializeArray().reduce(function(obj, item) {
                    obj[item.name.substr(4)] = item.value;
                    return obj;
                }, {});
            } else {
                var payment_form = "";
            }
            if (document.getElementById("del_" + id_delivery + "_frm") != null) {
                var delivery_form = $("#del_" + id_delivery + "_frm").serializeArray().reduce(function(obj, item) {
                    obj[item.name.substr(4)] = item.value;
                    return obj;
                }, {});
            } else {
                var delivery_form = "";
            }
            var postdata = {
                "step1": step1,
                "quantity": quantity,
                "payment": id_payment,
                "payment_form": payment_form,
                "delivery": id_delivery,
                "delivery_form": delivery_form,
                "voucher": voucher_code,
                "price": total
            };
            $.post("<?=RELPATH?>/validate",
                postdata,
                function(data) {
                    if (data == "success") {
                        $(location).attr("href", "<?=RELPATH?>/thanks");
                    } else if (data == "invalid") {
                        alert("danger", "<?=TRANSLATION["alerts"]["missing_title"]?>", "<?=TRANSLATION["alerts"]["missing_message"]?>");
                    } else {
                        $(location).attr("href", "<?=RELPATH?>/error");
                    }
                }
            );
        } else {
            alert("danger", "<?=TRANSLATION["alerts"]["missing_title"]?>", "<?=TRANSLATION["alerts"]["missing_message"]?>");
        }
    });

    $(document).ready(update_total_price());
</script>
