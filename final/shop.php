<?php
include('cfg.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

echo '<link rel="stylesheet" href="css/styles.css">';

if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    return;
}

if (isset($_POST['pokaz_system_kategorii'])) {
    $_SESSION['pokaz_system_kategorii'] = true;
} elseif (isset($_POST['ukryj_system_kategorii'])) {
    unset($_SESSION['pokaz_system_kategorii']);
}

echo '<form method="POST">';
echo '<button type="submit" class="custom-button-long-b" name="pokaz_system_kategorii">Zarządzaj kategoriami</button>';
echo '</form>';

if (isset($_SESSION['pokaz_system_kategorii']) && $_SESSION['pokaz_system_kategorii'] === true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja'])) {
        $akcja = $_POST['akcja'];
        $dane = $_POST;
        ZarzadzajKategoriami($akcja, $dane);
    }

    echo ZarzadzajKategoriami('pokaz');

    echo '<h3 class="dodaj-kategorie-header">Dodaj kategorię</h3>';
    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<input type="text" name="nazwa" placeholder="Nazwa kategorii" required>';
    echo '<input type="number" name="matka" placeholder="ID kategorii nadrzędnej (0 dla głównej)" required>';
    echo '<button type="submit" name="akcja" value="dodaj" class="custom-button-add">Dodaj kategorię</button>';
    echo '</form>';

    echo '<h3 class="usun-kategorie-header">Usuń kategorię</h3>';
    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<input type="number" name="id" placeholder="ID kategorii do usunięcia" required>';
    echo '<button type="submit" name="akcja" value="usun" class="custom-button-delete">Usuń kategorię</button>';
    echo '</form>';

    echo '<h3 class="edytuj-kategorie-header">Edytuj kategorię</h3>';
    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<input type="number" name="id" placeholder="ID kategorii do edytowania" required>';
    echo '<input type="text" name="nazwa" placeholder="Nowa nazwa kategorii" required>';
    echo '<button type="submit" name="akcja" value="edytuj" class="custom-button-edit">Edytuj kategorię</button>';
    echo '</form>';

    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<button type="submit" class="custom-button-long-r" name="ukryj_system_kategorii">Ukryj kategorie</button>';
    echo '</form>';
}

function ZarzadzajKategoriami($akcja, $dane = []) {
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

function DodajKategorie($link, $nazwa, $matka = 0) {
    if (empty($nazwa)) {
        echo "Nazwa kategorii nie może być pusta.<br>";
        return;
    }

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

function UsunKategorie($link, $id) {
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

function EdytujKategorie($link, $id, $nazwa) {
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

function PokazKategorie($link) {
    $query = "SELECT * FROM sklep WHERE matka = 0 ORDER BY nazwa ASC";
    $result = mysqli_query($link, $query);

    echo '<h3 class="drzewo-kategorii-header">Drzewo kategorii</h3>';
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

    echo '<h3 class="kategoria-header">Lista kategorii</h3>';
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>ID</th>';
    echo '<th>Nazwa</th>';
    echo '<th>ID Matki</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    $query = "SELECT * FROM sklep ORDER BY id ASC";
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
