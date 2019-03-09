<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>

                <div class="flex-fill p-3 overflow-auto">
                    <h1 class="display-3"><?=$translation["welcome_title"]?></h1>
                    <h3><?=$translation["welcome_content"]?></h3>
                    <?php

                        $buttons = "";
                        $fieldsets = [];
                        $globalfields = "";

                        foreach($config["types"] as $type) {
                            $buttons .= '<a class="btn btn-primary" data-toggle="collapse" href="#cps_' . $type["name"] . '" role="button" aria-expanded="false" aria-controls="cps_' . $type["name"] . '">' . $type["title"] . '</a>' . PHP_EOL;
                            $fieldsets[$type["name"]] = '';
                            foreach($config["fields"]["types"][$type["name"]] as $field) {
                                $fieldsets[$type["name"]] .= ext_field($field);
                            }
                        }

                        foreach($config["fields"]["general"] as $field) {
                            $globalfields .= ext_field($field);
                        }

                        if (sizeof($config["types"]) > 1) {
                            echo $buttons;
                            echo '<div class="accordion pt-3" id="acd_tps">';
                            foreach($config["types"] as $type) {
                                echo '<div class="collapse" id="cps_' . $type["name"] . '" data-parent="#acd_tps">
                                <form action="index2.php" method="post"><div class="form-row">';
                                echo $globalfields;
                                echo $fieldsets[$type["name"]];
                                echo '<div class="form-group col-md-3"><button type="submit" class="btn btn-primary btn-block">' . $translation["next"] . '</button></div>';;
                                echo '</div>
                        </form>
                    </div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<form class="pt-3" action="index2.php" method="post"><div class="form-row">';
                            echo $globalfields;
                            echo $fieldsets[$config["types"][0]["name"]];
                            echo '<div class="form-group col-md-3"><button type="submit" class="btn btn-primary btn-block">' . $translation["next"] . '</button></div>
                            </div></form>';
                        }
                    ?>
                </div>
                <div class="p-0 col-4 float-right d-none d-sm-block">
                    <img src="./res/img/spotlight.png" class="float-right img-fluid" style="width: 100%; height: 100%;" />
                </div>
<?php echo $footer; ?>
