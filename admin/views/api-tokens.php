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
                if (base64_decode($_POST["token"]) == md5("api_tokens_add" . $user["id"])) {
                    if (isset($_POST["frm_add_des"]) && strlen(trim($_POST["frm_add_des"])) >= 3) {
                        $con->query("INSERT INTO " . DBPREFIX . "api (id, timestamp, userid, description, token, secret) VALUES (NULL, CURRENT_TIMESTAMP, " . $user["id"] . ", '" . $con->real_escape_string($_POST["frm_add_des"]) . "', '" . strtoupper($con->real_escape_string(bin2hex(cst_random_bytes(8)))) . "', '')");
                        $_SESSION["alert"] = ["success", "added_successfully"];
                        header("Location: " . ADMIN . "/api-tokens/" . $con->insert_id);
                        exit;
                    } else {
                        alert("danger", TRANSLATION["alerts"]["missing_title"], TRANSLATION["alerts"]["missing_message"]);
                    }
                } else {
                    alert("danger", TRANSLATION["alerts"]["admin_error_title"], TRANSLATION["alerts"]["admin_error_message"]);
                }
            }

        ?>
        <form action="<?=ADMIN?>/api-tokens/add" method="POST" id="frm_add">
            <input type="hidden" name="token" value="<?=base64_encode(md5("api_tokens_add" . $user["id"]))?>" />
            <h1>
                <?=TRANSLATION["add"]?>
                <span class="d-block d-sm-inline-block">
                    <a class="text-muted dark-on-hover" onclick="if (document.getElementById('frm_add').reportValidity() == true) $('#frm_add').submit();" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["save_close"]?>" style="cursor: pointer;"><i class="ion-md-checkmark"></i></a>
                    <a class="text-muted dark-on-hover" href="<?=ADMIN?>/api-tokens" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
                </span>
            </h1>
            <table class="table table-hover shadow">
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["description"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["description"]?></p>
                        <textarea class="form-control" name="frm_add_des" rows="3" maxlength="65535" required minlength="3"><?=(isset($_POST["frm_add_des"]) ? $_POST["frm_add_des"] : "")?></textarea>
                    </td>
                </tr>
            </table>
        </form>
    <?php } elseif (sizeof($expl) == 3 || sizeof($expl) == 4) { ?>
        <?php
            $apitoken = $con->query("SELECT * FROM " . DBPREFIX . "api WHERE id = " . $con->real_escape_string($expl[2]));
            if ($apitoken->num_rows != 1) {
                header("Location: " . ADMIN . "/api-tokens");
                exit;
            }
            $apitoken = $apitoken->fetch_assoc();

        ?>
        <?php if (sizeof($expl) == 4 && $expl[3] == "revoke") { ?>
            <?php
                if (isset($_POST["token"])) {
                    if (base64_decode($_POST["token"]) == md5("api_tokens_revoke" . $apitoken["code"] . $apitoken["id"] . $apitoken["timestamp"])) {
                        $con->query("DELETE FROM " . DBPREFIX . "api WHERE id = " . $apitoken["id"]);
                        $_SESSION["alert"] = ["success", "revoked_successfully"];
                    } else {
                        $_SESSION["alert"] = ["danger", "admin_error"];
                    }
                    header("Location: " . ADMIN . "/api-tokens");
                    exit;
                }

            ?>
            <h1><?=TRANSLATION["revoke"]?></h1>
            <p class="lead"><?=sprintf(TRANSLATION["revoke_sure"], "<span class=\"d-inline-block\">\"<span class=\"badge badge-primary mb-0 text-monospace\">" . $apitoken["token"] . "</span>\"</span>")?></p>
            <form action="<?=ADMIN?>/api-tokens/<?=$apitoken["id"]?>/revoke" method="POST" class="btn-group float-right" role="group">
                <input type="hidden" name="token" value="<?=base64_encode(md5("api_tokens_revoke" . $apitoken["code"] . $apitoken["id"] . $apitoken["timestamp"]))?>" />
                <div class="btn-group float-right" role="group">
                    <button class="btn btn-danger shadow" type="submit"><i class="ion-md-close-circle-outline"></i> <?=TRANSLATION["revoke"]?></button>
                    <a class="btn btn-default" href="<?=ADMIN?>/api-tokens"><?=TRANSLATION["abort"]?></a>
                </div>
            </form>
        <?php } else { ?>
            <h1>
                <?=TRANSLATION["details"]?>
                <span class="d-block d-sm-inline-block">
                    <a href="<?=ADMIN?>/api-tokens/<?=$apitoken["id"]?>/revoke" class="text-muted red-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["revoke"]?>"><i class="ion-md-close-circle-outline"></i></a>
                    <a class="text-muted dark-on-hover" href="<?=ADMIN?>/api-tokens" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["close"]?>"><i class="ion-md-close"></i></a>
                </span>
            </h1>
            <table class="table table-hover shadow">
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["api_token"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["api_token"]?></p>
                        <pre class="badge badge-primary mb-0"><?=$apitoken["token"]?></pre>
                    </td>
                </tr>
                <?php if (empty(trim($apitoken["secret"]))) { ?>
                    <tr>
                        <th class="d-none d-sm-block"><?=TRANSLATION["api_tokens_setup_qr"]?></th>
                        <td>
                            <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["api_tokens_setup_qr"]?></p>
                            <img src="<?=qr($apitoken["token"], "H", 4)?>" class="img-fluid">
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["description"]?></th>
                    <td class="text-break">
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["description"]?></p>
                        <?=str_replace(["\r\n", "\r", "\n"], "<br />", $apitoken["description"])?>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["created_by_at"]?></th>
                    <td class="text-break">
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["created_by_at"]?></p>
                        <?=$con->query("SELECT * FROM " . DBPREFIX . "users WHERE id=" . $apitoken["userid"])->fetch_assoc()["name"]?> / <?=date(TRANSLATION["date_time_format"]["datetime_long"], strtotime($apitoken["timestamp"]))?>
                    </td>
                </tr>
                <tr>
                    <th class="d-none d-sm-block"><?=TRANSLATION["status"]?></th>
                    <td>
                        <p class="d-block d-sm-none text-bold text-uppercase small"><?=TRANSLATION["status"]?></p>
                        <?php
                            if (empty(trim($apitoken["secret"]))) {
                                ?>
                                    <i class="ion-md-square-outline text-danger"></i> <?=TRANSLATION["api_tokens_unused"]?>
                                <?php
                            } else {
                                ?>
                                    <i class="ion-md-square text-success"></i> <?=TRANSLATION["api_tokens_used"]?>
                                <?php
                            }
                        ?>
                    </td>
                </tr>
            </table>
        <?php } ?>
    <?php } else { ?>
        <?php
            header("Location: " . ADMIN . "/api-tokens");
            exit;
        ?>
    <?php } ?>
<?php } else { ?>
    <h1>
        <?=TRANSLATION["api_tokens"]?>
        <span class="d-block d-sm-inline-block">
            <span id="api_btn_umr">
                <a href="<?=ADMIN?>/api-tokens/add" class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["add"]?>" id="api_btn_add"><i class="ion-md-add"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["search"]?>" id="api_btn_src"><i class="ion-md-search"></i></a>
                <a class="text-muted dark-on-hover" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["filter"]?>" id="api_btn_flt"><i class="ion-md-funnel"></i></a>
            </span>
            <span id="api_btn_mrk" style="display: none;">
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["details"]?>" id="api_btn_det"><i class="ion-md-eye text-muted dark-on-hover"></i></a>
                <a data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["revoke"]?>" id="api_btn_rvk"><i class="ion-md-close-circle-outline text-muted red-on-hover"></i></a>
            </span>
        </span>
    </h1>
    <div class="d-flex flex-column flex-sm-row flex-wrap">
        <div class="form-group p-0 shadow col-12 col-sm-6 col-md-4 col-xl-2 mr-2" id="api_tlb_src" style="display: none;">
            <input type="text" class="form-control" placeholder="<?=TRANSLATION["search"]?>..." id="api_src_txt" />
        </div>
        <div class="form-group p-0 shadow mr-1 api_tlb_flt" style="display: none;">
            <select class="form-control" id="api_flt_sta">
                <option value="all" selected><?=TRANSLATION["status"]?>: <?=TRANSLATION["all"]?></option>
                <option value="used"><?=TRANSLATION["status"]?>: <?=TRANSLATION["api_tokens_used"]?></option>
                <option value="unused"><?=TRANSLATION["status"]?>: <?=TRANSLATION["api_tokens_unused"]?></option>
            </select>
        </div>
    </div>
    <div class="table-responsive shadow">
        <table class="table table-hover table-sm mb-4" id="api_tbl">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"><?=TRANSLATION["description"]?></th>
                </tr>
            </thead>
            <tbody>
                <?php

                    $apitokens = $con->query("SELECT * FROM " . DBPREFIX . "api");
                    if ($apitokens->num_rows == 0) {
                        ?>
                        <tr>
                            <td colspan="3" class="text-center"><i><?=TRANSLATION["no_elements"]?></i></td>
                        </tr>
                        <?php
                    } else {
                        while($row = $apitokens->fetch_assoc()) {
                            if (empty(trim($row["secret"]))) {
                                $row["status"] = "unused";
                            } else {
                                $row["status"] = "used";
                            }
                            ?>
                            <tr class="row-checkbox" data-id="<?=$row["id"]?>" data-status="<?=$row["status"]?>">
                                <th class="text-center">
                                    <input type="radio" name="api_lst_rdo" class="ckb-lst" data-id="<?=$row["id"]?>" id="api_lst_<?=$row["id"]?>" value="<?=$row["id"]?>" />
                                    <label class="ckb-lst-lbl" for="api_lst_<?=$row["id"]?>"></label>
                                </th>
                                <td class="text-centers">
                                    <span class="d-none"><?=$row["token"]?></span>
                                    <?php
                                        if ($row["status"] == "unused") {
                                            ?>
                                                <i class="ion-md-square-outline text-danger" id="api_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["api_tokens_unused"]?>"></i>
                                            <?php
                                        } else {
                                            ?>
                                                <i class="ion-md-square text-success" id="api_<?=$row["id"]?>_sta" data-toggle="tooltip" data-placement="bottom" title="<?=TRANSLATION["api_tokens_used"]?>"></i>
                                            <?php
                                        }
                                    ?>
                                </td>
                                <td class="text-centers"><?=substr($row["description"], 0, 60) . (strlen($row["description"]) > 60 ? "&hellip;" : "")?></td>
                            <?php
                        }
                    }

                ?>
            </tbody>
        </table>
        <script>
            var selec = 0;
            var linkurl = "<?=ADMIN?>/api-tokens/";

            function checkbox_toggle(id) {
                if (id == selec) {
                    $("#api_lst_" + selec).prop("checked", false);
                    selec = 0;
                    $("#api_btn_mrk").hide();
                    $("#api_btn_det").prop("href", "");
                    $("#api_btn_rvk").prop("href", "");
                    $("#api_btn_umr").show();
                } else {
                    selec = id;
                    $("#api_lst_" + selec).prop("checked", true);
                    $("#api_btn_umr").hide();
                    $("#api_btn_det").prop("href", linkurl + selec);
                    $("#api_btn_rvk").prop("href", linkurl + selec + "/revoke");
                    $("#api_btn_mrk").show();
                }
            }

            function filter() {
                status = $("#api_flt_sta").val();
                if (status == "all" || !$(".api_tlb_flt").is(":visible")) {
                    $("#api_tbl>tbody>tr").each(function() {
                        $(this).addClass("filter");
                    });
                }
                if ($(".api_tlb_flt").is(":visible")) {
                    if (status != "all") {
                        $("#api_tbl>tbody>tr").filter(function() {
                            return $(this).data("status") == status;
                        }).addClass("filter");
                    }
                }
                query = $("#api_src_txt").val();
                if (query.trim().length > 0 && $("#api_tlb_src").is(":visible")) {
                    $("#api_tbl>tbody>tr.filter").filter(function() {
                        if ($(this).text().toLowerCase().search(query.toLowerCase()) > -1) {
                            return true;
                        } else {
                            $(this).removeClass("filter");
                            return false;
                        }
                    }).addClass("filter");
                }
                if ($("#api_tbl>tbody>tr.filter:not(#search-noresults)").length == 0) {
                    $("#api_tbl>tbody>tr").each(function() {
                        $(this).hide();
                    });
                    if ($("#search-noresults").val() == null) {
                        $("#api_tbl>tbody").append("<tr id=\"search-noresults\"><td colspan=\"3\" class=\"text-center\"><i><?=TRANSLATION["no_elements"]?></i></td></tr>");
                    }
                    $("#search-noresults").show();
                } else {
                    $("#search-noresults").remove();
                    $("#api_tbl>tbody>tr").each(function() {
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
                if ($("input[name=api_lst_rdo]:checked").val() != null) {
                    checkbox_toggle($("input[name=api_lst_rdo]:checked").val());
                }
            });

            $(".ckb-lst, .row-checkbox").click(function() {
                checkbox_toggle($(this).data("id"))
            });

            $("#api_btn_src").click(function() {
                $("#api_tlb_src").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#api_btn_flt").click(function() {
                $(".api_tlb_flt").fadeToggle("fast", function() {
                    filter();
                });
            });

            $("#api_flt_sta").change(function() {
                filter();
            });

            $("#api_src_txt").on("input", function() {
                filter();
            });
        </script>
    </div>
<?php } ?>
