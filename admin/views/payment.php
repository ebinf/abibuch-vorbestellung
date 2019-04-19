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
<h1><?=TRANSLATION["payment"]?></h1>
