<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

?>
<div class="col-12 col-sm-8 p-3 bg-light border-0 cont">
    <h1 class="display-3"><?=TRANSLATION["welcome_title"]?></h1>
    <h3><?=TRANSLATION["welcome_content"]?></h3>
    <div class="card text-white bg-danger mb-3">
        <div class="card-body">
            <h4 class="card-title"><?=TRANSLATION["alerts"]["noscript_title"]?></h4>
            <p class="card-text"><?=TRANSLATION["alerts"]["noscript_message"]?></p>
        </div>
    </div>
</div>
<div class="bg-light border-0 p-0 col-4 float-right d-none d-sm-block mh-100">
    <img src="<?=BASEURL?>/data/images/spotlight.png" class="spotlight" />
</div>
<script>
<!--
    $(document).ready(function () {
        $(location).attr("href", "<?=(strlen(RELPATH) == 0 ? "/" : RELPATH)?>");
    });
-->
</script>
