<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

?>
<div class="col-12 col-sm-8 p-3 bg-light border-0 cont">
    <h1 class="d-none d-md-block display-3"><?=TRANSLATION["welcome_title"]?></h1>
    <h1 class="d-block d-md-none display-4"><?=TRANSLATION["welcome_title"]?></h1>
    <div class="card text-white bg-warning mb-3">
        <div class="card-body">
            <h4 class="card-title"><?=TRANSLATION["alerts"]["no_orders_title"]?></h4>
            <p class="card-text"><?=sprintf(TRANSLATION["alerts"]["no_orders_message"], "<b>" . date(TRANSLATION["date_time_format"]["date_long"], strtotime(CONFIG["general"]["order_till"])) . "</b>")?></p>
        </div>
    </div>
</div>
<div class="bg-light border-0 p-0 col-4 float-right d-none d-sm-block mh-100">
    <img src="<?=BASEURL?>/data/images/spotlight.png" class="spotlight" />
</div>
