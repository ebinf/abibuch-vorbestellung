<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>

                <div class="flex-fill p-3 overflow-auto">
                    <h1 class="display-3"><?php echo $translation["welcome_title"]; ?></h1>
                    <h3><?php echo $translation["welcome_content"]; ?></h3>
                    <?php

                        $buttons = "";
                        $fieldsets = [];

                        foreach($type in $config["types"]) {
                            $buttons .= '<a class="btn btn-primary" data-toggle="collapse" href="#cps_' . $type["name"] . '" role="button" aria-expanded="false" aria-controls="cps_' . $type["name"] . '">' . $type["title"] . '</a>';
                            $fieldsets[$type["name"]] = '';
                            foreach($field in $config["fields"]["types"][$type["name"]]) {
                                if ($field["type"] == "text" || $field["type"] == "password" || $field["type"] == "email") {
                                    $fieldsets[$type["name"]] .= '<input type="' . $field["type"] . '" class="form-control col-md-' . $field["width"] . '" name="inp_' . $field["name"] . '" placeholder="' . $field["title"] . '">';
                                } elseif ($field["type"] == "select") {
                                    $fieldsets[$type["name"]] .= '<select id="inp_' . $field["name"] . '" class="form-control col-md-' . $field["width"] . '" required>
                                        <option selected disabled value="">' . $field["title"] . '</option>';
                                    foreach ($option in $field["options"]) {
                                        $fieldsets[$type["name"]] .= '<option>' . $option . '</option>';
                                    }
                                    $fieldsets[$type["name"]] .= '</select>';
                                }
                            }
                        }

                        if (sizeof($config["types"]) > 1) {
                            echo $buttons;
                            echo '<div class="accordion pt-3" id="acd_tps">';
                            foreach($type in $config["types"]) {
                                echo '<div class="collapse' . $type["name"] . '" id="cps_" data-parent="#acd_tps">
                                <form action="index2.php" method="post">';
                                echo $fieldsets[$type["name"]];
                                echo '<button type="submit" class="form-group col-md-2 btn btn-primary">' . $translation["next"] . '</button>
                        </form>
                    </div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<form class="pt-3" action="index2.php" method="post">';
                            echo $fieldsets;
                            echo '<button type="submit" class="form-group col-md-2 btn btn-primary">' . $translation["next"] . '</button>';
                        }
                    ?>
                    <!--<a class="btn btn-primary" data-toggle="collapse" href="#collapseSuS" role="button" aria-expanded="false" aria-controls="collapseSuS">Schüler/in</a>
                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseLuL" role="button" aria-expanded="false" aria-controls="collapseLuL">Lehrkraft</a>
                    <div class="accordion pt-3" id="acd_tps">
                        <div class="collapse" id="collapseSuS" data-parent="#accordionSuSLuL">
                            <form action="index2.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="text" class="form-control" id="inputEmail4" placeholder="Vorname" required>
                                    </div>
                                    <div class="form-group col-md-6 pr-0">
                                        <input type="text" class="form-control" id="inputPassword4" placeholder="Nachname" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="email" class="form-control" id="inputEmail4" placeholder="E-Mail-Adresse" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <select id="inputState" class="form-control" required>
                                            <option selected disabled value="">Tutorium</option>
                                            <option>Q3/4a</option>
                                            <option>Q3/4b</option>
                                            <option>Q3/4c</option>
                                            <option>Q3/4d</option>
                                            <option>Q3/4e</option>
                                            <option>Q3/4f</option>
                                            <option>Q3/4g</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="form-group col-md-2 btn btn-primary">Weiter</button>
                                </div>
                            </form>
                        </div>
                        <div class="collapse" id="collapseLuL" data-parent="#accordionSuSLuL">
                            <form action="index2.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="text" class="form-control" id="inputEmail4" placeholder="Vorname" required>
                                    </div>
                                    <div class="form-group col-md-6 pr-0">
                                        <input type="text" class="form-control" id="inputPassword4" placeholder="Nachname" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <input type="email" class="form-control" id="inputEmail4" placeholder="E-Mail-Adresse" required>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <input type="text" class="form-control" id="inputPassword4" placeholder="Kürzel" maxlength="4" required>
                                    </div>
                                    <button type="submit" class="form-group col-md-2 btn btn-primary">Weiter</button>
                                </div>
                            </form>
                        </div>
                    </div>-->
                </div>
                <div class="p-0 col-4 float-right d-none d-sm-block">
                    <img src="./res/img/spotlight.png" class="float-right img-fluid" style="width: 100%; height: 100%;" />
                </div>
<?php echo $footer; ?>
