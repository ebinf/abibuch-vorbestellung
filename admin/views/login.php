<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) > 2) {
        header("Location: " . ADMIN . "/" . $menu);
        exit;
    }

    if (isset($_SESSION["login_id"])) {
        header("Location: " . ADMIN . "/dashboard");
        exit;
    } else {
        if (isset($_GET["abort"])) {
            session_destroy();
            header("Location: " . ADMIN . "/login");
            exit;
        }
        if (isset($_POST["lgi_usr"]) && isset($_POST["lgi_pss"])) {
            if (empty(trim($_POST["lgi_usr"])) || empty(trim($_POST["lgi_pss"]))) {
                $login_error = "login_missing";
            } else {
                $query = $con->query("SELECT id, password, sessionhash, totp FROM " . DBPREFIX . "users WHERE username = '" . $con->real_escape_string(strtolower($_POST["lgi_usr"])) . "'");
                if ($query->num_rows == 1) {
                    $query = $query->fetch_assoc();
                    if (password_verify($_POST["lgi_pss"], $query["password"])) {
                        if (password_needs_rehash($query["password"], PASSWORD_DEFAULT)) {
                            $newHash = password_hash($_POST["lgi_pss"], PASSWORD_DEFAULT);
                            $con->query("UPDATE " . DBPREFIX . "users SET password = '" . $con->real_escape_string($newHash) . "' WHERE id = " . $query["id"]);
                        }
                        if (empty(trim($query["totp"]))) {
                            $_SESSION["login_id"] = $query["id"];
                            $_SESSION["last_activity"] = time();
                            $_SESSION["login_hash"] = $query["sessionhash"];
                        } else {
                            require_once(RESDIRABS . "/res/otphp/2fa.php");
                            $encrypted = base64_decode($query["totp"]);
                            $iv = substr($encrypted, 0, openssl_cipher_iv_length(TFA_CIPHER));
                            $decrypted_raw = substr($encrypted, openssl_cipher_iv_length(TFA_CIPHER));
                            $_SESSION["login_2fa"] = [$query["id"], openssl_decrypt($decrypted_raw, TFA_CIPHER, hash("sha256", $_POST["lgi_pss"]), 0, $iv)];
                        }
                    } else {
                        $login_error = "login_wrong";
                    }
                } else {
                    $login_error = "login_wrong";
                }
                unset($query);
                if (!isset($login_error)) {
                    header("Location: " . ADMIN . "/dashboard");
                    exit;
                }
            }
        } elseif (isset($_SESSION["login_2fa"]) && isset($_POST["lgi_2fa"])) {
            try {
                if (empty(trim($_POST["lgi_2fa"])) || !is_numeric($_POST["lgi_2fa"])) {
                    $login_error = "login_missing";
                } else {
                    require_once(RESDIRABS . "/res/otphp/2fa.php");
                    $factory = new OTPHP\Factory;
                    $otp = $factory::loadFromProvisioningUri($_SESSION["login_2fa"][1]);

                    if ($otp->verify($_POST["lgi_2fa"])) {
                        $user = $con->query("SELECT sessionhash FROM " . DBPREFIX . "users WHERE id = " . $_SESSION["login_2fa"][0])->fetch_assoc();
                        $_SESSION["login_id"] = $_SESSION["login_2fa"][0];
                        $_SESSION["last_activity"] = time();
                        $_SESSION["login_hash"] = $user["sessionhash"];
                        unset($_SESSION["login_2fa"]);
                    } else {
                        $login_error = "2fa_wrong";
                    }
                }
            } catch (Exception $e) {
                $login_error = "admin_error";
            } finally {
                if (!isset($login_error)) {
                    header("Location: " . ADMIN . "/dashboard");
                    exit;
                }
            }
        } elseif (isset($_SESSION["login_2fa"]) && isset($_POST["2fa_bck_use"]) && isset($_POST["lgi_2fa_bck"])) {
            if (empty(trim($_POST["lgi_2fa_bck"]))) {
                $login_error = "login_missing";
            } else {
                $query = $con->query("SELECT id, 2fa_backup, sessionhash FROM " . DBPREFIX . "users WHERE id = " . $_SESSION["login_2fa"][0])->fetch_assoc();
                $backupcodes = json_decode($query["2fa_backup"]);

                foreach($backupcodes as $key => $backupcode) {
                    if (password_verify(strtoupper($_POST["lgi_2fa_bck"]), $backupcode)) {
                        unset($backupcodes[$key]);
                        $con->query("UPDATE " . DBPREFIX . "users SET 2fa_backup = '" . $con->real_escape_string(json_encode(array_values($backupcodes))) . "' WHERE id = " . $query["id"]);
                        $_SESSION["login_id"] = $query["id"];
                        $_SESSION["last_activity"] = time();
                        $_SESSION["login_hash"] = $query["sessionhash"];
                        unset($_SESSION["login_2fa"]);
                        if (sizeof($backupcodes) == 0) {
                            $_SESSION["alert"] = ["danger", "2fa_disabled_backupcodes"];
                            $_SESSION["2fa_backupcodes_create"] = true;
                            header("Location: " . ADMIN . "/user/two-factor-authentication/backupcodes");
                            exit;
                        }
                        header("Location: " . ADMIN . "/dashboard");
                        exit;
                    }
                }
                $login_error = "2fa_backup_wrong";
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title><?=TRANSLATION["administration"]?> – <?=CONFIG["general"]["title"]?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?=RESDIR?>/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/global/style.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/font-awesome/css/font-awesome.min.css" />
        <script src="<?=RESDIR?>/bootstrap/js/jquery.min.js"></script>
        <script src="<?=RESDIR?>/bootstrap/js/bootstrap.min.js"></script>
    </head>
    <body class="d-flex justify-content-end">
        <div class="col-12 col-lg-4 h-100 bg-light border-0 w-auto text-right d-flex flex-column justify-content-between cont">
            <div>
                <h1 class="display-3"><?=TRANSLATION["login"]?></h1>
                <p class="lead"><?=TRANSLATION["login_info"]?></p>
            </div>
            <div>
                <?php

                if (isset($_SESSION["login_2fa"])) {
                    ?>
                        <div class="text-center">
                            <h5><?=TRANSLATION["2fa"]?></h5>
                            <?php if (isset($_POST["2fa_bck_use"])) { ?>
                                <p><?=TRANSLATION["2fa_login_backupcode"]?></p>
                            <?php } else { ?>
                                <p><?=TRANSLATION["2fa_login"]?></p>
                            <?php } ?>
                        </div>
                    <?php
                }

                    if (isset($login_error)) {
                        ?>
                            <div class="alert alert-dismissible alert-danger text-left shadow">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h4 class="alert-heading"><?=TRANSLATION["alerts"][$login_error . "_title"]?></h4>
                                <p class="mb-0"><?=TRANSLATION["alerts"][$login_error . "_message"]?></p>
                            </div>
                        <?php
                    } elseif (isset($_GET["timeout"])) {
                        ?>
                            <div class="alert alert-dismissible alert-warning text-left shadow">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h4 class="alert-heading"><?=TRANSLATION["alerts"]["timeout_title"]?></h4>
                                <p class="mb-0"><?=TRANSLATION["alerts"]["timeout_message"]?></p>
                            </div>
                        <?php
                    } elseif (isset($_GET["logout"])) {
                        ?>
                            <div class="alert alert-dismissible alert-success text-left shadow">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <h4 class="alert-heading"><?=TRANSLATION["alerts"]["logout_title"]?></h4>
                                <p class="mb-0"><?=TRANSLATION["alerts"]["logout_message"]?></p>
                            </div>
                        <?php
                    }

                    if (isset($_SESSION["login_2fa"])) {
                        include_once(RESDIRABS . "/res/otphp/2fa.php");
                        if (isset($_POST["2fa_bck_use"])) { ?>
                            <div class="form-group">
                                <form action="./login" method="POST">
                                    <div class="shadow">
                                        <input type="hidden" name="2fa_bck_use" />
                                        <input class="form-control mb-3 text-uppercase" name="lgi_2fa_bck" type="text" placeholder="<?=TRANSLATION["2fa_backupcode"]?>" required autofocus maxlength="<?=(TFA_BACKUPCODE_LENGTH + ceil((TFA_BACKUPCODE_LENGTH / TFA_BACKUPCODE_SEPARATOR) - 1))?>" minlength="<?=(TFA_BACKUPCODE_LENGTH + ceil((TFA_BACKUPCODE_LENGTH / TFA_BACKUPCODE_SEPARATOR) - 1))?>" />
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block shadow"><?=TRANSLATION["login"]?></button>
                                </form>
                                <form action="./login" method="POST">
                                    <button type="submit" class="btn btn-default btn-block"><?=TRANSLATION["2fa_use_code"]?></button>
                                </form>
                            </div>
                        <?php } else { ?>
                                <div class="form-group">
                                    <form action="./login" method="POST">
                                        <div class="shadow">
                                            <input class="form-control form-control-lg text-center mb-3" name="lgi_2fa" type="text" placeholder="<?=TRANSLATION["2fa_code"]?>" required autofocus maxlength="<?=TFA_DIGITS?>" minlength="<?=TFA_DIGITS?>" />
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-block shadow"><?=TRANSLATION["login"]?></button>
                                    </form>
                                    <form action="./login" method="POST">
                                        <input type="hidden" name="2fa_bck_use" />
                                        <button type="submit" class="btn btn-default btn-block"><?=TRANSLATION["2fa_use_backupcode"]?></button>
                                    </form>
                                </div>
                        <?php } ?>
                            </div>
                            <div class="text-center">
                                <h6><a href="./login?abort" class="mb-1"><?=TRANSLATION["abort"]?></a></h6>
                            </div>
            <?php } else { ?>
                <form action="./login" method="POST">
                    <div class="form-group shadow">
                        <input class="form-control" name="lgi_usr" type="text" placeholder="<?=TRANSLATION["username"]?>" required autocomplete="username" autofocus />
                    </div>
                    <div class="form-group shadow">
                        <input class="form-control" name="lgi_pss" type="password" placeholder="<?=TRANSLATION["password"]?>" required autocomplete="current-password" />
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block shadow"><?=TRANSLATION["login"]?></button>
                    </div>
                </form>
                </div>
                <div>
                    <h6><a href="<?=(strlen(RELPATH) == 0 ? "/" : RELPATH)?>" class="mb-1"><i class="fa fa-angle-left" aria-hidden="true"></i> <?=TRANSLATION["back_to"]?> <?=CONFIG["general"]["title"]?></a></h6>
                </div>
            <?php } ?>
        </div>
    </body>
</html>
