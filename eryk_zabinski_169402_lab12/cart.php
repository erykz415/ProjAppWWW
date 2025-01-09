<?php
include('cfg.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja'])) {
    $akcja = $_POST['akcja'];

    if (isset($_POST['id'])) {
        $id_produktu = $_POST['id'];
    } else {
        $id_produktu = null;
    }

    switch ($akcja) {
        case 'dodaj':
            if ($id_produktu !== null) {
                DodajDoKoszyka($id_produktu);
            }
            break;
        case 'usun':
            if ($id_produktu !== null) {
                UsunZKoszyka($id_produktu);
            }
            break;
        case 'edytuj':
            if ($id_produktu !== null && isset($_POST['ilosc'])) {
                $ilosc = $_POST['ilosc'];
                EdytujKoszyk($id_produktu, $ilosc);
            }
            break;
    }
}

if (isset($_POST['pokaz_koszyk'])) {
    echo '<h2>Twój koszyk</h2>';
    echo ZarzadzajKoszykiem('pokaz');

    echo '<form method="POST" style="margin-top: 20px;">';
	echo '<h3>Edytuj ilość produktu w koszyku</h3>';
	echo '<input type="number" name="id" placeholder="ID produktu do edycji" required>';
	echo '<input type="number" name="ilosc" placeholder="Nowa ilość" required>';
	echo '<input type="hidden" name="pokaz_koszyk" value="1">'; // Pole ukryte
	echo '<button type="submit" name="akcja" value="edytuj">Zaktualizuj</button>';
	echo '</form>';


    echo '<form method="POST" style="margin-top: 20px;">';
    echo '<button type="submit" name="ukryj_koszyk">Ukryj koszyk</button>';
    echo '</form>';
} else {
    echo '<form method="POST">';
    echo '<button type="submit" name="pokaz_koszyk">Pokaż koszyk</button>';
    echo '</form>';
}

function DodajDoKoszyka($id_produktu) {
    global $link;

    $query = $link->prepare("SELECT id, tytul, cena_netto, podatek_vat FROM produkty WHERE id = ?");
    $query->bind_param("i", $id_produktu);
    $query->execute();
    $result = $query->get_result();
    $produkt = $result->fetch_assoc();

    if ($produkt) {
        $id = $produkt['id'];

        if (isset($_SESSION['koszyk'][$id])) {
            $_SESSION['koszyk'][$id]['ilosc'] += 1;
        } else {
            $_SESSION['koszyk'][$id] = [
                'tytul' => $produkt['tytul'],
                'cena_netto' => $produkt['cena_netto'],
                'podatek_vat' => $produkt['podatek_vat'],
                'ilosc' => 1, 
            ];
        }
    }
}

function UsunZKoszyka($id_produktu) {
    unset($_SESSION['koszyk'][$id_produktu]);
}

function EdytujKoszyk($id_produktu, $ilosc) {
    if (isset($_SESSION['koszyk'][$id_produktu])) {
        $_SESSION['koszyk'][$id_produktu]['ilosc'] = $ilosc;
    }
}

function ZarzadzajKoszykiem($akcja) {
    ob_start();

    switch ($akcja) {
        case 'pokaz':
            if (empty($_SESSION['koszyk'])) {
                echo '<h3>Twój koszyk jest pusty.</h3>';
            } else {
                echo '<table border="1">';
                echo '<thead><tr><th>ID</th><th>Tytuł</th><th>Cena Netto</th><th>VAT</th><th>Ilość</th><th>Łączna Cena Brutto</th><th>Akcja</th></tr></thead>';
                echo '<tbody>';

                $wartosc_calkowita = 0;

                foreach ($_SESSION['koszyk'] as $id => $produkt) {
                    $ilosc = isset($produkt['ilosc']) ? $produkt['ilosc'] : 0; 

                    $cena_brutto = $produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100);
                    $wartosc = $cena_brutto * $ilosc;
                    $wartosc_calkowita += $wartosc;

                    echo '<tr>';
                    echo '<td>' . $id . '</td>';
                    echo '<td>' . htmlspecialchars($produkt['tytul']) . '</td>';
                    echo '<td>' . $produkt['cena_netto'] . '</td>';
                    echo '<td>' . $produkt['podatek_vat'] . '%</td>';
                    echo '<td>' . $ilosc . '</td>';
                    echo '<td>' . number_format($wartosc, 2) . '</td>';
                    echo '<td>';
                    echo '<form method="POST">';
                    echo '<input type="hidden" name="id" value="' . $id . '">';
                    echo '<input type="hidden" name="pokaz_koszyk" value="1">'; 
                    echo '<button type="submit" name="akcja" value="usun">Usuń</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '<h3>Łączna wartość koszyka: ' . number_format($wartosc_calkowita, 2) . ' PLN</h3>';
            }

            echo '<form method="POST" style="margin-top: 20px;">';
            echo '<h3>Dodaj nowy produkt do koszyka</h3>';
            echo '<input type="number" name="id" placeholder="ID produktu" required>';

            echo '<input type="hidden" name="pokaz_koszyk" value="1">';
            echo '<button type="submit" name="akcja" value="dodaj">Dodaj do koszyka</button>';
            echo '</form>';

            break;
    }

    return ob_get_clean();
}


?>
