<?php
    define('BASEURL', explode("/index.php", $_SERVER["SCRIPT_NAME"])[0]);
    define('RESDIR', BASEURL . "/res");
    define('LINKURL', ((isset($_SERVER["HTTPS"]) && (strtolower($_SERVER["HTTPS"]) == "on" || $_SERVER["HTTPS"] == 1)) ? "https://" : "http://") . $_SERVER["HTTP_HOST"]);

    require("./res/global/global.inc.php");

    $request = strtolower(isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (isset($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '/'));

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
        header("Location: " . RELPATH . substr($request, 0, -1));
        exit;
    }

    if ($request == "/validate") {
        require("./res/views/validate.php");
        exit;
    }

    if ($request == "/voucher") {
        require("./res/views/voucher.php");
        exit;
    }

?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <title><?=CONFIG["general"]["title"]?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="<?=RESDIR?>/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/global/style.css" />
        <link rel="stylesheet" href="<?=RESDIR?>/ionicons/css/ionicons.min.css">
        <script src="<?=RESDIR?>/bootstrap/js/jquery.min.js"></script>
        <script src="<?=RESDIR?>/bootstrap/js/bootstrap.min.js"></script>
        <?php if ($request != "/noscript") { ?>
            <noscript><meta http-equiv="refresh" content="0; URL=<?=RELPATH?>/noscript"/></noscript>
        <?php } ?>
    </head>
    <body class="d-flex justify-content-center align-items-center flex-column">
        <div class="d-flex col-xl-9 col-12 p-0 align-items-stretch flex-column flex-sm-row text-break shadow cont">
            <?php

                function alert($type, $title, $text) {
                    echo '<div class="fixed-top">
                        <div class="alert alert-dismissible alert-' . $type . ' fade show">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>' .
                            (!empty(trim($title)) ? '<h4 class="alert-heading">' . $title . '</h4>' : '') .
                            (!empty(trim($text)) ? '<p class="mb-0">' . $text . '</p>' : '')
                            . '</div>
                    </div>';
                }

                session_start();
                if (isset($_SESSION["login_id"])) {
                  $user = $con->query("SELECT id, username, name, sessionhash FROM " . DBPREFIX . "users WHERE id = " . $con->real_escape_string($_SESSION["login_id"]));
                  if ($user->num_rows != 1) {
                      $user = [];
                  } else {
                      $user = $user->fetch_assoc();
                      alert("success", "", sprintf(TRANSLATION["loggedin_as"], "<b>" . $user["name"] . "</b>") . " <a href=\"./admin\" class=\"alert-link\">" . TRANSLATION["administration"] . "</a>.");
                      if ($_SESSION["login_hash"] != $user["sessionhash"] || (time() - $_SESSION["last_activity"]) > CONFIG["general"]["timeout"]) {
                          session_destroy();
                          header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
                          exit;
                      }
                  }
                }
                session_write_close();

                $expl = explode("/", $request);
                if (sizeof($expl) > 1 && $expl[1] == "o") {
                    if (sizeof($expl) >= 3 && strlen($expl[2]) > 0 && strlen($expl[3]) == 16) {
                        $query = $con->query("SELECT * FROM " . DBPREFIX . "orders WHERE ordernr = '" . $con->real_escape_string($expl[2]) . "' && secret = '" . $con->real_escape_string($expl[3]) . "'");
                        if ($query->num_rows != 1) {
                            header("Location: " . RELPATH . "/not-found");
                            exit;
                        } else {
                            require("./res/views/overview.php");
                        }
                    } else {
                        header("Location: " . RELPATH . "/not-found");
                        exit;
                    }
                } else {
                    switch ($request) {
                        case "/cancel":
                            if (isset($_POST["ordernr"]) && isset($_POST["secret"]) && isset($_POST["token"]) && $con->query("UPDATE " . DBPREFIX . "orders SET status = 'ordered,cancelled', cancelled_timestamp = CURRENT_TIMESTAMP WHERE ordernr='" . $con->real_escape_string($_POST["ordernr"]) . "' AND secret='" . $con->real_escape_string($_POST["secret"]) . "' AND MD5(timestamp)='" . $con->real_escape_string($_POST["token"]) . "'")) {
                                alert("success", TRANSLATION["alerts"]["cancel_title"], TRANSLATION["alerts"]["cancel_message"]);
                                require("./res/views/home.php");
                                break;
                            } else {
                                header("Location: " . RELPATH . "/error");
                                exit;
                            }
                        case "/cancelled":
                            alert("info", TRANSLATION["alerts"]["cancelled_title"], TRANSLATION["alerts"]["cancelled_message"]);
                            require("./res/views/home.php");
                            break;
                        case "/not-found":
                            alert("danger", TRANSLATION["alerts"]["not_found_title"], TRANSLATION["alerts"]["not_found_message"]);
                            require("./res/views/home.php");
                            break;
                        case "/error":
                            alert("danger", TRANSLATION["alerts"]["error_title"], sprintf(TRANSLATION["alerts"]["error_message"], '<a class="alert-link" href="mailto:' . CONFIG["general"]["contact_email"] . '">' . CONFIG["general"]["contact_email"] . '</a>'));
                            require("./res/views/home.php");
                            break;
                        case "/missing":
                            alert("danger", TRANSLATION["alerts"]["missing_title"], TRANSLATION["alerts"]["missing_message"]);
                            require("./res/views/home.php");
                            break;

                        case "":
                        case "/":
                            if (strtotime(CONFIG["general"]["order_till"]) > time() || !empty($user)) {
                                require("./res/views/home.php");
                            } else {
                                require("./res/views/noorders.php");
                            }
                            break;
                        case "/checkout":
                            if (strtotime(CONFIG["general"]["order_till"]) > time() || !empty($user)) {
                                require("./res/views/checkout.php");
                            } else {
                                require("./res/views/noorders.php");
                            }
                            break;
                        case "/noscript":
                            require("./res/views/noscript.php");
                            break;
                        case "/thanks":
                            require("./res/views/thanks.php");
                            break;
                        default:
                            header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
                            exit;
                    }
                }

            ?>
        </div>
    </body>
</html>
