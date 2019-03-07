<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>
                <div class="col-12 col-sm-8 col-md-8 col-lg-8 col-xl-8 p-3 overflow-auto">
                    <h1 class="display-3">Hallo, Max.</h1>
                    <div class="w-100 bg-primary text-light p-2 mb-3">
                        <table class="w-100">
                            <tr>
                                <td width="70px" class="mr-2">
                                    <img src="./res/img/transparent.png" class="float-left" width="70px" height="70px" />
                                </td>
                                <td>
                                    <p class="m-0">Abibuch<br />
                                    <small>300 Seiten, Hardcover</small></p>
                                    <p class="mt-2 m-0">16,00 €</p>
                                </td>
                                <td>
                                    <form class="form-inline float-right">
                                        <select class="form-control">
                                            <option>1 Stück</option>
                                            <option>2 Stück</option>
                                            <option>3 Stück</option>
                                            <option>4 Stück</option>
                                            <option>5 Stück</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="nav flex-column nav-pills w-100" aria-orientation="vertical" role="tablist">
                        <a class="nav-item nav-link m-0 active" href="#v-pills-home" data-toggle="pill" aria-controls="v-pills-home" aria-selected="true">Ich möchte mein Abibuch bei der akademischen Feier abholen.</a>
                        <a class="nav-item nav-link m-0" href="#v-pills-profile" data-toggle="pill" aria-controls="v-pills-profile" aria-selected="false">Mein Abibuch soll in mein Fach gelegt werden.</a>
                        <a class="nav-item nav-link m-0" href="#v-pills-messages" data-toggle="pill" aria-controls="v-pills-messages" aria-selected="false">Ich möchte mein Abibuch per Post zugesendet bekommen. (+5,00 €)</a>
                    </div>
                    <div class="col-10 offset-1 m-2">
                        <div class="tab-content" id="v-pills-tabContent">
                            <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab"></div>
                            <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab"></div>
                            <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                <h3>Lieferadresse</h3>
                                <form class="w-100">
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <input type="text" class="form-control" id="inputAddress" placeholder="Straße und Hausnummer">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <input type="text" class="form-control" id="inputAddress2" placeholder="Adresszusatz">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-4">
                                            <input type="text" class="form-control" id="inputZip" placeholder="PLZ" maxlength="5">
                                        </div>
                                        <div class="form-group col-md-8">
                                            <input type="text" class="form-control" id="inputCity" placeholder="Stadt">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4 bg-primary text-light overflow-auto">
                    <h3 class="text-light">Bestellung</h3>
                    <table class="w-100">
                        <tr>
                            <td style="text-align: right;">3<td>
                            <td>Abibücher</td>
                            <td style="text-align: right;">48,00 €</td>
                        </tr>
                        <tr>
                            <td><td>
                            <td>Rabatt</td>
                            <td style="text-align: right;">-32,00 €</td>
                        </tr>
                        <tr>
                            <td><td>
                            <td>Versand</td>
                            <td style="text-align: right;">5,00 €</td>
                        </tr>
                        <tr class="border-top">
                            <td><td>
                            <td>GESAMT</td>
                            <td style="text-align: right;">21,00 €</td>
                        </tr>
                    </table>
                    <br />
                    <h3 class="text-light">Lieferung an</h3>
                    <p>Max Mustermann<br />
                    Erdgeschoss<br />
                    Musterstraße 1a<br />
                    12345 Musterstadt</p>
                    <h3 class="text-light">Bezahl&shy;methode</h3>
                    <p>Überweisung</p>
                    <h3 class="text-light">E-Mail-Adresse</h3>
                    <p>max.mustermann@example.com</p>
                    <hr class="bg-light" />
                    <p class="lead">Bitte stell' sicher, dass alle Angaben korrekt sind, bevor du bestellst!</p>
                    <a class="btn btn-secondary float-right" href="index3.php">Kostenpflichtig bestellen</a>
                </div>
<?php echo $footer; ?>
