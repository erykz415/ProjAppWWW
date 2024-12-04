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
    // Wyświetlenie formularza kontaktowego
    echo PokazKontakt();
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
    // Sprawdzanie, czy formularz kontaktowy został wysłany
    if (isset($_POST['submit_contact'])) {

        // Sprawdzanie, czy wszystkie wymagane pola zostały wypełnione
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo 'Nie wypełniłeś wszystkich pól.';
            echo PokazKontakt(); // Ponowne wyświetlenie formularza w przypadku błędu
            return;
        }

        // Zabezpieczenie przed atakami XSS
        $temat = htmlspecialchars($_POST['temat']);
        $tresc = htmlspecialchars($_POST['tresc']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        // Walidacja adresu e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Podaj poprawny adres e-mail.';
            echo PokazKontakt(); // Ponowne wyświetlenie formularza w przypadku błędu
            return;
        }

        // Przygotowanie nagłówków maila
        $headers = "From: Formularz kontaktowy <$email>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        // Wysłanie wiadomości e-mail
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
    // Sprawdzanie, czy formularz przypomnienia hasła został wysłany
    if (isset($_POST['submit_remind'])) {

        // Zabezpieczenie i walidacja adresu e-mail
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo 'Podaj poprawny adres e-mail.';
            return;
        }

        // Przykładowe dane do wysłania w wiadomości
        $haslo = 'twoje_haslo123';
        $temat = 'Przypomnienie hasła';
        $tresc = "Twoje hasło do panelu admina: $haslo";

        // Przygotowanie nagłówków maila
        $headers = "From: no-reply@twojastrona.pl\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

        // Wysłanie wiadomości e-mail z przypomnieniem hasła
        if (mail($email, $temat, $tresc, $headers)) {
            echo 'Na Twój adres e-mail zostało wysłane przypomnienie hasła.';
        } else {
            echo 'Wystąpił problem z wysłaniem maila.';
        }
    } else {
        // Wyświetlenie formularza przypomnienia hasła
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
