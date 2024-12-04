<?php
// **************************************************************
// * Funkcja: PokazPodstrone                                     *
// * Opis: Pobiera treść podstrony na podstawie jej ID z bazy.   *
// **************************************************************
function PokazPodstrone($id)
{
    // Użycie zmiennej globalnej do połączenia z bazą danych.
    global $link; 

    // Zabezpieczenie przed SQL Injection poprzez oczyszczenie ID.
    $id_clear = mysqli_real_escape_string($link, $id);

    // Zapytanie SQL pobierające treść podstrony, która ma status aktywny (status=1).
    // LIMIT 1 gwarantuje, że zwrócona zostanie tylko jedna pasująca podstrona.
    $query = "SELECT page_content FROM page_list WHERE id='$id_clear' AND status=1 LIMIT 1";

    // Wykonanie zapytania SQL.
    $result = mysqli_query($link, $query);

    // Sprawdzenie, czy zapytanie SQL zakończyło się sukcesem.
    if (!$result) {
        // W przypadku błędu zapytania wyświetl komunikat błędu i zakończ skrypt.
        die('Błąd zapytania SQL: ' . mysqli_error($link));
    }

    // Sprawdzenie, czy zapytanie zwróciło jakikolwiek wynik.
    if ($row = mysqli_fetch_assoc($result)) {
        // Jeśli podstrona została znaleziona, przypisujemy jej zawartość do zmiennej $web.
        $web = $row['page_content'];
    } else {
        // Jeśli podstrona nie została znaleziona, ustawiamy komunikat o błędzie.
        $web = '[nie_znaleziono_strony]';
    }

    // Zwrócenie zawartości podstrony (lub komunikatu o błędzie) jako wynik funkcji.
    return $web;
}
?>

