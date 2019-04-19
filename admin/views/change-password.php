<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) > 3) {
        header("Location: " . ADMIN . "/" . $menu);
        exit;
    }

    if (isset($_POST["pwd_sbm"])) {
        if (!isset($_POST["pwd_old"]) ||
            empty(trim($_POST["pwd_old"])) ||
            !isset($_POST["pwd_new"]) ||
            empty(trim($_POST["pwd_new"])) ||
            !isset($_POST["pwd_rep"]) ||
            empty(trim($_POST["pwd_rep"]))) {
            alert("danger", TRANSLATION["alerts"]["login_missing_title"], TRANSLATION["alerts"]["login_missing_message"]);
        } else {
            $query = $con->query("SELECT password,totp FROM " . DBPREFIX . "users WHERE id = " . $user["id"])->fetch_assoc();
            if ($_POST["pwd_new"] != $_POST["pwd_rep"]) {
                alert("danger", TRANSLATION["alerts"]["not_matching_title"], TRANSLATION["alerts"]["not_matching_message"]);
            } else {
                if (!password_verify($_POST["pwd_old"], $query["password"])) {
                    alert("danger", TRANSLATION["alerts"]["old_password_wrong_title"], TRANSLATION["alerts"]["old_password_wrong_message"]);
                } else {
                    $new_pass = password_hash($_POST["pwd_new"], PASSWORD_DEFAULT);
                    $new_sessionhash = base64_encode(md5(time()));
                    if (!empty(trim($query["totp"]))) {
                        require_once(RESDIRABS . "/res/otphp/2fa.php");
                        $encrypted = base64_decode($query["totp"]);
                        $iv = substr($encrypted, 0, openssl_cipher_iv_length(TFA_CIPHER));
                        $decrypted_raw = substr($encrypted, openssl_cipher_iv_length(TFA_CIPHER));
                        $decrypted = openssl_decrypt($decrypted_raw, TFA_CIPHER, hash("sha256", $_POST["pwd_old"]), 0, $iv);
                        unset($iv, $encrypted, $decrypted_raw);
                        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(TFA_CIPHER));
                        $encrypted = openssl_encrypt($decrypted, TFA_CIPHER, hash("sha256", $_POST["pwd_new"]), 0, $iv);
                        $new_totp = base64_encode($iv . $encrypted);
                    } else {
                        $new_totp = $query["totp"];
                    }
                    $query = $con->query("UPDATE " . DBPREFIX . "users SET password = '" . $con->real_escape_string($new_pass) . "', totp = '" . $con->real_escape_string($new_totp) . "', sessionhash='" . $con->real_escape_string($new_sessionhash) . "' WHERE id = " . $user["id"]);
                    $_SESSION["login_hash"] = $new_sessionhash;
                    $_SESSION["alert"] = ["success", "password_changed"];
                    header("Location: " . ADMIN . "/user");
                    exit;
                }
            }
        }
    }

?>
<h1>Passwort ändern</h1>
<form action="./change-password" method="POST">
    <div class="form-group shadow">
        <input type="password" class="form-control" name="pwd_old" placeholder="<?=TRANSLATION["old_password"]?>" autocomplete="current-password" required />
    </div>
    <div class="form-group shadow">
        <input type="password" class="form-control" name="pwd_new" placeholder="<?=TRANSLATION["new_password"]?>" autocomplete="new-password" required />
    </div>
    <div class="form-group shadow">
        <input type="password" class="form-control" name="pwd_rep" placeholder="<?=TRANSLATION["repeat_password"]?>" autocomplete="new-password" required />
    </div>
    <div class="btn-group float-right" role="group">
        <input type="submit" name="pwd_sbm" class="btn btn-primary shadow" value="<?=TRANSLATION["change_password"]?>" />
        <a href="<?=ADMIN?>/user" class="btn btn-default"><?=TRANSLATION["abort"]?></a>
    </div>
</form>
