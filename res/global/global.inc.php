<?php

    define('RESDIRABS', realpath(__DIR__ . "/../.."));

    require(RESDIRABS . "/data/settings/credentials.inc.php");

    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_dbas);
    define('DBPREFIX', $db_pref);
    unset($db_host, $db_user, $db_pass, $db_dbas, $db_pref);
    if (!$con) {
        die("Could not connect to Database.");
    }
    $con->set_charset("utf8");

    if (!file_exists(RESDIRABS . "/data/settings/config.json")) {
        die("Config-file missing.");
    }
    define('CONFIG_RAW', json_decode(file_get_contents(RESDIRABS . "/data/settings/config.json"), true));

    if (!file_exists(RESDIRABS . "/res/locales/" . CONFIG_RAW["general"]["locale"] . ".json")) {
        die("Language-file missing.");
    }
    define('TRANSLATION', json_decode(file_get_contents(RESDIRABS . "/res/locales/" . CONFIG_RAW["general"]["locale"] . ".json"), true));
    define('CONFIG', patternmatch(CONFIG_RAW, NULL, true, CONFIG_RAW));

    define('RELPATH', BASEURL . (!CONFIG["general"]["rewrite"] ? "/index.php" : ""));
    define('ABSPATH', LINKURL . RELPATH);
    $baseurl = ABSPATH;

    use PHPMailer\PHPMailer\PHPMailer;
    require(RESDIRABS . "/res/phpmailer/PHPMailer.php");
    require(RESDIRABS . "/res/phpmailer/SMTP.php");
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = $em_host;
    $mail->Port = $em_port;
    $mail->SMTPSecure = $em_prot;
    $mail->SMTPAuth = $em_auth;
    $mail->Username = $em_user;
    $mail->Password = $em_pass;
    $mail->setFrom($em_addr, CONFIG["general"]["title"]);
    $mail->addReplyTo(CONFIG["general"]["contact_email"]);
    $mail->isHTML(true);
    $mail->CharSet = "UTF-8";
    unset($em_addr, $em_auth, $em_host, $em_pass, $em_port, $em_prot, $em_user);

    function qr($content, $ecc="Q", $pixels=4) {
        require_once(RESDIRABS . "/res/phpqrcode/qrlib.php");
        ob_start();
        if (strtoupper($ecc) == "L") {
            $ecc = QR_ECLEVEL_L;
        } elseif (strtoupper($ecc) == "M") {
            $ecc = QR_ECLEVEL_M;
        } elseif (strtoupper($ecc) == "H") {
            $ecc = QR_ECLEVEL_H;
        } else {
            $ecc = QR_ECLEVEL_Q;
        }
        QRcode::png($content, false, $ecc, $pixels);
        $qr = base64_encode(ob_get_contents());
        ob_end_clean();
        return "data:image/png;utf8;base64," . $qr;
    }

    function globalfield($fields, $name) {
        return $fields[CONFIG["general"][$name]];
    }

    function money($input, $prefix=false, $html=true) {
        $sep = ($html ? "&nbsp;" : " ");
        return ($input > 0 && $prefix ? "+" : "") . (strlen(TRANSLATION["money_format"]["prefix"]) > 0 ? TRANSLATION["money_format"]["prefix"] . $sep : "") . number_format($input, TRANSLATION["money_format"]["decimals"], TRANSLATION["money_format"]["dec_point"], TRANSLATION["money_format"]["thousands_sep"]) . (strlen(TRANSLATION["money_format"]["suffix"]) > 0 ? $sep . TRANSLATION["money_format"]["suffix"] : "");
    }

    function ext_field($field) {
        $contents = "";
        $contents .= '<div class="form-group col-md-' . $field["width"] . '">';
        $required = "";
        if (!array_key_exists("not_required", $field) || $field["not_required"] != true) {
            $required .= (array_key_exists("pattern", $field) && strlen($field["pattern"]) > 0 ? " pattern=\"" . $field["pattern"] . "\"" : "");
            $required .= (array_key_exists("minlength", $field) && strlen($field["minlength"]) > 0 ? " minlength=\"" . $field["minlength"] . "\"" : "");
            $required .= (array_key_exists("maxlength", $field) && strlen($field["maxlength"]) > 0 ? " maxlength=\"" . $field["maxlength"] . "\"" : "");
            $required .= " required";
        }
        if ($field["type"] == "text" || $field["type"] == "password" || $field["type"] == "email") {
            $contents .= '<input type="' . $field["type"] . '" class="form-control" name="inp_' . $field["name"] . '" placeholder="' . $field["label"] . '"' . $required . (array_key_exists("autocomplete", $field) && strlen($field["autocomplete"]) > 0 ? " autocomplete=\"" . $field["autocomplete"] . "\"" : "") .' />';
        } elseif ($field["type"] == "select") {
            $contents .= '<select name="inp_' . $field["name"] . '" class="form-control"' . $required . (array_key_exists("autocomplete", $field) && strlen($field["autocomplete"]) > 0 ? " autocomplete=\"" . $field["autocomplete"] . "\"" : "") . '><option selected disabled value="">' . $field["label"] . '</option>';
            foreach ($field["options"] as $option) {
                $contents .= '<option>' . $option . '</option>';
            }
            $contents .= '</select>';
        }
        $contents .= '</div>';
        return $contents;
    }

    function find_in_json($array, $fieldname, $value) {
        foreach($array as $line) {
            if ($line[$fieldname] == $value) {
                return $line;
            }
        }
        return false;
    }

    function query_expand_json($query) {
        $querycopy = $query;
        foreach($query as $key => $line) {
            $json = json_decode($line, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $querycopy[$key] = $json;
            }
        }
        return $querycopy;
    }

    function repadd($input, $ident, $htmlspecialchars=false) {
        $reppattern = [];
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                if (is_array($value)) {
                    $reppattern += repadd($value, $ident . ":" . $key, $htmlspecialchars);
                } else {
                    if ($htmlspecialchars) {
                        $reppattern["{{" . $ident . ":" . $key . "}}"] = htmlspecialchars($value, ENT_NOQUOTES);
                    } else {
                        $reppattern["{{" . $ident . ":" . $key . "}}"] = $value;
                    }
                }
            }
        } else {
            $reppattern["{{" . $ident . "}}"] = $input;
        }
        return $reppattern;
    }

    function patternmatch($input, $query=NULL, $doconfig=false, $config=CONFIG, $dotranslation=true, $translation=TRANSLATION, $remove_unused=true, $doqr=true) {
        global $baseurl;
        if (is_array($query)) {
            $reppattern = array(
                "{{order:total_price}}" => money($query["total_price"]),
                "{{order:nr}}" => $query["ordernr"],
                "{{order:amount}}" => $query["amount"],
                "{{order:timestamp}}" => $query["timestamp"],
                "{{order:secret}}" => $query["secret"],
                "{{type}}" => find_in_json($config["types"], "name", $query["type"])["title"],
                "{{payment}}" => find_in_json($config["payment"], "name", $query["payment"])["title"],
                "{{delivery}}" => find_in_json($config["delivery"], "name", $query["type"])["title"]
            );
            $reppattern["{{order:product_name}}"] = ($query["amount"] == 1 ? CONFIG["product"]["name"] : CONFIG["product"]["name_plural"]);
            $reppattern += repadd($query["general_fields"], "general", true);
            $reppattern += repadd($query["type_fields"], "type", true);
            $reppattern += repadd($query["payment_fields"], "payment", true);
            $reppattern += repadd($query["delivery_fields"], "delivery", true);
        } else {
            $reppattern = array();
        }
        $reppattern["{{base_url}}"] = $baseurl;
        $reppattern += repadd($config["general"], "system");
        if ($doconfig == true) {
            $reppattern += repadd($config, "config");
        }
        if ($dotranslation == true) {
            $reppattern += repadd($translation, "translation");
        }
        if (is_array($input)) {
            $isarray = true;
            $input = json_encode($input);
        }
        $input = strtr($input, $reppattern);
        if ($doqr && preg_match("/{{qr}}(.+?){{\/qr}}/", $input)) {
            $input = preg_replace_callback("/{{qr}}(.+?){{\/qr}}/", function ($br) { return qr($br[1], "H", 9); }, $input);
        }
        if ($remove_unused) {
            $input = preg_replace("({{.+?}})", "", $input);
        }
        if (isset($isarray)) {
            $input = json_decode($input, true);
        }
        return $input;
    }

    function validate_field($userinput, $specification) {
        if ((!array_key_exists("not_required", $specification) || $specification["not_required"] != true) && !array_key_exists($specification["name"], $userinput) && empty(trim($userinput[$specification["name"]]))) {
            return false;
        }
        if (array_key_exists("minlength", $specification) && strlen($specification["minlength"]) > 0 && strlen($userinput[$specification["name"]]) < $specification["minlength"]) {
            return false;
        }
        if (array_key_exists("maxlength", $specification) && strlen($specification["maxlength"]) > 0 && strlen($userinput[$specification["name"]]) > $specification["maxlength"]) {
            return false;
        }
        if ($specification["type"] == "select" && !in_array($userinput[$specification["name"]], $specification["options"])) {
            return false;
        }
        if (array_key_exists("pattern", $specification) && strlen($specification["pattern"]) > 0 && !preg_match("/^(" . $specification["pattern"] . ")$/", $userinput[$specification["name"]])) {
            return false;
        }
        return true;
    }

    function cst_random_bytes($length) {
        if (function_exists('random_bytes')) {
            return random_bytes($length);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($len);
        } else {
            return substr(hash('sha256', time() . mt_rand() . uniqid("", true), true), 0, 2 * $length);
        }
    }

?>
