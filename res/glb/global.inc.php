<?php

    $title = "Abibuch 2019 Vorverkauf";

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
        die("Fehler: konnte nicht mit der Datenbank verbinden.");
        exit;
    }

?>
