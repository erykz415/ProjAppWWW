<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$baza = 'moja_strona';
$login = "admin";  // Zmienna przechowująca login
$pass = "haslo"; 

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

if (!$link) {
    die('Błąd połączenia: ' . mysqli_connect_error());
}
?>
