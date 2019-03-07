<?php

    require("database.inc.php");

    $header = '<!DOCTYPE html>
<html lang="de">
    <head>
        <title>' . $title . '</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="./res/boo/css/bootstrap.min.css" />
        <link rel="stylesheet" href="./res/glb/style.css" />
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


    $con = mysqli_connect($db_host, $db_user, $db_pass, $db_dbas);
    if (!$con) {
        die("Could not connect to Database.");
    }

    if (!file_exists("local.json")) {
        die("Language-file missing.");
    }
    $translation = json_decode("local.json", true);

    if (!file_exists("config.json")) {
        die("Config-file missing.");
    }
    $config = json_decode("config.json", true);

?>
