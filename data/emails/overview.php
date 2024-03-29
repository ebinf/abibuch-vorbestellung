<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="-webkit-box-sizing: border-box; box-sizing: border-box; --blue: #007bff; --indigo: #6610f2; --purple: #6f42c1; --pink: #e83e8c; --red: #d9534f; --orange: #fd7e14; --yellow: #f0ad4e; --green: #4BBF73; --teal: #20c997; --cyan: #1F9BCF; --white: #fff; --gray: #919aa1; --gray-dark: #343a40; --primary: #1a1a1a; --secondary: #fff; --success: #4BBF73; --info: #1F9BCF; --warning: #f0ad4e; --danger: #d9534f; --light: #fff; --dark: #343a40; --breakpoint-xs: 0; --breakpoint-sm: 576px; --breakpoint-md: 768px; --breakpoint-lg: 992px; --breakpoint-xl: 1200px; --font-family-sans-serif: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; --font-family-monospace: SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace; font-family: sans-serif; line-height: 1.15; -webkit-text-size-adjust: 100%; -webkit-tap-highlight-color: rgba(0,0,0,0);">
    <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bestellbestätigung Nr. {{order:nr}}</title>
    </head>
    <body style="-webkit-box-sizing: border-box; box-sizing: border-box; font-family: 'Nunito Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol'; font-size: 0.875rem; line-height: 1.5; color: #919aa1; text-align: left; background-color: #fff; font-weight: 200; letter-spacing: 1px; margin: 0; padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="-webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse;">
            <tr style="-webkit-box-sizing: border-box; box-sizing: border-box;">
                <td style="-webkit-box-sizing: border-box; box-sizing: border-box;">
                    <h3 style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 0.5rem; font-weight: 600; line-height: 1.2; color: #1a1a1a; font-size: 1.5rem;">Bestellbestätigung <small style="-webkit-box-sizing: border-box; box-sizing: border-box; font-size: 80%; font-weight: 400; color: #919aa1;">(Nr. {{order:nr}})</small></h3>
                </td>
            </tr>
            <tr style="-webkit-box-sizing: border-box; box-sizing: border-box;">
                <td style="-webkit-box-sizing: border-box; box-sizing: border-box;">
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;">Hallo {{general:firstname}},</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; text-align: justify;">vielen Dank für deine Bestellung von {{order:amount}} "{{order:product_name}}" in Höhe von {{order:total_price}}.</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; text-align: justify;">Bitte denke unbedingt daran, deine Bestellung bis zum <b>{{system:pay_till}}</b> zu bezahlen! Tust du das nicht, wird deine Bestellung nicht bearbeitet.</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; text-align: justify;">Die Information, wie du deine Bestellung bezahlen kannst, und auch
                        nochmal eine Übersicht über deine Daten findest du, wenn du unten auf Bestellübersicht klickst.
                        Dort hast du auch die Möglichkeit, deine Bestellung zu stornieren, wenn du es dir anders überlegen solltest.</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; text-align: center;"><a href="{{base_url}}/o/{{order:nr}}/{{order:secret}}" style="-webkit-box-sizing: border-box; box-sizing: border-box; text-decoration: none; display: inline-block; font-weight: 600; text-align: center; vertical-align: middle; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none; border: 0 solid transparent; padding: 0.75rem 2rem; line-height: 1.5rem; border-radius: 0; -webkit-transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out; transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out, -webkit-box-shadow 0.15s ease-in-out; color: #fff; background-color: #1a1a1a; border-color: #1a1a1a; font-size: 0.765625rem; text-transform: uppercase;">Bestellübersicht</a></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; text-align: justify;">Solltest du noch irgendwelche Fragen oder andere Anliegen haben, schreib uns einfach an <a href="mailto:{{system:contact_email}}" style="-webkit-box-sizing: border-box; box-sizing: border-box; color: #1a1a1a; text-decoration: none; background-color: transparent;">{{system:contact_email}}</a>.
                        Damit wir dir schnell helfen können, gib bitte immer deine Bestellnummer {{order:nr}} an.</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;">Dein Team von {{system:title}}</p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem;"></p>
                    <p style="-webkit-box-sizing: border-box; box-sizing: border-box; margin-top: 0; margin-bottom: 1rem; color: #919aa1;"><i>Dies ist eine automatisch versendete E-Mail. Bitte antworte nicht darauf, sondern wende dich an die oben stehende E-Mail-Adresse.</i></p>
                </td>
            </tr>
        </table>
    </body></html>
</html>
