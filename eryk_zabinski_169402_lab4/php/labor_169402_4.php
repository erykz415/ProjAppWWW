<?php
 session_start();
 $nr_indeksu = '169402';
 $nrGrupy = '4';
 echo 'Eryk Żabiński ' . $nr_indeksu . ' grupa ' . $nrGrupy . '<br /><br />';
 echo 'Zastosowanie metody include() <br />';
 $color = 'green';
 $fruit = 'apple';
 echo "A $color $fruit<br>"; 
 include 'test.php';
 echo "A $color $fruit<br><br>"; 
 echo 'Zastosowanie metody require_once()<br>';
 require_once 'test.php';
 echo kot('puszek'); 
 echo '<br><br>warunki if, else, elseif, switch<br><br>';
 echo 'if, elseif, else<br>';
 $a=5;
 $b=5;
 if ($a > $b)
  echo "a jest większe od b";
 elseif($a < $b)
  echo "a jest mniejsze od b";
 else 
  echo "a jest równe b";
 echo "<br><br> switch<br>";
$dzien=3;
switch ($dzien) {
    case 1:
        echo "Poniedziałek";
        break;
    case 2:
        echo "Wtorek";
        break;
    case 3:
        echo "Środa";
        break;
    case 4:
        echo "Czwartek";
        break;
    case 5:
        echo "Piątek";
        break;
    case 6:
        echo "Sobota";
        break;
    case 7:
        echo "Niedziela";
        break;
    default:
        echo "Niepoprawny dzień tygodnia";
}
 echo "<br><br>Pętla while() i for()<br><br>";
 echo "petla while<br>";
 $i = 1;
 while ($i <= 10) {
    echo $i++ . " ";  
 }
 echo "<br><br> petla for <br>";
 $owoce = ["jabłko", "banan", "wiśnia"];

 for ($i = 0; $i < count($owoce); $i++) {
    echo $owoce[$i] . " ";
 }
 echo "<br><br>Typy zmiennych \$_GET, \$_POST, \$_SESSION<br><br>";
 echo "\$_GET<br>";
 // Sprawdzamy, czy parametry 'name' i 'age' są w URL
if (isset($_GET['name']) && isset($_GET['age'])) {
    $name = htmlspecialchars($_GET['name']); // Bezpieczne przetwarzanie
    $age = (int)$_GET['age']; // Konwersja wieku na liczbę całkowitą

    echo "Witaj, $name! Masz $age lat.";
} else {
    echo "Brak danych.";
}
 echo "<br><br> \$_POST<br>";
 $_POST['name'] = 'Jan';

if (isset($_POST['name'])) {
    $name = $_POST['name'];
    echo "Witaj, " . htmlspecialchars($name) . "!";
}
 echo "<br><br> metoda \$_SESSION<br>";
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $_SESSION['username'] = $_POST['username'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Sesje w PHP</title>
</head>
<body>
    <h1>Witaj w systemie sesji PHP!</h1>

    <form method="post">
        <label for="username">Nazwa użytkownika:</label>
        <input type="text" id="username" name="username" required>
        <input type="submit" value="Zaloguj się">
    </form>

    <?php if (isset($_SESSION['username'])): ?>
        <p>Witaj, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
        <form method="post" action="">
            <input type="submit" name="logout" value="Wyloguj się">
        </form>
        <?php
        if (isset($_POST['logout'])) {
            session_unset();
            session_destroy();
            header('Location: ' . $_SERVER['PHP_SELF']); 
            exit();
        }
        ?>
    <?php endif; ?>
</body>
</html>

