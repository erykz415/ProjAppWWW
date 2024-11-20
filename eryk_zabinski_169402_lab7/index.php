<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
include('cfg.php'); 
include('showpage.php'); 
include('admin/admin.php');


$idp = $_GET['idp'] ?? 4; 

$dozwolone_idp = [1, 2, 3, 4, 5, 6];
if (in_array($idp, $dozwolone_idp)) {
    $strona = $idp;
} else {
    $strona = 4;
}
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
    <div id="zegar">
        <div id="data"></div>
        <div id="zegarek"></div>
    </div>
    <div class="header">
        <p>HODOWLA ŻÓŁWIA WODNEGO</p>
        <p>Wszystko, co musisz wiedzieć o opiece nad żółwiem wodnym</p>
    </div>
    <div class="navbar">
        <a href="index.php?idp=4">Strona Główna</a>
        <a href="index.php?idp=6">O Hodowli</a>
        <a href="index.php?idp=3">Gatunki Żółwi</a>
        <a href="index.php?idp=2">Galeria</a>
        <a href="index.php?idp=5">Kontakt</a>
        <a href="index.php?idp=1">Filmy</a>
    </div>
    <div id="contentCell" class="content">
        <?php
            echo PokazPodstrone($strona);
        ?>
    </div>
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
