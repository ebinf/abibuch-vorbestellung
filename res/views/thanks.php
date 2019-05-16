<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

?>
<div class="col-12 col-sm-8 p-3 bg-light border-0 cont">
    <h1 class="d-none d-md-block display-3"><?=TRANSLATION["thanks"]?></h1>
    <h1 class="d-block d-md-none display-3"><?=TRANSLATION["thanks"]?></h1>
    <h3><?=TRANSLATION["thanks_sub"]?></h3>
    <p class="lead"><?php printf(TRANSLATION["thanks_email"], '<a href="mailto:' . CONFIG["general"]["contact_email"] . '">' . CONFIG["general"]["contact_email"] . '</a>'); ?></p>
</div>
<div class="bg-light border-0 p-0 col-4 float-right d-none d-sm-block mh-100">
    <img src="<?=BASEURL?>/data/images/spotlight.png" class="spotlight" />
</div>
