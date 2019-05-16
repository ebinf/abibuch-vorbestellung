<?php
    return '<h3>Abholung</h3>
    <p>Bitte zeige bei der Abholung den untenstehenden QR-Code vor. Rufe dafür bitte diese Seite bei der Abholung auf oder mache einen Screenshot.</p>
    <div class="text-center">
        <img class="img-fluid" src="{{qr}}{{order:nr}}.{{order:secret}}{{/qr}}">
    </div>';
?>
