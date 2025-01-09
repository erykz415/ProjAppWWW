<?php
include('cfg.php');

if (isset($_POST['pokaz_produkty'])) {
    echo '<h2>System zarządzania produktami</h2>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja'])) {
        $akcja = $_POST['akcja'];
        $dane = $_POST;
        ZarzadzajProduktami($akcja, $dane);
    }

    echo ZarzadzajProduktami('pokaz');

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Dodaj produkt</h3>';
    echo '<input type="text" name="tytul" placeholder="Tytuł produktu" required>';
    echo '<textarea name="opis" placeholder="Opis produktu" required></textarea>';
    echo '<input type="date" name="data_wygasniecia" placeholder="Data wygaśnięcia" required>';
    echo '<input type="number" step="0.01" name="cena_netto" placeholder="Cena netto" required>';
    echo '<input type="number" step="0.01" name="podatek_vat" placeholder="Podatek VAT (%)" required>';
    echo '<input type="number" name="ilosc_sztuk" placeholder="Ilość sztuk" required>';
    echo '<input type="text" name="status_dostepnosci" placeholder="Status dostępności (np. Dostępny/Niedostępny)" required>';
    echo '<input type="text" name="kategoria" placeholder="Kategoria produktu" required>';
    echo '<input type="text" name="gabaryt_produkty" placeholder="Gabaryt produktu" required>';
    echo '<input type="text" name="zdjecie" placeholder="URL do zdjęcia" required>';
    echo '<button type="submit" name="akcja" value="dodaj">Dodaj produkt</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Usuń produkt</h3>';
    echo '<input type="number" name="id" placeholder="ID produktu do usunięcia" required>';
    echo '<button type="submit" name="akcja" value="usun">Usuń produkt</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Edytuj produkt</h3>';
    echo '<input type="number" name="id" placeholder="ID produktu do edycji" required>'; // ID produktu
    echo '<input type="text" name="tytul" placeholder="Nowy tytuł produktu" required>';
    echo '<textarea name="opis" placeholder="Nowy opis produktu" required></textarea>';
    echo '<input type="date" name="data_wygasniecia" placeholder="Nowa data wygaśnięcia" required>';
    echo '<input type="number" step="0.01" name="cena_netto" placeholder="Nowa cena netto" required>';
    echo '<input type="number" step="0.01" name="podatek_vat" placeholder="Nowy podatek VAT (%)" required>';
    echo '<input type="number" name="ilosc_sztuk" placeholder="Nowa ilość sztuk" required>';
    echo '<input type="text" name="status_dostepnosci" placeholder="Nowy status dostępności (np. Dostępny/Niedostępny)" required>';
    echo '<input type="text" name="kategoria" placeholder="Nowa kategoria produktu" required>';
    echo '<input type="text" name="gabaryt_produkty" placeholder="Nowy gabaryt produktu" required>';
    echo '<input type="text" name="zdjecie" placeholder="Nowy URL do zdjęcia" required>';
    echo '<button type="submit" name="akcja" value="edytuj">Edytuj produkt</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<button type="submit" name="ukryj_produkty">Ukryj system zarządzania produktami</button>';
    echo '</form>';

} else {
    echo '<form method="POST">';
    echo '<button type="submit" name="pokaz_produkty">Pokaż system zarządzania produktami</button>';
    echo '</form>';
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
    var_dump($dane);

    if (empty($dane['nazwa'])) {
        echo "Błąd: 'nazwa' jest puste!";
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
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Produkt został dodany.<br>";
    } else {
        echo "Wystąpił błąd podczas dodawania produktu.<br>";
    }

    $stmt->close();
}


function UsunProdukt($link, $id)
{
    $stmt = $link->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Produkt został usunięty.<br>";
    } else {
        echo "Nie znaleziono produktu o ID $id.<br>";
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
    echo '<h3>Lista produktów</h3>';
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
