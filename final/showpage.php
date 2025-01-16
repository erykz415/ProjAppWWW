<?php

echo '<link rel="stylesheet" href="css/styles.css">';

function PokazPodstrone($id)
{
    global $link; 
    $id_clear = mysqli_real_escape_string($link, $id);
    $query = "SELECT page_content FROM page_list WHERE id='$id_clear' AND status=1 LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link));
    }
    if ($row = mysqli_fetch_assoc($result)) {
        $web = $row['page_content'];
    } else {
        $web = '[nie_znaleziono_strony]';
    }
    return $web;
}
function PokazSklep($link)
{
    $result = mysqli_query($link, "SELECT * FROM produkty WHERE status_dostepnosci = 'Dostępny' ORDER BY data_utworzenia ASC");

    echo '<div class="koszyk-container">';
    echo '<h3 class="new-naglowek">Nasze Produkty</h3>';
    echo '<div class="produkty-container">';

    while ($produkt = mysqli_fetch_assoc($result)) {
        echo '<div class="produkt-item">';
        echo '<img src="' . htmlspecialchars($produkt['zdjecie']) . '" alt="Zdjęcie produktu">';
        echo '<h4>' . htmlspecialchars($produkt['tytul']) . '</h4>';
        echo '<p>' . htmlspecialchars($produkt['opis']) . '</p>';
        echo '<p class="cena">Cena netto: ' . number_format($produkt['cena_netto'], 2) . ' PLN</p>';
        echo '<p class="cena-brutto">Cena brutto: ' . number_format($produkt['cena_netto'] * (1 + $produkt['podatek_vat'] / 100), 2) . ' PLN</p>';
        echo '<p><strong>Status:</strong> ' . htmlspecialchars($produkt['status_dostepnosci']) . '</p>';

        echo '<form method="POST" action="index.php?idp=18">';
        echo '<input type="hidden" name="akcja" value="dodaj">';
        echo '<input type="hidden" name="id" value="' . $produkt['id'] . '">';
        echo '<input type="hidden" name="tytul" value="' . htmlspecialchars($produkt['tytul']) . '">';
        echo '<input type="hidden" name="cena_netto" value="' . $produkt['cena_netto'] . '">';
        echo '<input type="hidden" name="podatek_vat" value="' . $produkt['podatek_vat'] . '">';
        echo '<button type="submit" class="koszyk-back-button", class="custom-button-add">Dodaj do koszyka</button>';
        echo '</form>';

        echo '</div>';
    }

    echo '</div>';
    echo '</div>';

    mysqli_free_result($result);
}
