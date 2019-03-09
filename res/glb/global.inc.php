<?php

    require("database.inc.php");

    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_dbas);
    if (!$con) {
        die("Could not connect to Database.");
    }

    if (!file_exists("./res/glb/local.json")) {
        die("Language-file missing.");
    }
    $translation = json_decode(file_get_contents("./res/glb/local.json"), true);

    if (!file_exists("./res/glb/config.json")) {
        die("Config-file missing.");
    }
    $config = json_decode(file_get_contents("./res/glb/config.json"), true);

    $header = '<!DOCTYPE html>
<html lang="de">
    <head>
        <title>' . $config["general"]["title"] . '</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./res/boo/css/bootstrap.min.css" />
        <link rel="stylesheet" href="./res/glb/style.css" />
        <link rel="stylesheet" href="./res/foa/css/font-awesome.min.css" />
    </head>
    <body>
        <div class="h-100 d-flex justify-content-center align-items-stretch align-items-sm-center flex-column">
            <div class="col-12 col-xl-9 d-flex p-0 bg-light border-0 shadow flex-column flex-sm-row">' . "\r\n";

    $footer = "\t\t\t</div>
        </div>
        <script src=\"./res/boo/js/jquery.slim.min.js\"></script>
        <script src=\"./res/boo/js/bootstrap.min.js\"></script>
    </body>
</html>";

    function globalfield($fields, $name) {
        global $config;
        return $fields[$config["general"][$name]];
    }

    function greeting($fields) {
        global $config, $translation;
        return sprintf($translation["title"], globalfield($fields, "greeting"));
    }

    function money($input) {
        global $translation;
        return (strlen($translation["money_format"]["prefix"]) > 0 ? $translation["money_format"]["prefix"] . " " : "") . number_format($input, $translation["money_format"]["decimals"], $translation["money_format"]["dec_point"], $translation["money_format"]["thousands_sep"]) . (strlen($translation["money_format"]["suffix"]) > 0 ? " " . $translation["money_format"]["suffix"] : "");
    }

    function ext_field($field) {
        $contents = "";
        $contents .= '<div class="form-group col-md-' . $field["width"] . '">';
        if ($field["type"] == "text" || $field["type"] == "password" || $field["type"] == "email") {
            $contents .= '<input type="' . $field["type"] . '" class="form-control" name="inp_' . $field["name"] . '" placeholder="' . $field["label"] . '" required>';
        } elseif ($field["type"] == "select") {
            $contents .= '<select id="inp_' . $field["name"] . '" class="form-control" required>
                <option selected disabled value="">' . $field["label"] . '</option>';
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

    function repadd($input, $ident) {
        $reppattern = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $reppattern += repadd($value, $ident . ":" . $key);
            } else {
                $reppattern["{{" . $ident . ":" . $key . "}}"] = $value;
            }
        }
        return $reppattern;
    }

    function patternmatch($input, $query) {
        global $config, $translation;
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

        $reppattern += repadd($config["general"], "system");
        $reppattern += repadd($query["general_fields"], "general");
        $reppattern += repadd($query["type_fields"], "type");
        $reppattern += repadd($query["payment_fields"], "payment");
        $reppattern += repadd($query["delivery_fields"], "delivery");
        $reppattern += repadd($translation, "translation");
        $input = strtr($input, $reppattern);
        $input = preg_replace("{{.+}}", "", $input);
        return $input;
    }

?>
