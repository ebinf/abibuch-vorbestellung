<?php


    define('BASEURL', explode("/admin/index.php", $_SERVER["SCRIPT_NAME"])[0]);
    define('RESDIR', BASEURL . "/res");
    define('LINKURL', ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1)) ? "https://" : "http://") . $_SERVER["HTTP_HOST"]);

    require("../res/global/global.inc.php");

    define('ADMIN', RELPATH . "/admin");

    $request = strtolower(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/'));
    $expl = explode("/", $request);
    $menu = $expl[1];

    if (!CONFIG["general"]["allow_insecure"] && (!isset($_SERVER["HTTPS"]) || (strtolower($_SERVER["HTTPS"]) != "on" && $_SERVER["HTTPS"] != 1))) {
        header("Location: " . str_replace("http://", "https://", ABSPATH) . $request);
        exit;
    }

    if(CONFIG["general"]["rewrite"] && stripos($_SERVER["REQUEST_URI"], RELPATH . "/index.php") === 0) {
        if($request != "/" && substr($request, -1) == '/') {
            header("Location: " . RELPATH . substr($request, 0, -1));
        } else {
            header("Location: " . RELPATH . $request);
        }
        exit;
    }

    if($request != "/" && substr($request, -1) == '/') {
        header("Location: " . ADMIN . substr($request, 0, -1));
        exit;
    }

    if ($menu == "api") {
        require("./views/api.php");
        exit;
    }

    if (sizeof($expl) <= 1) {
        header("Location: " . ADMIN . "/dashboard");
        exit;
    }

    if ($menu == "noscript") {
        require("./views/noscript.php");
        exit;
    }

    if (session_status() != PHP_SESSION_ACTIVE) {
        ini_set('session.use_strict_mode', 1);
        ini_set('session.gc_maxlifetime', 43200);
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'strict');
        if (CONFIG["general"]["allow_insecure"] == false) {
            ini_set('session.cookie_secure', 1);
        }
    }
    session_start();

    if ($menu == "login") {
        require("./views/login.php");
        exit;
    }

    if (!isset($_SESSION["login_id"]) || !isset($_SESSION["last_activity"]) || !isset($_SESSION["login_hash"])) {
        header("Location: " . ADMIN . "/login");
        exit;
    }

    $user = $con->query("SELECT id, username, name, sessionhash FROM " . DBPREFIX . "users WHERE id = " . $con->real_escape_string($_SESSION["login_id"]));
    if ($user->num_rows != 1) {
        session_destroy();
        header("Location: " . ADMIN . "/login");
        exit;
    }

    $user = $user->fetch_assoc();
    if ($_SESSION["login_hash"] != $user["sessionhash"] || (time() - $_SESSION["last_activity"]) > CONFIG["general"]["timeout"]) {
        session_destroy();
        unset($expl[0]);
        header("Location: " . ADMIN . "/login?timeout&refer=" . join("/", $expl));
        exit;
    }
    $_SESSION["last_activity"] = time();


?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title><?=TRANSLATION["administration"]?> – <?=CONFIG["general"]["title"]?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?=RESDIR?>/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/global/admin.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/ionicons/css/ionicons.min.css">
        <script src="<?=RESDIR?>/popper/popper.min.js"></script>
        <script src="<?=RESDIR?>/bootstrap/js/jquery.min.js"></script>
        <script src="<?=RESDIR?>/bootstrap/js/bootstrap.min.js"></script>
        <script>$(function () { $('[data-toggle="tooltip"]').tooltip(); });</script>
        <noscript><meta http-equiv="refresh" content="0; URL=<?=RELPATH?>/admin/noscript"/></noscript>
        <?php
            if (sizeof($expl) == 4 && $expl[1] == "vouchers" && $expl[3] == "print") {
                require("./views/vouchers.php");
                exit;
            }
        ?>
    </head>
    <body>
        <nav class="navbar navbar-expand-md navbar-dark bg-primary sticky-top">
            <a class="navbar-brand" href="./"><?=TRANSLATION["administration"]?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav_bar" aria-controls="nav_bar" aria-expanded="false" aria-label="<?=TRANSLATION["toggle_navigation"]?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav_bar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item<?=($menu=="dashboard"?" active":"")?>">
                        <a class="nav-link" href="<?=ADMIN?>/dashboard"><?=TRANSLATION["dashboard"]?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?=($menu=="orders"||$menu=="vouchers"||$menu=="payment"?" active":"")?>" href="#" id="data-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=TRANSLATION["data"]?></a>
                        <div class="dropdown-menu" aria-labelledby="data-dropdown">
                            <a class="dropdown-item<?=($menu=="orders"?" active":"")?>" href="<?=ADMIN?>/orders"><?=TRANSLATION["orders"]?></a>
                            <a class="dropdown-item<?=($menu=="vouchers"?" active":"")?>" href="<?=ADMIN?>/vouchers"><?=TRANSLATION["vouchers"]?></a>
                            <a class="dropdown-item<?=($menu=="payment"?" active":"")?>" href="<?=ADMIN?>/payment"><?=TRANSLATION["payment"]?></a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle<?=($menu=="settings"||$menu=="users"||$menu=="api-tokens"?" active":"")?>" href="#" id="system-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=TRANSLATION["system"]?></a>
                        <div class="dropdown-menu" aria-labelledby="system-dropdown">
                            <a class="dropdown-item<?=($menu=="settings"?" active":"")?>" href="<?=ADMIN?>/settings"><?=TRANSLATION["settings"]?></a>
                            <a class="dropdown-item<?=($menu=="users"?" active":"")?>" href="<?=ADMIN?>/users"><?=TRANSLATION["users"]?></a>
                            <a class="dropdown-item<?=($menu=="api-tokens"?" active":"")?>" href="<?=ADMIN?>/api-tokens"><?=TRANSLATION["api_tokens"]?></a>
                        </div>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown mr-0">
                        <a class="nav-link dropdown-toggle<?=($menu=="user"?" active":"")?>" href="#" id="user-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$user["name"]?></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-dropdown">
                            <a class="dropdown-item<?=($menu=="user" && sizeof($expl) == 2?" active":"")?>" href="<?=ADMIN?>/user"><?=TRANSLATION["account"]?></a>
                            <a class="dropdown-item" href="<?=ADMIN?>/logout"><?=TRANSLATION["logout"]?></a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="p-2 container">
            <?php

                function alert($type, $title, $text, $undo="") {
                    echo '<div class="fixed-bottom">
                        <div class="alert alert-dismissible alert-' . $type . ' fade show mb-0">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4 class="alert-heading">' . $title . '</h4>
                            <p class="mb-0">' . $text . (strlen($undo) > 0 ? " <a class=\"alert-link\" href=\"" . $undo . "\"><i class=\"ion-md-undo\"></i> " . TRANSLATION["undo"] . "</a>" : "") . '</p>
                        </div>
                    </div>';
                }

                if (isset($_SESSION["alert"])) {
                    if (sizeof($_SESSION["alert"]) == 2) {
                        if (is_array($_SESSION["alert"][1])) {
                            alert($_SESSION["alert"][0], $_SESSION["alert"][1][0], $_SESSION["alert"][1][1]);
                        } else {
                            alert($_SESSION["alert"][0], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_title"], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_message"]);
                        }
                    } elseif (sizeof($_SESSION["alert"]) == 3) {
                        if (is_array($_SESSION["alert"][1])) {
                            alert($_SESSION["alert"][0], $_SESSION["alert"][1][0], $_SESSION["alert"][1][1], $_SESSION["alert"][2]);
                        } else {
                            alert($_SESSION["alert"][0], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_title"], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_message"], $_SESSION["alert"][2]);
                        }
                    }
                    unset($_SESSION["alert"]);
                }

                switch ($menu) {
                    case "dashboard":
                        require("./views/dashboard.php");
                        exit;

                    case "orders":
                        require("./views/orders.php");
                        exit;

                    case "vouchers":
                        require("./views/vouchers.php");
                        exit;

                    case "payment":
                        require("./views/payment.php");
                        exit;

                    case "api-tokens":
                        require("./views/api-tokens.php");
                        exit;

                    case "user":
                        require("./views/user.php");
                        exit;

                    case "logout":
                        if (sizeof($expl) == 3 && $expl[2] == "all-sessions") {
                            if (isset($_POST["token"]) && base64_decode($_POST["token"]) == md5("lgo_all_sessions" . $user["id"] . $user["username"])) {
                                $con->query("UPDATE " . DBPREFIX . "users SET sessionhash='" . $con->real_escape_string(base64_encode(md5(time()))) . "' WHERE id = " . $user["id"]);
                            } else {
                                $_SESSION["alert"] = ["danger", "admin_error"];
                                header("Location: " . ADMIN . "/user");
                                exit;
                            }
                        }
                        session_unset();
                        session_destroy();
                        header("Location: " . ADMIN . "/login?logout");
                        exit;

                    default:
                        header("Location: " . ADMIN . "/dashboard");
                        exit;
                }
            ?>
        </div>
    </body>
</html>
