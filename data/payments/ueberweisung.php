<?php
    return '<h3>Kontodaten</h3>
    <p>Bitte überweise auf folgendes Konto:</p>
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
            <td>{{order:total_price}}</td>
        </tr>
        <tr>
            <th>Verwendungszweck</th>
            <td>Abibuch / {{general:firstname}} {{general:lastname}} / {{order:nr}}</td>
        </tr>
    </table>';
?>
