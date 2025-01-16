<?php
include('cfg.php');
include('showpage.php');
include('admin/admin.php');
include('contact.php');
include('cart.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$query = "SELECT id, page_title FROM page_list WHERE status = 1 ORDER BY id ASC";
$result = mysqli_query($link, $query);

if (!$result) {
    die('Błąd zapytania SQL: ' . mysqli_error($link));
}

$pages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $pages[] = $row;
}

mysqli_free_result($result);

$idp = $_GET['idp'] ?? 4;

$dozwolone_idp = array_map(function($page) {
    return $page['id'];
}, $pages);

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
        <?php foreach ($pages as $page): ?>
            <a href="index.php?idp=<?php echo $page['id']; ?>"><?php echo htmlspecialchars($page['page_title']); ?></a>
        <?php endforeach; ?>
    </div>
    <div id="contentCell" class="content">
        <?php
        if ($strona == 18) {
            echo ZarzadzajKoszykiem('pokaz');
            PokazSklep($link);
        } else {
            echo PokazPodstrone($strona);
        }
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
