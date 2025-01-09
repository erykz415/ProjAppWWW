<?php
// *************************************
// Konfiguracja bazy danych i zmiennych
// *************************************

$dbhost = 'localhost';        // Adres hosta bazy danych
$dbuser = 'root';             // Nazwa użytkownika bazy danych
$dbpass = '';                 // Hasło użytkownika bazy danych
$baza = 'moja_strona';        // Nazwa bazy danych

$login = "admin";             // Zmienna przechowująca login administratora
$pass = "haslo";              // Zmienna przechowująca hasło administratora

// ******************************************
// Połączenie z bazą danych przy użyciu MySQLi
// ******************************************
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $baza);

if (!$link) {
    // Jeśli połączenie nie powiodło się, wyświetlany jest komunikat błędu
    die('Błąd połączenia: ' . mysqli_connect_error());
}
?>
