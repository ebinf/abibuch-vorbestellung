<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

    if (sizeof($expl) > 2) {
        header("Location: " . ADMIN . "/" . $menu);
        exit;
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
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h4 class="card-title"><?=TRANSLATION["alerts"]["noscript_title"]?></h4>
                        <p class="card-text"><?=TRANSLATION["alerts"]["noscript_message"]?></p>
                    </div>
                </div>
            </div>
            <div>
                <h6><a href="<?=(strlen(RELPATH) == 0 ? "/" : RELPATH)?>" class="mb-1"><i class="fa fa-angle-left" aria-hidden="true"></i> <?=TRANSLATION["back_to"]?> <?=CONFIG["general"]["title"]?></a></h6>
            </div>
        </div>
    </body>
</html>
<script>
<!--
    $(document).ready(function () {
        $(location).attr("href", "<?=RELPATH?>/admin");
    });
-->
</script>
