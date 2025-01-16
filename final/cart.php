<?php
include('cfg.php');
echo '<link rel="stylesheet" href="css/styles.css?v=' . time() . '">';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcja'])) {
    $akcja = $_POST['akcja'];
    $id_produktu = $_POST['id'] ?? null;

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

    header("Location: index.php?idp=18");
    exit;
}

function DodajDoKoszyka($id_produktu) {
    if (!isset($_SESSION['koszyk'])) {
        $_SESSION['koszyk'] = [];
    }

    $tytul = $_POST['tytul'] ?? '';
    $cena_netto = $_POST['cena_netto'] ?? 0;
    $podatek_vat = $_POST['podatek_vat'] ?? 0;

    if (isset($_SESSION['koszyk'][$id_produktu])) {
        $_SESSION['koszyk'][$id_produktu]['ilosc'] += 1;
    } else {
        $_SESSION['koszyk'][$id_produktu] = [
            'tytul' => $tytul,
            'cena_netto' => $cena_netto,
            'podatek_vat' => $podatek_vat,
            'ilosc' => 1,
        ];
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
            echo '<div class="koszyk-container">';
            if (empty($_SESSION['koszyk'])) {
                echo '<h3 class="new-naglowek">Twój koszyk:</h3>';
                echo '<h3 class="empty-cart-title">Twój koszyk jest pusty.</h3>';
            } else {
                echo '<h3 class="new-naglowek">Twój koszyk:</h3>';
                echo '<table class="koszyk-tabela">';
                echo '<thead><tr><th>Nazwa</th><th>Cena Netto</th><th>VAT</th><th>Ilość</th><th>Cena Brutto</th><th> </th></tr></thead>';
                echo '<tbody>';

                $wartosc_calkowita = 0;

                foreach ($_SESSION['koszyk'] as $id => $produkt) {
                    $ilosc = $produkt['ilosc'] ?? 0;

                    $cena_brutto = $produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100);
                    $wartosc = $cena_brutto * $ilosc;
                    $wartosc_calkowita += $wartosc;

                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($produkt['tytul']) . '</td>';
                    echo '<td>' . number_format($produkt['cena_netto'], 2) . ' PLN</td>';
                    echo '<td>' . htmlspecialchars($produkt['podatek_vat']) . '%</td>';
                    echo '<td>';
                    echo '<form method="POST" class="edit-quantity-form" style="display: inline-block;">';
                    echo '<input type="number" name="ilosc" value="' . $ilosc . '" min="1" required style="width: 50px; height: 30px;">';
                    echo '<input type="hidden" name="id" value="' . $id . '">'; 
                    echo '<button type="submit" name="akcja" value="edytuj" class="custom-button-edit">Zmień</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '<td>' . number_format($wartosc, 2) . ' PLN</td>';
                    echo '<td>';
                    echo '<form method="POST" style="display: inline-block;" class="akcja">'; 
                    echo '<input type="hidden" name="id" value="' . $id . '">'; 
                    echo '<button type="submit" name="akcja" value="usun" class="custom-button-delete">Usuń</button>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '<div class="koszyk-podsumowanie">Łączna wartość koszyka: <span>' . number_format($wartosc_calkowita, 2) . ' PLN</span></div>';
            }
            echo '</div>'; 
            break;
    }

    return ob_get_clean();
}

if (isset($_GET['akcja']) && $_GET['akcja'] === 'pokaz') {
    echo ZarzadzajKoszykiem('pokaz');
}
?>
