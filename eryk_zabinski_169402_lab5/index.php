<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

// Ustal plik do załadowania na podstawie parametru 'idp' z URL
$idp = $_GET['idp'] ?? '';
switch ($idp) {
    case 'ohodowli':
        $strona = 'html/ohodowli.html';
        break;
    case 'gatunki':
        $strona = 'html/gatunki.html';
        break;
    case 'galeria':
        $strona = 'html/galeria.html';
        break;
    case 'kontakt':
        $strona = 'html/kontakt.html';
        break;
    case 'filmy':
        $strona = 'html/filmy.html';
        break;
    default:
        $strona = 'html/glowna.html';
}

$filePath = $_SERVER['DOCUMENT_ROOT'] . '/lab5/' . $strona;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hodowla żółwia wodnego</title>

    <link rel="stylesheet" href="css/styles.css">
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
</head>
<body>
    <!-- Zegar -->
    <div id="zegar">
        <div id="data"></div>
        <div id="zegarek"></div>
    </div>

    <!-- Nagłówek strony -->
    <div class="header">
        <p>HODOWLA ŻÓŁWIA WODNEGO</p>
        <p>Wszystko, co musisz wiedzieć o opiece nad żółwiem wodnym</p>
    </div>

    <!-- Menu nawigacyjne -->
    <div class="navbar">
        <a href="index.php?idp=glowna">Strona Główna</a>
        <a href="index.php?idp=ohodowli">O Hodowli</a>
        <a href="index.php?idp=gatunki">Gatunki Żółwi</a>
        <a href="index.php?idp=galeria">Galeria</a>
        <a href="index.php?idp=kontakt">Kontakt</a>
        <a href="index.php?idp=filmy">Filmy</a>
    </div>

    <!-- Główna zawartość strony -->
    <div id="contentCell" class="content">
        <?php
            if (file_exists($filePath)) {
                include $filePath;
            } else {
                echo "ERROR 404";
            }
        ?>
    </div>

    <!-- Kontenery przycisków do zmiany koloru tła -->
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
        // Informacje o autorze
        $nr_indeksu = '169402';
        $nrGrupy = '4';
        echo 'Autor: Eryk Żabiński ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
    ?>
</body>
</html>
