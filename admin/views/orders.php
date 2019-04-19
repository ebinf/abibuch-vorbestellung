<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) > 3) {
        header("Location: " . ADMIN . "/orders");
        exit;
    }
    ?>
<?php if (sizeof($expl) == 3) ?>
    <?php

        $order = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($expl[2]));
        if ($order->num_rows != 1) {
            header("Location: " . ADMIN . "/orders");
            exit;
        }
        $order = $order->fetch_assoc();

    ?>
    <h1>
        <?=TRANSLATION["details"]?>
        <span class="d-inline-block">
            <a href="<?=ADMIN?>/ordchers/<?=$ordcher["id"]?>/edit" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["edit"]?>"><i class="ion-md-create"></i></a>
            <a href="<?=ADMIN?>/ordchers/<?=$ordcher["id"]?>/print" target="_blank" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["ordcher_print"]?>"><i class="ion-md-gift"></i></a>
            <?php if ($ordcher["order_id"] == 0) { ?>
                <a href="<?=ADMIN?>/ordchers/<?=$ordcher["id"]?>/delete" class="text-muted red-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["delete"]?>"><i class="ion-md-trash"></i></a>
            <?php } else { ?>
                <span class="text-muted icon-disabled" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["ordcher_no_editing"]?>"><i class="ion-md-trash"></i></span>
            <?php } ?>
            <a class="text-muted dark-on-hover" href="<?=ADMIN?>/ordchers" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
        </span>
    </h1>
    <table class="table table-hover shadow">
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["ordcher_code"]?></th>
            <td>
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["ordcher_code"]?></p>
                <pre class="badge badge-primary mb-0"><?=$ordcher["code"]?></pre>
            </td>
        </tr>
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["ordcher_value"]?></th>
            <td>
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["ordcher_value"]?></p>
                <?=money($ordcher["value"])?></span>
            </td>
        </tr>
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["comment"]?></th>
            <td class="text-break">
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["comment"]?></p>
                <?=str_replace(["\r\n", "\r", "\n"], "<br />", $ordcher["comment"])?>
            </td>
        </tr>
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["created_by"]?></th>
            <td class="text-break">
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["created_by"]?></p>
                <?=$con->query("SELECT * FROM " . DBPREFIX . "users WHERE id=" . $ordcher["userid"])->fetch_assoc()["name"]?>
            </td>
        </tr>
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["created_at"]?></th>
            <td>
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["created_at"]?></p>
                <?=date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($ordcher["timestamp"]))?>
            </td>
        </tr>
        <tr>
            <th class="d-none d-sm-block"><?=TRANSLATION["status"]?></th>
            <td>
                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["status"]?></p>
                <?php
                    if ($ordcher["order_id"] == 0) {
                        ?>
                            <i class="ion-md-square text-success"></i> <?=TRANSLATION["ordcher_unused"]?>
                        <?php
                    } else {
                        $row_order = $con->query("SELECT timestamp FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($ordcher["order_id"]))->fetch_assoc();
                        ?>
                            <i class="ion-md-square-outline text-danger"></i> <?=sprintf(TRANSLATION["ordcher_used_at"], date(TRANSLATION["date_time_format"]["datetime"], strtotime($row_order["timestamp"])))?><br />
                            <a href="<?=ADMIN?>/orders/<?=$ordcher["order_id"]?>"><?=TRANSLATION["goto_order"]?></a>
                        <?php
                    }
                ?>
            </td>
        </tr>
    </table>
<?php } else { ?>
    <h1>
        <?=TRANSLATION["orders"]?>
        <span class="d-inline-block">
            <span id="ord_btn_umr">
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["search"]?>" id="ord_btn_src"><i class="ion-md-search"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["filter"]?>" id="ord_btn_flt"><i class="ion-md-funnel"></i></a>
            </span>
            <span id="ord_btn_mrk" style="display: none;">
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["details"]?>" id="ord_btn_det"><i class="ion-md-eye text-muted dark-on-hover"></i></a>
            </span>
        </span>
    </h1>
    <div class="d-flex flex-column flex-sm-row">
        <div class="form-group p-0 shadow col-12 col-sm-6 col-md-4 col-xl-2 mr-2" id="ord_tlb_src" style="display: none;">
            <input type="text" class="form-control" placeholder="<?=TRANSLATION["search"]?>..." id="ord_src_txt" />
        </div>
        <div class="form-group p-0 shadow mr-1 ord_tlb_flt" style="display: none;">
            <select class="form-control" id="ord_flt_sta">
                <option value="all" selected><?=TRANSLATION["status"]?>: <?=TRANSLATION["all"]?></option>
                <option value="unused"><?=TRANSLATION["status"]?>: <?=TRANSLATION["ordcher_unused"]?></option>
                <option value="used"><?=TRANSLATION["status"]?>: <?=TRANSLATION["ordcher_used"]?></option>
            </select>
        </div>
    </div>
    <div class="table-responsive shadow">
        <table class="table table-hover table-sm mb-4" id="ord_tbl">
            <thead>
                <tr>
                    <th scope="col">Checkbox</th>
                    <th scope="col">Status</th>
                    <th scope="col">Anzahl</th>
                    <th scope="col">Gesamtpreis</th>
                    <?php

                        $fields = [];

                        if (!array_key_exists("overview_hide_type") || !CONFIG["general"]["overview_hide_type"]) {
                            $fields["type"] = TRANSLATION["type"];
                        }
                        foreach (CONFIG["fields"]["types"] as $type) {
                            foreach ($type as $type_field) {
                                if (array_key_exists("show_in_overview", $type_field) && $type_field["show_in_overview"]) {
                                    $fields[$type_field["name"]] = $type_field["label"];
                                }
                            }
                        }

                        if (!array_key_exists("overview_hide_payment") || !CONFIG["general"]["overview_hide_payment"]) {
                            $fields["payment"] = TRANSLATION["payment"];
                        }
                        foreach (CONFIG["fields"]["payment"] as $payment) {
                            foreach ($payment as $payment_field) {
                                if (array_key_exists("show_in_overview", $payment_field) && $payment_field["show_in_overview"]) {
                                    $fields[$payment_field["name"]] = $payment_field["label"];
                                }
                            }
                        }

                        if (!array_key_exists("overview_hide_delivery") || !CONFIG["general"]["overview_hide_delivery"]) {
                            $fields["delivery"] = TRANSLATION["delivery"];
                        }
                        foreach (CONFIG["fields"]["delivery"] as $delivery) {
                            foreach ($delivery as $delivery_field) {
                                if (array_key_exists("show_in_overview", $delivery_field) && $delivery_field["show_in_overview"]) {
                                    $fields[$delivery_field["name"]] = $delivery_field["label"];
                                }
                            }
                        }

                        foreach ($fields as $field) {
                            echo "<th scope=\"col\">" . $field . "</th>";
                        }

                    ?>
                </tr>
            </thead>
            <tbody>
                <?php

                    $orders = $con->query("SELECT * FROM " . DBPREFIX . "orders");
                    if ($orders->num_rows == 0) {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center"><i><?=TRANSLATION["no_elements"]?></i></td>
                        </tr>
                        <?php
                    } else {
                        while($row = $orders->fetch_assoc()) {
                            ?>
                            <tr class="row-checkbox" data-id="<?=$row["id"]?>">
                                <th class="text-center">
                                    <input type="radio" name="ord_lst_rdo" class="ckb-lst" data-id="<?=$row["id"]?>" id="ord_lst_<?=$row["id"]?>" value="<?=$row["id"]?>" />
                                    <label class="ckb-lst-lbl" for="ord_lst_<?=$row["id"]?>"></label>
                                </th>
                                <td><?=strtoupper($row["code"])?></td>
                                <td class="text-center ord-tbl-sta">

                                </td>
                                <td><?=money($row["value"])?></td>
                                <td><?=substr($row["comment"], 0, 60) . (strlen($row["comment"]) > 60 ? "" : "")?></td>
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

            function checkbox_toggle(id) {
                if (id == selec) {
                    $("#ord_lst_" + selec).prop("checked", false);
                    selec = 0;
                    $("#ord_btn_mrk").hide();
                    $("#ord_btn_det").prop("href", "");
                    $("#ord_btn_umr").show();
                } else {
                    selec = id;
                    link = linkurl + selec;
                    $("#ord_lst_" + selec).prop("checked", true);
                    $("#ord_btn_umr").hide();
                    $("#ord_btn_det").prop("href", link);
                    $("#ord_btn_mrk").show();
                }
            }

            function filter() {
                status = $("#ord_flt_sta").val()
                if (status == "all" || !$(".ord_tlb_flt").is(":visible")) {
                    $("#ord_tbl>tbody>tr").each(function() {
                        $(this).addClass("filter");
                    });
                } else {
                    var used = false;
                    if (status == "used") {
                        used = true;
                    }
                    $("#ord_tbl>tbody>tr").filter(function() {
                        return $(this).children(".ord-tbl-sta").children().first().data("used") == used;
                    }).addClass("filter");
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
                if ($("#ord_tbl>tbody>tr.filter").length == 0) {
                    $("#ord_tbl>tbody>tr").each(function() {
                        $(this).hide();
                    });
                    if ($("#search-noresults").val() == null) {
                        $("#ord_tbl>tbody").append("<tr id=\"search-noresults\"><td colspan=\"5\" class=\"text-center\"><i><?=TRANSLATION["no_elements"]?></i></td></tr>");
                    }
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
            });

            $(".ord-lst, .row-checkbox").click(function() {
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

            $("#ord_flt_sta").change(function() {
                filter();
            });

            $("#ord_src_txt").on("input", function() {
                filter();
            });
        </script>
    </div>
<?php } ?>
