<?php
// Włączenie raportowania błędów, wyłączając NOTICE i WARNING
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

// Dołączanie plików konfiguracyjnych i pomocniczych
//include('cfg.php');       // Plik z konfiguracją
include('showpage.php');  // Funkcje do wyświetlania treści podstron
include('contact.php');   // Obsługa formularza kontaktowego
include('admin/admin.php'); // Panel administracyjny
include('shop.php');
include('products.php');

// Pobranie wartości 'idp' z parametru GET z ustawieniem domyślnej wartości na 4
$idp = $_GET['idp'] ?? 4; 

// Zabezpieczenie przed wstrzykiwaniem kodu: sprawdzenie, czy 'idp' należy do listy dozwolonych wartości
$dozwolone_idp = [1, 2, 3, 4, 5, 6]; // Lista dozwolonych identyfikatorów podstron
if (in_array($idp, $dozwolone_idp)) {
    $strona = $idp; // Jeśli wartość jest poprawna, przypisz ją do zmiennej 'strona'
} else {
    $strona = 4; // W przeciwnym razie ustaw domyślną stronę
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hodowla żółwia wodnego</title>
    <!-- Łączenie z plikiem CSS -->
    <link rel="stylesheet" href="css/styles.css">
    <!-- Skrypty JavaScript -->
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
</head>
<body>
    <!-- Sekcja wyświetlająca datę i godzinę -->
    <div id="zegar">
        <div id="data"></div>
        <div id="zegarek"></div>
    </div>

    <!-- Nagłówek strony -->
    <div class="header">
        <p>HODOWLA ŻÓŁWIA WODNEGO</p>
        <p>Wszystko, co musisz wiedzieć o opiece nad żółwiem wodnym</p>
    </div>

    <!-- Nawigacja po stronie -->
    <div class="navbar">
        <a href="index.php?idp=4">Strona Główna</a>
        <a href="index.php?idp=6">O Hodowli</a>
        <a href="index.php?idp=3">Gatunki Żółwi</a>
        <a href="index.php?idp=2">Galeria</a>
        <a href="index.php?idp=5">Kontakt</a>
        <a href="index.php?idp=1">Filmy</a>
    </div>

    <!-- Dynamiczne ładowanie treści podstron -->
    <div id="contentCell" class="content">
        <?php
            // Wywołanie funkcji 'PokazPodstrone', która zwraca treść wybranej podstrony
            echo PokazPodstrone($strona);
        ?>
    </div>

    <!-- Przyciski zmieniające tło strony -->
    <div class="button-container">
        <button onclick="changeBackground('#ffffff')">biały</button>
        <button onclick="changeBackground('#faec8e')">żółty</button>
        <button onclick="changeBackground('#fa5757')">czerwony</button>
        <button onclick="changeBackground('#c7c7c7')">szary</button>
        <button onclick="changeBackground('#93d9fa')">niebieski</button>
        <button onclick="changeBackground('#6dfcb0')">zielony</button>
        <button onclick="changeBackground('#f58efa')">różowy</button>
    </div>

    <?php
        $nr_indeksu = '169402';
        $nrGrupy = '4'; 
        echo 'Autor: Eryk Żabiński ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />'; 
    ?>
</body>
</html>
