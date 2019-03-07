<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>
                <div class="col-12 col-sm-8 p-3 overflow-auto">
                    <h1 class="display-3">Hallo, Max.</h1>
                    <h2>Bestellübersicht</h2>

                    <div class="w-100 bg-primary text-light p-2 mb-3">
                        <h3 class="text-light">Bezahlung: <span class="text-success">Bezahlt</span> <small>(Überwiesen am 01.01.2001)</small></h3>
                        <p class="m-0">Bitte bezahle deine Bestellung bis zum <span class="badge badge-secondary">01.01.2001</span>. Andernfalls werden wir deine Bestellung nicht berücksichtigen.</p>
                    </div>
                    <div class="w-100 p-2 mb-3">
                        <h3>Kontodaten</h3>
                        <table class="table table-borderless table-hover table-sm">
                            <tr>
                                <th>Empfänger</th>
                                <td>Max Mustermann</td>
                            </tr>
                            <tr>
                                <th>IBAN</th>
                                <td>DE12 3456 7890 1234 45</td>
                            </tr>
                            <tr>
                                <th>BIC</th>
                                <td>DEXXXXXXXXXX</td>
                            </tr>
                            <tr>
                                <th>Betrag</th>
                                <td>21,00 €</td>
                            </tr>
                            <tr>
                                <th>Verwendungszweck</th>
                                <td>Abibuch / Max Mustermann / 081415</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="p-2 col-12 col-sm-4 bg-primary text-light overflow-auto">
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
                    <p class="lead">Wenn du noch nicht bezahlt hast, kannst du deine Bestellung hier jederzeit stornieren. Bestell' einfach erneut, wenn du es dir anders überlegst.</p>
                    <a class="btn btn-secondary float-right" href="#">Bestellung stornieren</a>
                </div>
<?php echo $footer; ?>
