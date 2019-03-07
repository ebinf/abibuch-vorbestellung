<?php
    require("./res/glb/global.inc.php");
    echo $header;
?>
                <div class="flex-fill p-3 overflow-auto">
                    <h1 class="display-3">Danke.</h1>
                    <h3>Deine Bestellung wurde erfolgreich entgegengenommen.</h3>
                    <p class="lead">
                        Du solltest in Kürze eine Bestellbestätigung per E-Mail bekommen. In dieser findest du die Kontodaten für die Überweisung sowie einen Link, mit dem du deine Bestellung überprüfen und stornieren kannst.<br />
                        <br />
                        Wenn du keine Mail erhalten solltest, du Fragen oder Probleme hast, wende dich bitte an <a href="mailto:support@ebinf.eu">support@ebinf.eu</a>.
                    </p>
                </div>
                <div class="p-0 col-4 float-right d-none d-sm-block">
                    <img src="./res/img/spotlight.png" class="float-right img-fluid" style="width: 100%; height: 100%;" />
                </div>
<?php echo $footer; ?>
