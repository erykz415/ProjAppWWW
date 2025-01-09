<?php
// Włącz raportowanie błędów i wyświetlanie na ekranie
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Główna logika decydująca o tym, która funkcja zostanie wywołana
if (isset($_POST['show_remind_form'])) {
    // Obsługa przypomnienia hasła
    PrzypomnijHaslo();
} elseif (isset($_POST['submit_contact'])) {
    // Obsługa formularza kontaktowego
    WyslijMailKontakt("odbiorca@przyklad.pl");
} else {
    // Wyświetlenie przycisku i ukrytego formularza kontaktowego
    echo '
    <button id="showContactForm" style="margin: 20px; padding: 10px 20px;">Pokaż formularz kontaktowy</button>
    <div id="contact-form-container" style="display: none; margin-top: 20px;">
        ' . PokazKontakt() . '
    </div>
    <script>
        document.getElementById("showContactForm").addEventListener("click", function() {
            document.getElementById("contact-form-container").style.display = "block";
            this.style.display = "none"; // Ukryj przycisk po kliknięciu
        });
    </script>';
}

// ----------------------------------------------------
// Funkcja: Wyświetlanie formularza kontaktowego
// ----------------------------------------------------
function PokazKontakt()
{
    // Zwraca HTML dla formularza kontaktowego
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

// ----------------------------------------------------
// Funkcja: Wysyłanie maila kontaktowego
// ----------------------------------------------------
function WyslijMailKontakt($odbiorca)
{
    if (isset($_POST['submit_contact'])) {
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo 'Nie wypełniłeś wszystkich pól.';
            echo PokazKontakt(); // Ponowne wyświetlenie formularza w przypadku błędu
            return;
        }

        $temat = htmlspecialchars($_POST['temat']);
        $tresc = htmlspecialchars($_POST['tresc']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Podaj poprawny adres e-mail.';
            echo PokazKontakt(); // Ponowne wyświetlenie formularza w przypadku błędu
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

// ----------------------------------------------------
// Funkcja: Przypominanie hasła
// ----------------------------------------------------
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
