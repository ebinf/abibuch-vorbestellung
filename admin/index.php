<?php
    session_start();

    define('BASEURL', explode("/admin/index.php", $_SERVER["SCRIPT_NAME"])[0]);
    define('RESDIR', BASEURL . "/res");
    define('LINKURL', ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1)) ? "https://" : "http://") . $_SERVER["HTTP_HOST"]);

    require("../res/global/global.inc.php");

    define('ADMIN', RELPATH . "/admin");

    $request = strtolower(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/'));
    $expl = explode("/", $request);
    $menu = $expl[1];

    if (sizeof($expl) <= 1) {
        header("Location: " . ADMIN . "/dashboard");
        exit;
    }

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

    if ($menu == "noscript") {
        require("./views/noscript.php");
        exit;
    }

    if ($menu == "login") {
        require("./views/login.php");
        exit;
    }

    if (!isset($_SESSION["login_id"]) || !isset($_SESSION["last_activity"]) || !isset($_SESSION["login_hash"])) {
        header("Location: " . ADMIN . "/login");
        exit;
    }

    if((time() - $_SESSION["last_activity"]) > CONFIG["general"]["timeout"]) {
        session_destroy();
        header("Location: " . ADMIN . "/login?timeout");
        exit;
    }
    $_SESSION["last_activity"] = time();

    $user = $con->query("SELECT id, username, name, sessionhash FROM " . DBPREFIX . "users WHERE id = " . $con->real_escape_string($_SESSION["login_id"]));
    if ($user->num_rows != 1) {
        session_destroy();
        header("Location: " . ADMIN . "/login");
        exit;
    }

    $user = $user->fetch_assoc();
    if ($_SESSION["login_hash"] != $user["sessionhash"] ) {
        session_destroy();
        header("Location: " . ADMIN . "/login?timeout");
        exit;
    }

    if ($menu == "logout" && sizeof($expl) == 3 && $expl[2] == "all-sessions") {
        if (isset($_POST["token"]) && base64_decode($_POST["token"]) == md5("lgo_all_sessions" . $user["id"] . $user["username"])) {
            $con->query("UPDATE " . DBPREFIX . "users SET sessionhash='" . $con->real_escape_string(base64_encode(md5(time()))) . "' WHERE id = " . $user["id"]);
        } else {
            $_SESSION["alert"] = ["danger", "admin_error"];
            header("Location: " . ADMIN . "/user");
            exit;
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
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
            <a class="navbar-brand" href="./"><?=TRANSLATION["administration"]?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#nav_bar" aria-controls="nav_bar" aria-expanded="false" aria-label="<?=TRANSLATION["toggle_navigation"]?>">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="nav_bar">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item<?=($menu=="dashboard"?" active":"")?>">
                        <a class="nav-link" href="<?=ADMIN?>/dashboard"><?=TRANSLATION["dashboard"]?></a>
                    </li>
                    <li class="nav-item<?=($menu=="orders"?" active":"")?>">
                        <a class="nav-link" href="<?=ADMIN?>/orders"><?=TRANSLATION["orders"]?></a>
                    </li>
                    <li class="nav-item<?=($menu=="vouchers"?" active":"")?>">
                        <a class="nav-link" href="<?=ADMIN?>/vouchers"><?=TRANSLATION["vouchers"]?></a>
                    </li>
                    <li class="nav-item<?=($menu=="payment"?" active":"")?>">
                        <a class="nav-link" href="<?=ADMIN?>/payment"><?=TRANSLATION["payment"]?></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown mr-0">
                        <a class="nav-link dropdown-toggle<?=($menu=="user"?" active":"")?>" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?=$user["name"]?></a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item<?=($menu=="user" && sizeof($expl) == 2?" active":"")?>" href="<?=ADMIN?>/user"><?=TRANSLATION["account"]?></a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?=ADMIN?>/logout"><?=TRANSLATION["logout"]?></a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="p-2 container">
            <?php

                function alert($type, $title, $text) {
                    echo '<div class="fixed-bottom">
                        <div class="alert alert-dismissible alert-' . $type . ' fade show mb-0">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <h4 class="alert-heading">' . $title . '</h4>
                            <p class="mb-0">' . $text . '</p>
                        </div>
                    </div>';
                }

                if (isset($_SESSION["alert"])) {
                    if (sizeof($_SESSION["alert"]) == 2) {
                        alert($_SESSION["alert"][0], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_title"], TRANSLATION["alerts"][$_SESSION["alert"][1] . "_message"]);
                    } elseif (sizeof($_SESSION["alert"]) == 3) {
                        alert($_SESSION["alert"][0], $_SESSION["alert"][1], $_SESSION["alert"][2]);
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

                    case "user":
                        require("./views/user.php");
                        exit;

                    case "logout":
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
