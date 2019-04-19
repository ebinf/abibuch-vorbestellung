<?php

    if (count(get_included_files()) == 1) {
        header("Location: " . (strlen(RELPATH) == 0 ? "/" : RELPATH));
        exit;
    }

?>
<div class="col-12 col-sm-8 bg-light border-0 cont">
    <h1 class="display-3"><?=TRANSLATION["welcome_title"]?></h1>
    <h3><?=TRANSLATION["welcome_content"]?></h3>
    <?php

        $buttons = "";
        $fieldsets = [];
        $globalfields = "";

        foreach(CONFIG["types"] as $type) {
            $buttons .= '<a class="btn btn-primary" data-toggle="collapse" href="#cps_' . $type["name"] . '" role="button" aria-expanded="false" aria-controls="cps_' . $type["name"] . '">' . $type["title"] . '</a>' . PHP_EOL;
            $fieldsets[$type["name"]] = '';
            foreach(CONFIG["fields"]["types"][$type["name"]] as $field) {
                $fieldsets[$type["name"]] .= ext_field($field);
            }
        }

        foreach(CONFIG["fields"]["general"] as $field) {
            $globalfields .= ext_field($field);
        }

        if (sizeof(CONFIG["types"]) > 1) {
            echo $buttons;
            echo '<div class="accordion pt-3" id="acd_tps">';
            foreach(CONFIG["types"] as $type) {
                echo '<div class="collapse" id="cps_' . $type["name"] . '" data-parent="#acd_tps">
                <form action="' . RELPATH . '/checkout" method="post"><div class="form-row">';
                echo $globalfields;
                echo $fieldsets[$type["name"]];
                echo '<div class="form-group col-md-3"><button type="submit" class="btn btn-primary btn-block">' . TRANSLATION["next"] . '</button></div>';;
                echo '</div>
                <input type="hidden" name="typ_type" value="' . $type["name"] . '" />
        </form>
    </div>';
            }
            echo '</div>';
        } else {
            echo '<form class="pt-3" action="' . RELPATH . '/checkout" method="post"><div class="form-row">';
            echo $globalfields;
            echo $fieldsets[CONFIG["types"][0]["name"]];
            echo '<div class="form-group col-md-3"><button type="submit" class="btn btn-primary btn-block">' . TRANSLATION["next"] . '</button></div>
            </div></form>';
        }
    ?>
</div>
<div class="bg-light border-0 p-0 col-4 float-right d-none d-sm-block mh-100">
    <img src="<?=BASEURL?>/data/images/spotlight.png" class="spotlight" />
</div>
