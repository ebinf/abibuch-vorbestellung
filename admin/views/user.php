<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) >= 3) {
        if ($expl[2] == "change-password") {
            require("./views/change-password.php");
        } elseif ($expl[2] == "two-factor-authentication") {
            require("./views/two-factor-authentication.php");
        } else {
            header("Location: " . ADMIN . "/user");
        }
        exit;
    }

?>
<h1><?=TRANSLATION["account"]?></h1>
<?php

    $query = $con->query("SELECT totp, 2fa_backup FROM " . DBPREFIX . "users WHERE id = " . $con->real_escape_string($_SESSION["login_id"]))->fetch_assoc();
    if(empty(trim($query["totp"]))) {
        ?>
            <div class="d-flex flex-column flex-sm-row align-items-stretch mb-3 shadow-sm">
                <div class="card border-danger flex-fill">
                    <div class="card-body text-danger">
                        <h6 class="card-title mb-0"><?=TRANSLATION["2fa_inactive"]?></h6>
                    </div>
                </div>
                <form action="<?=ADMIN?>/user/two-factor-authentication" method="GET">
                    <input type="submit" class="btn btn-danger btn-block h-100" value="<?=TRANSLATION["2fa_activate"]?>" />
                </form>
            </div>
        <?php
    } else {
        $backup_codes = json_decode($query["2fa_backup"]);
        require_once(RESDIRABS . "/res/otphp/2fa.php");
        ?>
            <div class="d-flex flex-column flex-sm-row align-items-stretch mb-3 shadow-sm">
                <div class="card border-success flex-fill">
                    <div class="card-body text-success">
                        <h6 class="card-title mb-0"><?=TRANSLATION["2fa_active"]?></h6>
                    </div>
                </div>
                <form action="<?=ADMIN?>/user/two-factor-authentication" method="POST">
                    <input type="hidden" name="token" value="<?=base64_encode(md5("2fa_disable" . $user["id"] . $user["username"]))?>" />
                    <input type="submit" class="btn btn-success btn-block h-100" value="<?=TRANSLATION["2fa_deactivate"]?>" />
                </form>
            </div>
            <div class="d-flex flex-column flex-sm-row align-items-stretch mb-3 shadow-sm">
                <div class="card border-<?=(sizeof($backup_codes) <= 1 ? "danger" : "success")?> flex-fill">
                    <div class="card-body text-<?=(sizeof($backup_codes) <= 1 ? "danger" : "success")?>">
                        <h6 class="card-title mb-0"><?=sprintf(TRANSLATION["2fa_backupcodes_count"], sizeof($backup_codes), TFA_BACKUPCODE_QUANTITY)?></h6>
                    </div>
                </div>
                <form action="<?=ADMIN?>/user/two-factor-authentication/backupcodes" method="POST">
                    <input type="hidden" name="token" value="<?=base64_encode(md5("2fa_backupcodes_renew" . $user["id"] . $user["username"]))?>" />
                    <input type="submit" class="btn btn-<?=(sizeof($backup_codes) <= 1 ? "danger" : "success")?> btn-block h-100" value="<?=TRANSLATION["2fa_backupcodes_renew"]?>" />
                </form>
            </div>
        <?php
    }
?>
<div class="d-flex flex-column flex-sm-row align-items-stretch mb-3 shadow-sm">
    <div class="card border-primary flex-fill">
        <div class="card-body text-primary">
            <h6 class="card-title mb-0"><?=TRANSLATION["change_password"]?>.</h6>
        </div>
    </div>
    <form action="<?=ADMIN?>/user/change-password" method="GET">
        <input type="submit" class="btn btn-primary btn-block h-100" value="<?=TRANSLATION["change_password"]?>" />
    </form>
</div>
<div class="d-flex flex-column flex-sm-row align-items-stretch mb-3 shadow-sm">
    <div class="card border-primary flex-fill">
        <div class="card-body text-primary">
            <h6 class="card-title mb-0">Von allen Geräten abmelden.</h6>
        </div>
    </div>
    <form action="<?=ADMIN?>/logout/all-sessions" method="POST">
        <input type="hidden" name="token" value="<?=base64_encode(md5("lgo_all_sessions" . $user["id"] . $user["username"]))?>" />
        <input type="submit" class="btn btn-primary btn-block h-100" value="<?=TRANSLATION["logout"]?>" />
    </form>
</div>
