<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (isset($_GET["clear"])) {
        unset($_SESSION["2fa_verified"]);
        header("Location: " . ADMIN . "/user");
        exit;
    }

    if (sizeof($expl) > 3 && !(sizeof($expl) == 4 && $expl[3] == "backupcodes")) {
        header("Location: " . ADMIN . "/" . $menu);
        exit;
    }

    require_once(RESDIRABS . "/res/otphp/2fa.php");

    use ParagonIE\ConstantTime\Base32;
    use OTPHP\TOTP;
    use OTPHP\Factory;
?>
<h1><?=TRANSLATION["2fa"]?></h1>
<?php

    $totp_check = $con->query("SELECT totp FROM " . DBPREFIX . "users WHERE id = " . $con->real_escape_string($_SESSION["login_id"]))->fetch_assoc()["totp"];
    if(!empty(trim($totp_check))) {
        if (sizeof($expl) == 4 && $expl[3] == "backupcodes") {
            if (isset($_SESSION["2fa_backupcodes_create"])) {
                unset($_SESSION["2fa_backupcodes_create"]);
                ?>
                    <p class="lead"><?=TRANSLATION["2fa_backupcodes_lead"]?></p>
                    <ul class="list-unstyled text-center text-monospace" id="tfa_bck_cnt">
                        <?php
                            $backupcodes = [];
                            for($cnt = 0; $cnt < TFA_BACKUPCODE_QUANTITY; $cnt++) {
                                $backupcode = strtoupper(wordwrap(bin2hex(cst_random_bytes(TFA_BACKUPCODE_LENGTH / 2)), TFA_BACKUPCODE_SEPARATOR, "-", true));
                                $backupcodes[] = password_hash($backupcode, PASSWORD_DEFAULT);
                                echo "<li>" . $backupcode . "</li>";
                            }
                            $con->query("UPDATE " . DBPREFIX . "users SET 2fa_backup = '" . $con->real_escape_string(json_encode($backupcodes)) . "' WHERE id = " . $user["id"]);
                        ?>
                    </ul>
                    <div class="btn-group float-right" role="group">
                        <a href="<?=ADMIN?>/user" class="btn btn-primary shadow" role="button"><?=TRANSLATION["done"]?></a>
                    </div>
                    <script>
                        $("#tfa_bck_cnt").mouseup(function() {
                            window.getSelection().removeAllRanges();
                            range = document.createRange();
                            range.selectNodeContents($(this)[0]);
                            window.getSelection().addRange(range);
                        });
                    </script>
                <?php
            } elseif (isset($_POST["token"]) && base64_decode($_POST["token"]) == md5("2fa_backupcodes_renew" . $user["id"] . $user["username"])) {
                $_SESSION["2fa_backupcodes_create"] = true;
                header("Location: " . ADMIN . "/user/two-factor-authentication/backupcodes");
                exit;
            } else {
                $_SESSION["alert"] = ["danger", "admin_error"];
                header("Location: " . ADMIN . "/user");
                exit;
            }
        } elseif (isset($_POST["token"]) || isset($_POST["verifier"])) {
            if (isset($_POST["token"]) && base64_decode($_POST["token"]) == md5("2fa_disable" . $user["id"] . $user["username"])) {
                ?>
                    <p class="lead"><?=TRANSLATION["2fa_deactivate_sure"]?></p>
                    <form action="<?=ADMIN?>/user/two-factor-authentication" method="POST" class="btn-group float-right" role="group">
                        <input type="hidden" name="verifier" value="<?=base64_encode(md5("2fa_disable_verify" . $totp_check))?>" />
                        <div class="btn-group float-right" role="group">
                            <input type="submit" class="btn btn-danger shadow" value="<?=TRANSLATION["2fa_deactivate"]?>" />
                            <a href="<?=ADMIN?>/user/two-factor-authentication?clear" class="btn btn-default"><?=TRANSLATION["abort"]?></a>
                        </div>
                    </form>
                <?php
            } elseif (isset($_POST["verifier"]) && base64_decode($_POST["verifier"]) == md5("2fa_disable_verify" . $totp_check)) {
                $con->query("UPDATE " . DBPREFIX . "users SET totp = '', 2fa_backup = '' WHERE id = " . $user["id"]);
                $_SESSION["alert"] = ["success", "2fa_disabled_success"];
                header("Location: " . ADMIN . "/user");
                exit;
            } else {
                $_SESSION["alert"] = ["danger", "admin_error"];
                header("Location: " . ADMIN . "/user");
                exit;
            }
        } else {
            header("Location: " . ADMIN . "/user");
            exit;
        }
    } else {
        if (isset($_SESSION["2fa_provisioning_url"])) {
            $otp = Factory::loadFromProvisioningUri($_SESSION["2fa_provisioning_url"]);
            if (isset($_SESSION["2fa_verified"]) && isset($_POST["2fa_vrf_pss"]) && !empty(trim($_POST["2fa_vrf_pss"]))) {
                $query = $con->query("SELECT password FROM " . DBPREFIX . "users WHERE id = " . $user["id"])->fetch_assoc();
                if (password_verify($_POST["2fa_vrf_pss"], $query["password"])) {
                    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(TFA_CIPHER));
                    $encrypted = openssl_encrypt($otp->getProvisioningUri(), TFA_CIPHER, hash("sha256", $_POST["2fa_vrf_pss"]), 0, $iv);
                    $encrypted_base64 = base64_encode($iv . $encrypted);
                    $con->query("UPDATE " . DBPREFIX . "users SET totp = '" . $con->real_escape_string($encrypted_base64) . "' WHERE id = " . $user["id"]);
                    unset($_SESSION["2fa_verified"]);
                    unset($_SESSION["2fa_provisioning_url"]);
                    $_SESSION["alert"] = ["success", "2fa_setup_success"];
                    $_SESSION["2fa_backupcodes_create"] = true;
                    header("Location: " . ADMIN . "/user/two-factor-authentication/backupcodes");
                    exit;
                } else {
                    alert("danger", TRANSLATION["alerts"]["2fa_password_wrong_title"], TRANSLATION["alerts"]["2fa_password_wrong_message"]);
                    ?>
                        <p class="lead"><?=TRANSLATION["2fa_password"]?></p>
                        <form action="<?=ADMIN?>/user/two-factor-authentication" method="POST">
                            <div class="form-group shadow">
                                <input type="password" placeholder="<?=TRANSLATION["password"]?>" class="form-control" name="2fa_vrf_pss" required autofocus autocomplete="current-password" />
                            </div>
                            <div class="btn-group float-right" role="group">
                                <input type="submit" class="btn btn-primary shadow" value="<?=TRANSLATION["2fa_verify_activate"]?>" />
                                <a href="<?=ADMIN?>/user/two-factor-authentication?clear" class="btn btn-default"><?=TRANSLATION["abort"]?></a>
                            </div>
                        </form>
                    <?php
                    exit;
                }
            } elseif (isset($_POST["2fa_vrf_tkn"]) && strlen($_POST["2fa_vrf_tkn"]) == 6) {
                if ($otp->verify($_POST["2fa_vrf_tkn"])) {
                    $_SESSION["2fa_verified"] = true;
                    ?>
                        <p><?=TRANSLATION["2fa_password"]?></p>
                        <form action="<?=ADMIN?>/user/two-factor-authentication" method="POST">
                            <div class="form-group shadow">
                                <input type="password" placeholder="<?=TRANSLATION["password"]?>" class="form-control" name="2fa_vrf_pss" required autofocus autocomplete="current-password" />
                            </div>
                            <div class="btn-group float-right" role="group">
                                <input type="submit" class="btn btn-primary shadow" value="<?=TRANSLATION["2fa_verify_activate"]?>" />
                                <a href="<?=ADMIN?>/user/two-factor-authentication?clear" class="btn btn-default"><?=TRANSLATION["abort"]?></a>
                            </div>
                        </form>
                    <?php
                    exit;
                } else {
                    alert("danger", TRANSLATION["alerts"]["2fa_setup_fail_title"], TRANSLATION["alerts"]["2fa_setup_fail_message"]);
                }
            }
        } else {
            $otp = TOTP::create(
                trim(Base32::encodeUpper(cst_random_bytes(TFA_SECRET_LENGTH / 2)), '='),
                TFA_PERIOD,
                TFA_DIGEST,
                TFA_DIGITS
            );
            $otp->setIssuer(CONFIG["general"]["title"]);
            $otp->setLabel($user["username"]);
            $_SESSION["2fa_provisioning_url"] = $otp->getProvisioningUri();
        }

        ?>
        <p class="lead"><?=TRANSLATION["2fa_lead"]?></p>
        <form action="<?=ADMIN?>/user/two-factor-authentication" method="POST">
            <ol class="mb-2">
                <li><?=TRANSLATION["2fa_step1"]?></li>
                <li>
                    <?=TRANSLATION["2fa_step2"]?> <a data-toggle="collapse" href="#tfa_sec_cde" role="button" aria-expanded="false" aria-controls="tfa_sec_cde"><?=TRANSLATION["2fa_step2_code"]?></a><br />
                    <div class="mx-auto col-12 col-sm-9 col-md-7 col-lg-5 col-xl-4 collapse" id="tfa_sec_cde">
                        <div class="form-group shadow">
                            <input class="form-control text-center pr-3 pl-3" type="text" value="<?=wordwrap($otp->getSecret(), 4, " ", true)?>" onClick="this.select();" readonly />
                        </div>
                    </div>
                    <div class="text-center">
                        <img src="<?=qr($otp->getProvisioningUri())?>">
                    </div>
                </li>
                <li>
                    <?=TRANSLATION["2fa_step3"]?>
                    <div class="mx-auto col-8 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                        <div class="form-group shadow">
                            <input type="text" class="form-control text-center pl-3 pr-3" name="2fa_vrf_tkn" maxlength="<?=TFA_DIGITS?>" minlength="<?=TFA_DIGITS?>" size="<?=TFA_DIGITS?>" placeholder="<?=TRANSLATION["2fa_code"]?>" required autofocus />
                        </div>
                    </div>
                </li>
            </ol>
            <div class="btn-group float-right" role="group">
                <input type="submit" class="btn btn-primary shadow" value="<?=TRANSLATION["2fa_next"]?>" />
                <a href="<?=ADMIN?>/user/two-factor-authentication?clear" class="btn btn-default"><?=TRANSLATION["abort"]?></a>
            </div>
        </form>
<?php } ?>
