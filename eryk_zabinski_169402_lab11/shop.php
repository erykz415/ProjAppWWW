<?php
include('cfg.php');

if (isset($_POST['pokaz_system_kategorii'])) {
    $pokazSystem = true;
} elseif (isset($_POST['ukryj_system_kategorii'])) {
    $pokazSystem = false;
} else {
    $pokazSystem = false;
}

if ($pokazSystem) {
    echo '<h2>System zarządzania kategoriami</h2>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja'])) {
        $akcja = $_POST['akcja'];
        $dane = $_POST;
        ZarzadzajKategoriami($akcja, $dane);
    }

    echo ZarzadzajKategoriami('pokaz');

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Dodaj kategorię</h3>';
    echo '<input type="text" name="nazwa" placeholder="Nazwa kategorii" required>';
    echo '<input type="number" name="matka" placeholder="ID kategorii nadrzędnej (0 dla głównej)" required>';
    echo '<button type="submit" name="akcja" value="dodaj">Dodaj kategorię</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Usuń kategorię</h3>';
    echo '<input type="number" name="id" placeholder="ID kategorii do usunięcia" required>';
    echo '<button type="submit" name="akcja" value="usun">Usuń kategorię</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<h3>Edytuj kategorię</h3>';
    echo '<input type="number" name="id" placeholder="ID kategorii do edytowania" required>';
    echo '<input type="text" name="nazwa" placeholder="Nowa nazwa kategorii" required>';
    echo '<button type="submit" name="akcja" value="edytuj">Edytuj kategorię</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<button type="submit" name="ukryj_system_kategorii">Ukryj system zarządzania kategoriami</button>';
    echo '</form>';
} else {
    echo '<form method="POST">';
    echo '<button type="submit" name="pokaz_system_kategorii">Pokaż system zarządzania kategoriami</button>';
    echo '</form>';
}

function ZarzadzajKategoriami($akcja, $dane = [])
{
    global $link;

    ob_start();

    switch ($akcja) {
        case 'dodaj':
            DodajKategorie($link, $dane['nazwa'], $dane['matka'] ?? 0);
            break;
        case 'usun':
            UsunKategorie($link, $dane['id']);
            break;
        case 'edytuj':
            EdytujKategorie($link, $dane['id'], $dane['nazwa']);
            break;
        case 'pokaz':
            PokazKategorie($link);
            break;
        default:
            echo 'Nieznana akcja.';
    }

    return ob_get_clean();
}

function DodajKategorie($link, $nazwa, $matka = 0)
{
    $stmt = $link->prepare("INSERT INTO sklep (matka, nazwa) VALUES (?, ?)");
    $stmt->bind_param("is", $matka, $nazwa);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Kategoria '$nazwa' została dodana.<br>";
    } else {
        echo "Wystąpił błąd podczas dodawania kategorii.<br>";
    }

    $stmt->close();
}

function UsunKategorie($link, $id)
{
    $stmt = $link->prepare("DELETE FROM sklep WHERE matka = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    $stmt = $link->prepare("DELETE FROM sklep WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Kategoria o ID $id została usunięta.<br>";
    } else {
        echo "Nie znaleziono kategorii o ID $id.<br>";
    }

    $stmt->close();
}

function EdytujKategorie($link, $id, $nazwa)
{
    $stmt = $link->prepare("UPDATE sklep SET nazwa = ? WHERE id = ?");
    $stmt->bind_param("si", $nazwa, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Kategoria o ID $id została zaktualizowana na '$nazwa'.<br>";
    } else {
        echo "Nie znaleziono kategorii o ID $id lub brak zmian.<br>";
    }

    $stmt->close();
}

function PokazKategorie($link)
{
    $query = "SELECT * FROM sklep WHERE matka = 0 ORDER BY nazwa ASC";
    $result = mysqli_query($link, $query);

    echo '<h3>Drzewo kategorii</h3>';
    echo '<ul>';
    while ($kategoria = mysqli_fetch_assoc($result)) {
        echo '<li>' . htmlspecialchars($kategoria['nazwa']) . '</li>';

        $podquery = "SELECT * FROM sklep WHERE matka = " . (int)$kategoria['id'] . " ORDER BY nazwa ASC";
        $podresult = mysqli_query($link, $podquery);

        if (mysqli_num_rows($podresult) > 0) {
            echo '<ul>';
            while ($podkategoria = mysqli_fetch_assoc($podresult)) {
                echo '<li>' . htmlspecialchars($podkategoria['nazwa']) . '</li>';
            }
            echo '</ul>';
        }
    }
    echo '</ul>';

    echo '<h3>Lista kategorii</h3>';
    echo '<table border="1" cellpadding="5" cellspacing="0">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nazwa</th>';
    echo '<th>ID Matki</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $query = "SELECT * FROM sklep ORDER BY matka, nazwa ASC";
    $result = mysqli_query($link, $query);
    while ($kategoria = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>' . $kategoria['id'] . '</td>';
        echo '<td>' . htmlspecialchars($kategoria['nazwa']) . '</td>';
        echo '<td>' . $kategoria['matka'] . '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
}
?>
