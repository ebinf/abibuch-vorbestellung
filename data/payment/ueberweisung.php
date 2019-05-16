<?php
    return '<h3>Kontodaten</h3>
    <p>Bitte überweise auf folgendes Konto:</p>
    <table class="table table-borderless table-hover table-sm">
        <tr>
            <th>Empfänger</th>
            <td>JULIAN HEHL ABIBALL 2019 KOPERNIKUSSCHULE</td>
        </tr>
        <tr>
            <th>IBAN</th>
            <td>DE83 5075 0094 0000 0821 45</td>
        </tr>
        <tr>
            <th>BIC</th>
            <td>HELADEF1GEL</td>
        </tr>
        <tr>
            <th>Betrag</th>
            <td>{{order:total_price}}</td>
        </tr>
        <tr>
            <th>Verwendungszweck</th>
            <td>Abibuch / {{general:firstname}} {{general:lastname}} / {{order:nr}}</td>
        </tr>
    </table>';
?>
