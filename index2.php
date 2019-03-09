<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>
                <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 p-3 overflow-auto">
                    <h1 class="display-3"><?php printf($translation["title"], "Max"); ?></h1>
                    <div class="w-100 bg-primary text-light p-2 mb-3">
                        <table class="w-100">
                            <tr>
                                <td width="70px" class="mr-2">
                                    <img src="./res/img/transparent.png" class="float-left" width="70px" height="70px" />
                                </td>
                                <td>
                                    <p class="m-0"><?=$config["product"]["name"]?><br />
                                    <small><?=$config["product"]["description"]?></small></p>
                                    <p class="mt-2 m-0"><?=money($config["product"]["price"])?></p>
                                </td>
                                <td>
                                    <form class="form-inline float-right">
                                        <select class="form-control" id="sel_abi_qty">
                                            <?php
                                                $cnt = $config["product"]["min"];
                                                while ($cnt <= $config["product"]["max"]) {
                                                    echo '<option value="' . $cnt .'">' . $cnt . " " . $translation["piece"] . '</option>';
                                                    $cnt += 1;
                                                }
                                            ?>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?php

                            if(sizeof($config["payment"]) > 1 || strlen($config["payment"][0]["fieldset"]) > 0) {
                                $buttons = "";
                                $contents = "";
                                foreach($config["payment"] as $payment) {
                                    if (array_search($payment, $config["payment"]) == 0) {
                                        $buttons .= '<a class="nav-item nav-link m-0 active" id="pay_' . $payment["name"] . '-tab" href="#pay_' . $payment["name"] . '" data-toggle="pill" aria-controls="pay_' . $payment["name"] . '" aria-selected="true">' . $payment["description"] . ($payment["price"] > 0 ? " (+" . money($payment["price"]) . ")" : "") . '</a>';
                                        $contents .= '<div class="tab-pane fade show active" id="pay_' . $payment["name"] . '" role="tabpanel" aria-labelledby="pay_' . $payment["name"] . '-tab">';
                                    } else {
                                        $buttons .= '<a class="nav-item nav-link m-0" id="pay_' . $payment["name"] . '-tab" href="#pay_' . $payment["name"] . '" data-toggle="pill" aria-controls="pay_' . $payment["name"] . '" aria-selected="false">' . $payment["description"] . ($payment["price"] > 0 ? " (+" . money($payment["price"]) . ")" : "") . '</a>';
                                        $contents .= '<div class="tab-pane fade" id="pay_' . $payment["name"] . '" role="tabpanel" aria-labelledby="pay_' . $payment["name"] . '-tab">';
                                    }
                                    if (strlen($payment["fieldset"]) > 0 && key_exists($payment["fieldset"], $config["fields"]["payment"])) {
                                        $contents .= '<div class="form-row">';
                                        foreach($config["fields"]["payment"][$payment["fieldset"]] as $field) {
                                            $contents .= ext_field($field);
                                        }
                                        $contents .= '</div>';
                                    }
                                    $contents .= '</div>';
                                }
                                echo '<div class="nav flex-column nav-pills w-100" aria-orientation="vertical" role="tablist">';
                                echo $buttons;
                                echo '</div>';
                                echo '<div class="col-10 offset-1 m-2"><div class="tab-content" id="tbs_pay">';
                                echo $contents;
                                echo '</div></div>';
                            }

                            if((sizeof($config["payment"]) > 1  || strlen($config["payment"][0]["fieldset"]) > 0) && (sizeof($config["delivery"]) > 1 || strlen($config["delivery"][0]["fieldset"]) > 0)) {
                                echo "<hr />";
                            }

                            if(sizeof($config["delivery"]) > 1 || strlen($config["delivery"][0]["fieldset"]) > 0) {
                                $buttons = "";
                                $contents = "";
                                foreach($config["delivery"] as $delivery) {
                                    if (array_search($delivery, $config["delivery"]) == 0) {
                                        $buttons .= '<a class="nav-item nav-link m-0 active" id="del_' . $delivery["name"] . '-tab" href="#del_' . $delivery["name"] . '" data-toggle="pill" aria-controls="del_' . $delivery["name"] . '" aria-selected="true">' . $delivery["description"] . ($delivery["price"] > 0 ? " (+" . money($delivery["price"]) . ")" : "") . '</a>';
                                        $contents .= '<div class="tab-pane fade show active" id="del_' . $delivery["name"] . '" role="tabpanel" aria-labelledby="del_' . $delivery["name"] . '-tab">';
                                    } else {
                                        $buttons .= '<a class="nav-item nav-link m-0" id="del_' . $delivery["name"] . '-tab" href="#del_' . $delivery["name"] . '" data-toggle="pill" aria-controls="del_' . $delivery["name"] . '" aria-selected="false">' . $delivery["description"] . ($delivery["price"] > 0 ? " (+" . money($delivery["price"]) . ")" : "") . '</a>';
                                        $contents .= '<div class="tab-pane fade" id="del_' . $delivery["name"] . '" role="tabpanel" aria-labelledby="del_' . $delivery["name"] . '-tab">';
                                    }
                                    if (strlen($delivery["fieldset"]) > 0 && key_exists($delivery["fieldset"], $config["fields"]["delivery"])) {
                                        $contents .= '<div class="form-row">';
                                        foreach($config["fields"]["delivery"][$delivery["fieldset"]] as $field) {
                                            $contents .= ext_field($field);
                                        }
                                        $contents .= '</div>';
                                    }
                                    $contents .= '</div>';
                                }
                                echo '<div class="nav flex-column nav-pills w-100" aria-orientation="vertical" role="tablist">';
                                echo $buttons;
                                echo '</div>';
                                echo '<div class="col-10 offset-1 m-2"><div class="tab-content" id="tbs_del">';
                                echo $contents;
                                echo '</div></div>';
                            }
                        ?>
                    <p class="text-center d-block d-sm-none"><i class="fa fa-angle-down fa-3x" aria-hidden="true"></i></p>
                </div>
                <div class="p-2 col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 bg-primary text-light overflow-auto">
                    <h3 class="text-light"><?=$translation["order"]?></h3>
                    <table class="w-100">
                        <tr>
                            <td style="text-align: right;" id="lbl_abi_qty">1<td>
                            <td id="lbl_abi_plr"><?=$config["product"]["name"]?></td>
                            <td style="text-align: right;" id="lbl_abi_pre">16,00 €</td>
                        </tr>
                        <tr class="border-top">
                            <td><td>
                            <td><?=$translation["total"]?></td>
                            <td style="text-align: right;" id="lbl_ges_pre">16,00 €</td>
                        </tr>
                    </table>
                    <br />
                    <h3 class="text-light"><?=$translation["delivery"]?></h3>
                    <p id="lbl_ver_met">Abholung bei der akademischen Feier</p>
                    <h3 class="text-light"><?=$translation["payment"]?></h3>
                    <p>Überweisung</p>
                    <h3 class="text-light"><?=$translation["email"]?></h3>
                    <p>max.mustermann@example.com</p>
                    <hr class="bg-light" />
                    <p class="lead"><?=$translation["place_order_lead"]?></p>
                    <form action="index3.php" method="post">
                        <input type="submit" class="btn btn-secondary float-right" value="<?=$translation["place_order"]?>">
                    </form>
                </div>
                <script></script>
<?php echo $footer; ?>
