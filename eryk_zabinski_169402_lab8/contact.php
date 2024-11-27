<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['show_remind_form'])) {
    PrzypomnijHaslo();
} elseif (isset($_POST['submit_contact'])) {
    WyslijMailKontakt("odbiorca@przyklad.pl");
} else {
    echo PokazKontakt();
}

function PokazKontakt()
{
    return '
    <div id="contact-form">
        <h2>Formularz kontaktowy</h2>
        <form method="POST">
            <label for="temat">Temat:</label><br>
            <input type="text" id="temat" name="temat" required><br><br>

            <label for="tresc">Treść:</label><br>
            <textarea id="tresc" name="tresc" required></textarea><br><br>

            <label for="email">Twój adres e-mail:</label><br>
            <input type="email" id="email" name="email" required><br><br>

            <button type="submit" name="submit_contact">Wyślij</button>
            <button type="submit" name="show_remind_form">Przypomnij hasło</button>
        </form>
    </div>';
}

function WyslijMailKontakt($odbiorca)
{
    if (isset($_POST['submit_contact'])) {
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo 'Nie wypełniłeś wszystkich pól.';
            echo PokazKontakt(); 
            return;
        }

        $temat = htmlspecialchars($_POST['temat']);
        $tresc = htmlspecialchars($_POST['tresc']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Podaj poprawny adres e-mail.';
            echo PokazKontakt(); 
            return;
        }

        $headers = "From: Formularz kontaktowy <$email>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        if (mail($odbiorca, $temat, $tresc, $headers)) {
            echo 'Wiadomość została wysłana.';
        } else {
            echo 'Wystąpił błąd podczas wysyłania wiadomości.';
        }
    }
}

function PrzypomnijHaslo()
{
    if (isset($_POST['submit_remind'])) {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Podaj poprawny adres e-mail.';
            return;
        }

        $haslo = 'twoje_haslo123';
        $temat = 'Przypomnienie hasła';
        $tresc = "Twoje hasło do panelu admina: $haslo";

        $headers = "From: no-reply@twojastrona.pl\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        if (mail($email, $temat, $tresc, $headers)) {
            echo 'Na Twój adres e-mail zostało wysłane przypomnienie hasła.';
        } else {
            echo 'Wystąpił problem z wysłaniem maila.';
        }
    } else {
        echo '
        <div id="remind-form">
            <h2>Przypomnij hasło</h2>
            <form method="POST">
                <label for="email">Twój adres e-mail:</label><br>
                <input type="email" id="email" name="email" required><br><br>
                <input type="submit" name="submit_remind" value="Przypomnij hasło">
            </form>
        </div>';
    }
}
?>
