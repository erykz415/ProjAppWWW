<?php
include('cfg.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
echo '<link rel="stylesheet" href="css/styles.css">';

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true) {
    echo '<form method="POST">';
    echo '<button type="submit" name="pokaz_produkty" class="custom-button-long-b">Zarządzaj produktami</button>';
    echo '</form>';
} else {
    return;
}

if (isset($_POST['pokaz_produkty']) || isset($_POST['akcja'])) {
    echo ZarzadzajProduktami('pokaz');

    echo '<form method="POST" style="margin-top: 20px;">
        <h3 class="dodaj-kategorie-header">Dodaj produkt</h3>
        <input type="text" name="tytul" placeholder="Tytuł produktu" required>
        <textarea name="opis" placeholder="Opis produktu" required></textarea>
        <input type="date" name="data_wygasniecia" placeholder="Data wygaśnięcia" required>
        <input type="number" step="0.01" name="cena_netto" placeholder="Cena netto" required>
        <input type="number" step="0.01" name="podatek_vat" placeholder="Podatek VAT (%)" required>
        <input type="number" name="ilosc_sztuk" placeholder="Ilość sztuk" required>
        <select name="status_dostepnosci" required>
            <option value="Dostępny">Dostępny</option>
            <option value="Niedostępny">Niedostępny</option>
        </select>
        <input type="text" name="kategoria" placeholder="Kategoria produktu" required>
        <input type="text" name="gabaryt_produkty" placeholder="Gabaryt produktu" required>
        <input type="text" name="zdjecie" placeholder="URL do zdjęcia" required>
        <button type="submit" name="akcja" value="dodaj" class="custom-button-add">Dodaj produkt</button>
    </form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja']) && $_POST['akcja'] === 'dodaj') {
        ZarzadzajProduktami('dodaj', $_POST);
    }

    echo '<form method="POST" style="margin-top: 20px;">
        <h3 class="usun-kategorie-header">Usuń produkt</h3>
        <input type="number" name="id" placeholder="ID produktu do usunięcia" required>
        <button type="submit" name="akcja" value="usun" class="custom-button-delete">Usuń produkt</button>
    </form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja']) && $_POST['akcja'] === 'usun') {
        if (!empty($_POST['id'])) {
            ZarzadzajProduktami('usun', $_POST);
        } else {
            echo "Proszę podać ID produktu do usunięcia.<br>";
        }
    }

    echo '<form method="POST" style="margin-top: 20px;">
        <h3 class="edytuj-kategorie-header">Edytuj produkt</h3>
        <input type="number" name="id" placeholder="ID produktu do edycji" required>
        <input type="text" name="tytul" placeholder="Nowy tytuł produktu" required>
        <textarea name="opis" placeholder="Nowy opis produktu" required></textarea>
        <input type="date" name="data_wygasniecia" placeholder="Nowa data wygaśnięcia" required>
        <input type="number" step="0.01" name="cena_netto" placeholder="Nowa cena netto" required>
        <input type="number" step="0.01" name="podatek_vat" placeholder="Nowy podatek VAT (%)" required>
        <input type="number" name="ilosc_sztuk" placeholder="Nowa ilość sztuk" required>
        <select name="status_dostepnosci" required>
            <option value="Dostępny">Dostępny</option>
            <option value="Niedostępny">Niedostępny</option>
        </select>
        <input type="text" name="kategoria" placeholder="Nowa kategoria produktu" required>
        <input type="text" name="gabaryt_produkty" placeholder="Nowy gabaryt produktu" required>
        <input type="text" name="zdjecie" placeholder="Nowy URL do zdjęcia" required>
        <button type="submit" name="akcja" value="edytuj" class="custom-button-edit">Edytuj produkt</button>
    </form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja']) && $_POST['akcja'] === 'edytuj') {
        ZarzadzajProduktami('edytuj', $_POST);
    }

    echo '<form method="POST" style="margin-top: 20px;">
        <button type="submit" name="ukryj_produkty" class="custom-button-long-r">Ukryj produkty</button>
    </form>';

    if (isset($_POST['ukryj_produkty'])) {
        unset($_POST['pokaz_produkty']);
    }
}

function ZarzadzajProduktami($akcja, $dane = [])
{
    global $link;

    ob_start();

    switch ($akcja) {
        case 'dodaj':
            DodajProdukt($link, $dane);
            break;
        case 'usun':
            UsunProdukt($link, $dane['id']);
            break;
        case 'edytuj':
            EdytujProdukt($link, $dane);
            break;
        case 'pokaz':
            PokazProdukty($link);
            break;
        default:
            echo 'Nieznana akcja.';
    }

    return ob_get_clean();
}

function DodajProdukt($link, $dane)
{
    $stmt = $link->prepare("SELECT COUNT(*) FROM produkty WHERE tytul = ?");
    $stmt->bind_param("s", $dane['tytul']);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "Produkt o tytule '" . htmlspecialchars($dane['tytul']) . "' już istnieje w bazie danych.<br>";
        return;
    }

    if (empty($dane['tytul'])) {
        echo "Błąd: 'tytul' jest pusty!<br>";
        return;
    }

    $stmt = $link->prepare("INSERT INTO produkty (tytul, opis, data_wygasniecia, cena_netto, podatek_vat, ilosc_sztuk, status_dostepnosci, kategoria, gabaryt_produkty, zdjecie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "sssdiissss",
        $dane['tytul'],
        $dane['opis'],
        $dane['data_wygasniecia'],
        $dane['cena_netto'],
        $dane['podatek_vat'],
        $dane['ilosc_sztuk'],
        $dane['status_dostepnosci'],
        $dane['kategoria'],
        $dane['gabaryt_produkty'],
        $dane['zdjecie']
    );

    if ($stmt->execute()) {
        echo "Produkt został dodany.<br>";
    } else {
        echo "Wystąpił błąd podczas dodawania produktu: " . $stmt->error . "<br>";
    }

    $stmt->close();
}

function UsunProdukt($link, $id)
{
    if (empty($id)) {
        echo "Nie podano ID do usunięcia.<br>";
        return;
    }

    $stmt = $link->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Produkt został usunięty.<br>";
    } else {
        echo "Nie znaleziono produktu o ID $id lub wystąpił błąd podczas usuwania.<br>";
    }

    $stmt->close();
}

function EdytujProdukt($link, $dane)
{
    $stmt = $link->prepare("UPDATE produkty SET tytul = ?, opis = ?, data_wygasniecia = ?, cena_netto = ?, podatek_vat = ?, ilosc_sztuk = ?, status_dostepnosci = ?, kategoria = ?, gabaryt_produkty = ?, zdjecie = ? WHERE id = ?");
    $stmt->bind_param(
        "sssdiissssi",
        $dane['tytul'],
        $dane['opis'],
        $dane['data_wygasniecia'],
        $dane['cena_netto'],
        $dane['podatek_vat'],
        $dane['ilosc_sztuk'],
        $dane['status_dostepnosci'],
        $dane['kategoria'],
        $dane['gabaryt_produkty'],
        $dane['zdjecie'],
        $dane['id']
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Produkt został zaktualizowany.<br>";
    } else {
        echo "Nie znaleziono produktu lub brak zmian.<br>";
    }

    $stmt->close();
}

function PokazProdukty($link)
{
    $result = mysqli_query($link, "SELECT * FROM produkty ORDER BY data_utworzenia ASC");
    echo '<h3 class="kategoria-header">Lista produktów</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Tytuł</th>';
    echo '<th>Opis</th>';
    echo '<th>Data Utworzenia</th>';
    echo '<th>Data Modyfikacji</th>';
    echo '<th>Data Wygaśnięcia</th>';
    echo '<th>Cena Netto</th>';
    echo '<th>VAT</th>';
    echo '<th>Ilość</th>';
    echo '<th>Status</th>';
    echo '<th>Kategoria</th>';
    echo '<th>Gabaryt</th>';
    echo '<th>Zdjęcie</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($produkt = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $produkt['id'] . '</td>';
        echo '<td>' . htmlspecialchars($produkt['tytul']) . '</td>';
        echo '<td>' . htmlspecialchars($produkt['opis']) . '</td>';
        echo '<td>' . $produkt['data_utworzenia'] . '</td>';
        echo '<td>' . $produkt['data_modyfikacji'] . '</td>';
        echo '<td>' . $produkt['data_wygasniecia'] . '</td>';
        echo '<td>' . $produkt['cena_netto'] . '</td>';
        echo '<td>' . $produkt['podatek_vat'] . '%</td>';
        echo '<td>' . $produkt['ilosc_sztuk'] . '</td>';
        echo '<td>' . htmlspecialchars($produkt['status_dostepnosci']) . '</td>';
        echo '<td>' . htmlspecialchars($produkt['kategoria']) . '</td>';
        echo '<td>' . htmlspecialchars($produkt['gabaryt_produkty']) . '</td>';
        echo '<td><img src="' . htmlspecialchars($produkt['zdjecie']) . '" alt="Zdjęcie produktu" style="max-width: 100px;"></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
?>
