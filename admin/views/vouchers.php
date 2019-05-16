<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

?>
<?php if (sizeof($expl) >= 3) { ?>
    <?php if ($expl[2] == "add") { ?>
        <?php

            if (isset($_POST["token"])) {
                if (base64_decode($_POST["token"]) == md5("vochers_add" . $user["id"])) {
                    if ($con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE code = '" . $con->real_escape_string($_POST["frm_add_cde"]) . "'")->num_rows >= 1) {
                        alert("danger", TRANSLATION["alerts"]["voucher_code_duplicate_title"], TRANSLATION["alerts"]["voucher_code_duplicate_message"]);
                    } elseif (strlen($_POST["frm_add_cde"]) != 15 || !is_numeric($_POST["frm_add_val"])) {
                        alert("danger", TRANSLATION["alerts"]["missing_title"], TRANSLATION["alerts"]["missing_message"]);
                    } else {
                        $con->query("INSERT INTO " . DBPREFIX . "vouchers (id, timestamp, value, code, order_id, comment, userid) VALUES (NULL, CURRENT_TIMESTAMP, '" . $con->real_escape_string($_POST["frm_add_val"]) . "', '" . strtoupper($con->real_escape_string($_POST["frm_add_cde"])) . "', 0, '" . strip_tags($con->real_escape_string($_POST["frm_add_cmt"])) . "', '" . $user["id"] . "')");
                        $_SESSION["alert"] = ["success", "added_successfully"];
                        header("Location: " . ADMIN . "/vouchers/" . $con->insert_id);
                        exit;
                    }
                } else {
                    alert("danger", TRANSLATION["alerts"]["admin_error_title"], TRANSLATION["alerts"]["admin_error_message"]);
                }
            }

        ?>
        <form action="<?=ADMIN?>/vouchers/add" method="POST" id="frm_add">
            <input type="hidden" name="token" value="<?=base64_encode(md5("vochers_add" . $user["id"]))?>" />
            <h1>
                <?=TRANSLATION["add"]?>
                <span class="d-block d-sm-inline-block">
                    <a class="text-muted dark-on-hover" onclick="if (document.getElementById('frm_add').reportValidity() == true) $('#frm_add').submit();" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["save_close"]?>" style="cursor: pointer;"><i class="ion-md-checkmark"></i></a>
                    <a class="text-muted dark-on-hover" href="<?=ADMIN?>/vouchers" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
                </span>
            </h1>
            <table class="table table-hover shadow">
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["voucher_code"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_code"]?></p>
                        <div class="input-group mb-3">
                            <input type="text" name="frm_add_cde" id="frm_add_cde" placeholder="XXX-XXX-XXX-XXX" class="form-control text-uppercase text-monospace" maxlength="15" minlength="15" required <?=(isset($_POST["frm_add_cde"]) ? "value=\"" . $_POST["frm_add_cde"] . "\"" : "")?> aria-describedby="btn_add_gen" />
                            <div class="input-group-append d-none d-sm-flex">
                                <button class="btn btn-primary btn-sm" type="button" id="btn_add_gen"><?=TRANSLATION["generate"]?></button>
                            </div>
                            <button class="d-block d-sm-none btn btn-primary btn-sm btn-block" type="button" id="btn_add_gem"><?=TRANSLATION["generate"]?></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["voucher_value"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_value"]?></p>
                        <div class="form-group">
                            <div class="form-group">
                                <div class="input-group">
                                    <?php if (!empty(trim(TRANSLATION["money_format"]["prefix"]))) { ?>
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><?=TRANSLATION["money_format"]["prefix"]?></span>
                                        </div>
                                    <?php } ?>
                                    <input type="number" step="0.<?=str_repeat("0", TRANSLATION["money_format"]["decimals"] - 1)?>1" class="form-control" name="frm_add_val" required <?=(isset($_POST["frm_add_val"]) ? "value=\"" . $_POST["frm_add_val"] . "\"" : "")?> />
                                    <?php if (!empty(trim(TRANSLATION["money_format"]["suffix"]))) { ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text"><?=TRANSLATION["money_format"]["suffix"]?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["comment"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["comment"]?></p>
                        <textarea class="form-control" name="frm_add_cmt" rows="3" maxlength="65535"><?=(isset($_POST["frm_add_cmt"]) ? $_POST["frm_add_cmt"] : "")?></textarea>
                    </td>
                </tr>
            </table>
        </form>
        <script>
            $("#btn_add_gen, #btn_add_gem").click(function() {
                chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                generated = "";
                for(var length = 0; length < 15; ++length) {
                    if (length == 3 || length == 7 || length == 11) {
                        generated += "-";
                    } else {
                        generated += chars.charAt(Math.floor(Math.random() * chars.length));;
                    }
                }
                $("#frm_add_cde").val(generated);
            });
        </script>
    <?php } else { ?>
        <?php
            $voucher = $con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE id = " . $con->real_escape_string($expl[2]));
            if ($voucher->num_rows != 1) {
                header("Location: " . ADMIN . "/vouchers");
                exit;
            }
            $voucher = $voucher->fetch_assoc();
        ?>
        <?php if (sizeof($expl) > 3) { ?>
            <?php if ($expl[3] == "edit") { ?>
                <?php

                    if (isset($_POST["token"])) {
                        $redirect = true;
                        if (base64_decode($_POST["token"]) == md5($voucher["timestamp"] . $voucher["code"] . $voucher["id"])) {
                            if ($voucher["order_id"] == 0) {
                                if ($con->query("SELECT * FROM " . DBPREFIX . "vouchers WHERE code = '" . $con->real_escape_string($_POST["frm_edt_cde"]) . "' AND id != " . $voucher["id"])->num_rows >= 1) {
                                    alert("danger", TRANSLATION["alerts"]["voucher_code_duplicate_title"], TRANSLATION["alerts"]["voucher_code_duplicate_message"]);
                                    $redirect = false;
                                } elseif (strlen($_POST["frm_edt_cde"]) != 15 || !is_numeric($_POST["frm_edt_val"])) {
                                    alert("danger", TRANSLATION["alerts"]["missing_title"], TRANSLATION["alerts"]["missing_message"]);
                                    $redirect = false;
                                } else {
                                    $con->query("UPDATE " . DBPREFIX . "vouchers SET code = '" . strtoupper($con->real_escape_string($_POST["frm_edt_cde"])) . "', value = '" . $con->real_escape_string($_POST["frm_edt_val"]) . "', comment = '" . strip_tags($con->real_escape_string($_POST["frm_edt_cmt"])) . "' WHERE id = " . $voucher["id"]);
                                }
                            } else {
                                $con->query("UPDATE " . DBPREFIX . "vouchers SET comment = '" . strip_tags($con->real_escape_string($_POST["frm_edt_cmt"])) . "' WHERE id = " . $voucher["id"]);
                            }
                            $_SESSION["alert"] = ["success", "saved_successfully"];
                        } else {
                            $_SESSION["alert"] = ["danger", "admin_error"];
                        }
                        if ($redirect) {
                            header("Location: " . ADMIN . "/vouchers/" . $expl[3]);
                            exit;
                        }
                    }

                ?>
                <form action="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>/edit" method="POST" id="frm_edt">
                    <input type="hidden" name="token" value="<?=base64_encode(md5($voucher["timestamp"] . $voucher["code"] . $voucher["id"]))?>" />
                    <h1>
                        <?=TRANSLATION["edit"]?>
                        <span class="d-block d-sm-inline-block">
                            <a class="text-muted dark-on-hover" onclick="if (document.getElementById('frm_edt').reportValidity() == true) $('#frm_edt').submit();" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["save_close"]?>" style="cursor: pointer;"><i class="ion-md-checkmark"></i></a>
                            <a class="text-muted dark-on-hover" href="<?=ADMIN?>/vouchers" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
                        </span>
                    </h1>
                        <?php if ($voucher["order_id"] == 0) { ?>
                        <table class="table table-hover shadow">
                            <tr>
                                <th class="d-none d-sm-block"><?=TRANSLATION["voucher_code"]?></th>
                                <td>
                                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_code"]?></p>
                                    <div class="input-group mb-3">
                                        <input type="text" name="frm_edt_cde" id="frm_edt_cde" value="<?=(isset($_POST["frm_edt_cde"]) ? $_POST["frm_edt_cde"] : $voucher["code"])?>" placeholder="XXX-XXX-XXX-XXX" class="form-control text-uppercase text-monospace" maxlength="15" minlength="15" required aria-describedby="btn_edt_gen" />
                                        <div class="input-group-append d-none d-sm-flex">
                                            <button class="btn btn-primary btn-sm" type="button" id="btn_edt_gen"><?=TRANSLATION["generate"]?></button>
                                        </div>
                                        <button class="d-block d-sm-none btn btn-primary btn-sm btn-block" type="button" id="btn_edt_gem"><?=TRANSLATION["generate"]?></button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th class="d-none d-sm-block"><?=TRANSLATION["voucher_value"]?></th>
                                <td>
                                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_value"]?></p>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <?php if (!empty(trim(TRANSLATION["money_format"]["prefix"]))) { ?>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><?=TRANSLATION["money_format"]["prefix"]?></span>
                                                    </div>
                                                <?php } ?>
                                                <input type="number" step="0.<?=str_repeat("0", TRANSLATION["money_format"]["decimals"] - 1)?>1" class="form-control" name="frm_edt_val" value="<?=number_format((isset($_POST["frm_edt_val"]) && is_numeric($_POST["frm_edt_val"]) ? $_POST["frm_edt_val"] : $voucher["value"]), TRANSLATION["money_format"]["decimals"], ".", "")?>" required />
                                                <?php if (!empty(trim(TRANSLATION["money_format"]["suffix"]))) { ?>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"><?=TRANSLATION["money_format"]["suffix"]?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } else { ?>
                        <div class="alert alert-dismissible alert-info shadow">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4 class="alert-heading"><?=TRANSLATION["hint"]?></h4>
                            <p class="mb-0"><?=TRANSLATION["voucher_no_editing"]?></p>
                        </div>
                        <table class="table table-hover shadow">
                            <tr>
                                <th class="d-none d-sm-block"><?=TRANSLATION["voucher_code"]?></th>
                                <td>
                                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_code"]?></p>
                                    <pre class="badge badge-primary mb-0"><?=$voucher["code"]?></pre>
                                </td>
                            </tr>
                            <tr>
                                <th class="d-none d-sm-block"><?=TRANSLATION["voucher_value"]?></th>
                                <td>
                                    <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_value"]?></p>
                                    <?=money($voucher["value"])?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <th class="d-none d-sm-block"><?=TRANSLATION["comment"]?></th>
                            <td>
                                <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["comment"]?></p>
                                <textarea class="form-control" name="frm_edt_cmt" rows="3" maxlength="65535"><?=(isset($_POST["frm_edt_cmt"]) ? $_POST["frm_edt_cmt"] : $voucher["comment"])?></textarea>
                            </td>
                        </tr>
                    </table>
                </form>
                <?php if ($voucher["order_id"] == 0) { ?>
                    <script>
                        $("#btn_edt_gen, #btn_edt_gem").click(function() {
                            chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
                            generated = "";
                            for(var length = 0; length < 15; ++length) {
                                if (length == 3 || length == 7 || length == 11) {
                                    generated += "-";
                                } else {
                                    generated += chars.charAt(Math.floor(Math.random() * chars.length));;
                                }
                            }
                            $("#frm_edt_cde").val(generated);
                        });
                    </script>
                <?php } ?>
            <?php } elseif ($expl[3] == "print") { ?>
                    <style>
                        @page {
                          size: A4 landscape;
                          -webkit-print-color-adjust: exact !important;
                          color-adjust: exact !important;
                        }
                    </style>
                    <script>
                        $(document).ready(function () {
                            window.print();
                        });
                    </script>
                </head>
                <body>
                    <div class="col-12 p-0 m-0 position-absolute container row" style="bottom: 0; top: 0; left: 0; right: 0;">
                        <div class="col-8 p-3 border-0 d-flex flex-column justify-content-between">
                            <div>
                                <h1 class="display-3"><?=TRANSLATION["voucher"]?></h1>
                                <h2><?=CONFIG["general"]["title"]?></h2>
                                <h5><?=LINKURL?></h5>
                            </div>
                            <div class="mb-2 text-break">
                                <h3><?=str_replace(["\r\n", "\r", "\n"], "<br />", $voucher["comment"])?></h3>
                            </div>
                        </div>
                        <div class="p-2 col-4 text-light" style="background-color: #1a1a1a; border: 1px solid #1a1a1a;">
                            <h3 class="text-reset"><?=TRANSLATION["voucher_value"]?></h3>
                            <h5 class="text-reset text-right"><?=money($voucher["value"])?></h5>
                            <br />
                            <h3 class="text-reset"><?=TRANSLATION["voucher_code"]?></h3>
                            <h5 class="text-reset text-right"><?=$voucher["code"]?></h5>
                            <div class="position-absolute" style="bottom: 0;right: 0.5rem; left: 0.5rem;">
                                <h6 class="text-reset"><?=TRANSLATION["voucher_hint"]?></h6>
                                <p class="text-reset text-justify small"><?=patternmatch(TRANSLATION["voucher_terms"])?></p>
                            </div>
                        </div>
                    </div>
                </body>
            <?php } elseif ($expl[3] == "delete") { ?>
                <?php

                    if($voucher["order_id"] != 0) {
                        $_SESSION["alert"] = ["danger", [TRANSLATION["alerts"]["admin_error_title"], TRANSLATION["voucher_no_editing"]]];
                        header("Location: " . ADMIN . "/vouchers");
                        exit;
                    }

                    if (isset($_POST["token"])) {
                        if (base64_decode($_POST["token"]) == md5($voucher["code"] . $voucher["id"] . $voucher["timestamp"])) {
                            $con->query("DELETE FROM " . DBPREFIX . "vouchers WHERE id = " . $voucher["id"]);
                            $_SESSION["alert"] = ["success", "deleted_successfully"];
                        } else {
                            $_SESSION["alert"] = ["danger", "admin_error"];
                        }
                        header("Location: " . ADMIN . "/vouchers");
                        exit;
                    }

                ?>
                <h1><?=TRANSLATION["delete"]?></h1>
                <p class="lead"><?=sprintf(TRANSLATION["delete_sure"], "<span class=\"d-inline-block\">\"<span class=\"badge badge-primary mb-0 text-monospace\">" . $voucher["code"] . "</span>\"</span>")?></p>
                <form action="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>/delete" method="POST" class="btn-group float-right" role="group">
                    <input type="hidden" name="token" value="<?=base64_encode(md5($voucher["code"] . $voucher["id"] . $voucher["timestamp"]))?>" />
                    <div class="btn-group float-right" role="group">
                        <button class="btn btn-danger shadow" type="submit"><i class="ion-md-trash"></i> <?=TRANSLATION["delete"]?></button>
                        <a class="btn btn-default" href="<?=ADMIN?>/vouchers"><?=TRANSLATION["abort"]?></a>
                    </div>
                </form>
            <?php } else { ?>
                <?php
                    header("Location: " . ADMIN . "/vouchers");
                    exit;
                ?>
            <?php } ?>
        <?php } else { ?>
            <h1>
                <?=TRANSLATION["details"]?>
                <span class="d-block d-sm-inline-block">
                    <a href="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>/edit" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["edit"]?>"><i class="ion-md-create"></i></a>
                    <a href="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>/print" target="_blank" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["voucher_print"]?>"><i class="ion-md-gift"></i></a>
                    <?php if ($voucher["order_id"] == 0) { ?>
                        <a href="<?=ADMIN?>/vouchers/<?=$voucher["id"]?>/delete" class="text-muted red-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["delete"]?>"><i class="ion-md-trash"></i></a>
                    <?php } else { ?>
                        <span class="text-muted icon-disabled" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["voucher_no_editing"]?>"><i class="ion-md-trash"></i></span>
                    <?php } ?>
                    <a class="text-muted dark-on-hover" href="<?=ADMIN?>/vouchers" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
                </span>
            </h1>
            <table class="table table-hover shadow">
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["voucher_code"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_code"]?></p>
                        <pre class="badge badge-primary mb-0"><?=$voucher["code"]?></pre>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["voucher_value"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["voucher_value"]?></p>
                        <?=money($voucher["value"])?>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["comment"]?></th>
                    <td class="text-break">
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["comment"]?></p>
                        <?=str_replace(["\r\n", "\r", "\n"], "<br />", $voucher["comment"])?>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["created_by_at"]?></th>
                    <td class="text-break">
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["created_by_at"]?></p>
                        <?=$con->query("SELECT * FROM " . DBPREFIX . "users WHERE id=" . $voucher["userid"])->fetch_assoc()["name"]?> / <?=date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($voucher["timestamp"]))?>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["status"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["status"]?></p>
                        <?php
                            if ($voucher["order_id"] == 0) {
                                ?>
                                    <i class="ion-md-square text-success"></i> <?=TRANSLATION["voucher_unused"]?>
                                <?php
                            } else {
                                $row_order = $con->query("SELECT timestamp FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($voucher["order_id"]))->fetch_assoc();
                                ?>
                                    <i class="ion-md-square-outline text-danger"></i> <?=sprintf(TRANSLATION["voucher_used_at"], date(TRANSLATION["date_time_format"]["datetime"], strtotime($row_order["timestamp"])))?><br />
                                    <a href="<?=ADMIN?>/orders/<?=$voucher["order_id"]?>"><?=TRANSLATION["goto_order"]?></a>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
            </table>
        <?php } ?>
    <?php } ?>
<?php } else { ?>
    <h1>
        <?=TRANSLATION["vouchers"]?>
        <span class="d-block d-sm-inline-block">
            <span id="vou_btn_umr">
                <a href="<?=ADMIN?>/vouchers/add" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["add"]?>" id="vou_btn_add"><i class="ion-md-add"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["search"]?>" id="vou_btn_src"><i class="ion-md-search"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["filter"]?>" id="vou_btn_flt"><i class="ion-md-funnel"></i></a>
            </span>
            <span id="vou_btn_mrk" style="display: none;">
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["details"]?>" id="vou_btn_det"><i class="ion-md-eye text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["edit"]?>" id="vou_btn_edt"><i class="ion-md-create text-muted dark-on-hover"></i></a>
                <a target="_blank" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["voucher_print"]?>" id="vou_btn_prt"><i class="ion-md-gift text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["delete"]?>" style="display: none;" id="vou_btn_dea"><i class="ion-md-trash text-muted red-on-hover"></i></a>
                <span data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["voucher_no_editing"]?>" style="display: none;" id="vou_btn_ded"><i class="ion-md-trash text-muted icon-disabled"></i></span>
            </span>
        </span>
    </h1>
    <div class="d-flex flex-column flex-sm-row flex-wrap">
        <div class="form-group p-0 shadow col-12 col-sm-6 col-md-4 col-xl-2 mr-2" id="vou_tlb_src" style="display: none;">
            <input type="text" class="form-control" placeholder="<?=TRANSLATION["search"]?>..." id="vou_src_txt" />
        </div>
        <div class="form-group p-0 shadow mr-1 vou_tlb_flt" style="display: none;">
            <select class="form-control" id="vou_flt_sta">
                <option value="all" selected><?=TRANSLATION["status"]?>: <?=TRANSLATION["all"]?></option>
                <option value="unused"><?=TRANSLATION["status"]?>: <?=TRANSLATION["voucher_unused"]?></option>
                <option value="used"><?=TRANSLATION["status"]?>: <?=TRANSLATION["voucher_used"]?></option>
            </select>
        </div>
    </div>
    <div class="table-responsive shadow">
        <table class="table table-hover table-sm mb-4" id="vou_tbl">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"><?=TRANSLATION["voucher_code"]?></th>
                    <th scope="col"></th>
                    <th scope="col"><?=TRANSLATION["voucher_value"]?></th>
                    <th scope="col"><?=TRANSLATION["comment"]?></th>
                </tr>
            </thead>
            <tbody>
                <?php

                    $vouchers = $con->query("SELECT * FROM " . DBPREFIX . "vouchers");
                    if ($vouchers->num_rows == 0) {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center"><i><?=TRANSLATION["no_elements"]?></i></td>
                        </tr>
                        <?php
                    } else {
                        while($row = $vouchers->fetch_assoc()) {
                            ?>
                            <tr class="row-checkbox" data-id="<?=$row["id"]?>">
                                <th class="text-center">
                                    <input type="radio" name="vou_lst_rdo" class="ckb-lst" data-id="<?=$row["id"]?>" id="vou_lst_<?=$row["id"]?>" value="<?=$row["id"]?>" />
                                    <label class="ckb-lst-lbl" for="vou_lst_<?=$row["id"]?>"></label>
                                </th>
                                <td><pre class="badge badge-primary mb-0"><?=strtoupper($row["code"])?></pre></td>
                                <td class="text-center vou-tbl-sta">
                                    <?php
                                        if ($row["order_id"] == 0) {
                                            ?>
                                                <i class="ion-md-square text-success" id="vou_<?=$row["id"]?>_sta" data-used="false" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["voucher_unused"]?>"></i>
                                            <?php
                                        } else {
                                            $row_order = $con->query("SELECT timestamp FROM " . DBPREFIX . "orders WHERE id = " . $con->real_escape_string($row["order_id"]))->fetch_assoc();
                                            ?>
                                                <i class="ion-md-square-outline text-danger" id="vou_<?=$row["id"]?>_sta" data-used="true" data-toggle="tooltip" data-placement="bottom" title="<?=sprintf(TRANSLATION["voucher_used_at"], date(TRANSLATION["date_time_format"]["datetime"], strtotime($row_order["timestamp"])))?>"></i>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td><?=money($row["value"])?></td>
                                <td><?=substr($row["comment"], 0, 60) . (strlen($row["comment"]) > 60 ? "&hellip;" : "")?></td>
                            </tr>
                            <?php
                        }
                    }

                ?>
            </tbody>
        </table>
        <script>
            var selec = 0;
            var linkurl = "<?=ADMIN?>/vouchers/";

            function checkbox_toggle(id) {
                if (id == selec) {
                    $("#vou_lst_" + selec).prop("checked", false);
                    selec = 0;
                    $("#vou_btn_mrk").hide();
                    $("#vou_btn_det").prop("href", "");
                    $("#vou_btn_edt").prop("href", "");
                    $("#vou_btn_prt").prop("href", "");
                    $("#vou_btn_umr").show();
                } else {
                    selec = id;
                    link = linkurl + selec;
                    $("#vou_lst_" + selec).prop("checked", true);
                    $("#vou_btn_umr").hide();
                    $("#vou_btn_det").prop("href", link);
                    $("#vou_btn_edt").prop("href", link + "/edit");
                    $("#vou_btn_prt").prop("href", link + "/print");
                    if ($("#vou_" + selec + "_sta").data("used")) {
                        $("#vou_btn_dea").hide();
                        $("#vou_btn_ded").show();
                    } else {
                        $("#vou_btn_ded").hide();
                        $("#vou_btn_dea").prop("href", link + "/delete");
                        $("#vou_btn_dea").show();
                    }
                    $("#vou_btn_mrk").show();
                }
            }

            function filter() {
                status = $("#vou_flt_sta").val();
                if (status == "all" || !$(".vou_tlb_flt").is(":visible")) {
                    $("#vou_tbl>tbody>tr").each(function() {
                        $(this).addClass("filter");
                    });
                } else {
                    var used = false;
                    if (status == "used") {
                        used = true;
                    }
                    $("#vou_tbl>tbody>tr").filter(function() {
                        return $(this).children(".vou-tbl-sta").children().first().data("used") == used;
                    }).addClass("filter");
                }
                query = $("#vou_src_txt").val();
                if (query.trim().length > 0 && $("#vou_tlb_src").is(":visible")) {
                    $("#vou_tbl>tbody>tr.filter").filter(function() {
                        if ($(this).text().toLowerCase().search(query.toLowerCase()) > -1) {
                            return true;
                        } else {
                            $(this).removeClass("filter");
                            return false;
                        }
                    }).addClass("filter");
                }
                if ($("#vou_tbl>tbody>tr.filter:not(#search-noresults)").length == 0) {
                    console.log("Keine Ergebnisse");
                    $("#vou_tbl>tbody>tr").each(function() {
                        $(this).hide();
                    });
                    if ($("#search-noresults").val() == null) {
                        $("#vou_tbl>tbody").append("<tr id=\"search-noresults\"><td colspan=\"5\" class=\"text-center\"><i><?=TRANSLATION["no_elements"]?></i></td></tr>");
                    }
                    $("#search-noresults").show();
                } else {
                    $("#search-noresults").remove();
                    $("#vou_tbl>tbody>tr").each(function() {
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
                if ($("input[name=vou_lst_rdo]:checked").val() != null) {
                    checkbox_toggle($("input[name=vou_lst_rdo]:checked").val());
                }
            });

            $(".ckb-lst, .row-checkbox").click(function() {
                checkbox_toggle($(this).data("id"))
            });

            $("#vou_btn_src").click(function() {
                $("#vou_tlb_src").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#vou_btn_flt").click(function() {
                $(".vou_tlb_flt").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#vou_flt_sta").change(function() {
                filter();
            });

            $("#vou_src_txt").on("input", function() {
                filter();
            });
        </script>
    </div>
<?php } ?>
