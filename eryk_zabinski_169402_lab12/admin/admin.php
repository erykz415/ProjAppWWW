<?php
session_start();
error_reporting(E_ALL);
include('cfg.php');

// ================================
// Sprawdzanie, czy użytkownik jest zalogowany
// ================================

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true) {
    // Obsługuje edycję podstrony, dodawanie nowej podstrony lub usuwanie podstrony w zależności od parametru w URL
    if (isset($_GET['edit_id'])) {
        $edit_id = intval($_GET['edit_id']);
        EdytujPodstrone($edit_id); // Funkcja do edycji podstrony
    } elseif (isset($_GET['add'])) {
        DodajNowaPodstrone(); // Funkcja do dodawania nowej podstrony
    } elseif (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        UsunPodstrone($delete_id); // Funkcja do usuwania podstrony
    } else {
        ListaPodstron(); // Funkcja do wyświetlania listy podstron
    }
} else {
    // ================================
    // Obsługa formularza logowania
    // ================================

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x1_submit'])) {
        $email = $_POST['login_email'] ?? '';
        $haslo = $_POST['login_pass'] ?? '';
        
        // Sprawdzanie danych logowania
        if ($email === $login && $haslo === $pass) {
            $_SESSION['zalogowany'] = true; // Ustawienie sesji jako zalogowany
            header('Location: ' . $_SERVER['PHP_SELF']); // Przekierowanie na tę samą stronę
            exit;
        } else {
            echo '<p style="color:red;">Nieprawidłowy e-mail lub hasło!</p>';
        }
    }

    // Dodaj przycisk i ukryty formularz logowania
    echo '
    <button id="showLoginForm" style="margin: 20px; padding: 10px 20px;">Zaloguj się jako administrator</button>
    <div id="loginForm" style="display: none; margin-top: 20px;">
        ' . FormularzLogowania() . '
    </div>
    <script>
        document.getElementById("showLoginForm").addEventListener("click", function() {
            document.getElementById("loginForm").style.display = "block";
            this.style.display = "none"; // Ukryj przycisk po kliknięciu
        });
    </script>';
}


// ================================
// Funkcja generująca formularz logowania
// ================================

function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS:</h1>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <table class="logowanie">
                <tr><td>E-mail:</td><td><input type="text" name="login_email" class="logowanie" required /></td></tr>
                <tr><td>Hasło:</td><td><input type="password" name="login_pass" class="logowanie" required /></td></tr>
                <tr><td></td><td><input type="submit" name="x1_submit" value="Zaloguj" /></td></tr>
            </table>
        </form>
    </div>';
}
function ListaPodstron() {
    global $link;

    // Zapytanie SQL do pobrania listy podstron z bazy danych
    $query = "SELECT id, page_title FROM page_list ORDER BY id ASC LIMIT 100"; // Ograniczenie wyników do 100
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link)); // Obsługa błędu zapytania
    }

    if (mysqli_num_rows($result) > 0) {
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr><th>ID</th><th>Tytuł podstrony</th><th>Akcje</th></tr>';
        echo '<a href="?add=true"><button type="button">Dodaj nową podstronę</button></a>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td>';
            echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '"><button>Edytuj</button></a>';
            echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')"><button>Usuń</button></a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    } else {
        echo 'Brak podstron w bazie danych.';
    }

    mysqli_free_result($result); // Zwolnienie pamięci po zapytaniu
}

// ================================
// Funkcja do edytowania podstrony
// ================================

function EdytujPodstrone($id) {
    global $link;

    // Ochrona przed SQL injection przez escapowanie ID
    $id_clear = mysqli_real_escape_string($link, $id);

    // Zapytanie SQL do pobrania danych podstrony
    $query = "SELECT page_title, page_content, status FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link)); // Obsługa błędu zapytania
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $page_title = $row['page_title'];
        $page_content = $row['page_content'];
        $status = $row['status'];
    } else {
        echo 'Strona o podanym ID nie istnieje.';
        return;
    }

    // Formularz do edycji podstrony
    echo '<h2>Edytuj podstronę: ' . htmlspecialchars($page_title) . '</h2>';
    echo '<form method="post" action="">';
    echo '<table>';
    echo '<tr><td>Tytuł:</td><td><input type="text" name="page_title" value="' . htmlspecialchars($page_title) . '" required /></td></tr>';
    echo '<tr><td>Treść:</td><td><textarea name="page_content" rows="10" cols="50" required>' . htmlspecialchars($page_content) . '</textarea></td></tr>';
    echo '<tr><td>Status (Aktywna):</td><td><input type="checkbox" name="status" value="1" ' . ($status == 1 ? 'checked' : '') . ' /></td></tr>';
    echo '<tr><td colspan="2"><button type="submit" name="update_page">Zaktualizuj</button></td></tr>';
    echo '<tr><td colspan="2"><a href="' . $_SERVER['PHP_SELF'] . '"><button type="button">Powrót do listy podstron</button></a></td></tr>';
    echo '</table>';
    echo '</form>';

    // Aktualizacja danych podstrony po zatwierdzeniu formularza
    if (isset($_POST['update_page'])) {
        $new_page_title = mysqli_real_escape_string($link, $_POST['page_title']);
        $new_page_content = mysqli_real_escape_string($link, $_POST['page_content']);
        $new_status = isset($_POST['status']) ? 1 : 0;

        // Zapytanie SQL do aktualizacji podstrony
        $update_query = "UPDATE page_list SET 
                            page_title='$new_page_title', 
                            page_content='$new_page_content', 
                            status='$new_status' 
                         WHERE id='$id_clear' LIMIT 1";

        if (mysqli_query($link, $update_query)) {
            echo '<p style="color:green;">Podstrona została zaktualizowana!</p>';
        } else {
            echo '<p style="color:red;">Błąd aktualizacji: ' . mysqli_error($link) . '</p>';
        }
    }
}

// ================================
// Funkcja do dodawania nowej podstrony
// ================================

function DodajNowaPodstrone() {
    global $link;

    echo '<h2>Dodaj nową podstronę</h2>';
    echo '<form method="post" action="">';
    echo '<table>';
    echo '<tr><td>Tytuł:</td><td><input type="text" name="page_title" required /></td></tr>';
    echo '<tr><td>Treść:</td><td><textarea name="page_content" rows="10" cols="50" required></textarea></td></tr>';
    echo '<tr><td>Status (Aktywna):</td><td><input type="checkbox" name="status" value="1" /></td></tr>';
    echo '<tr><td colspan="2"><button type="submit" name="add_page">Dodaj</button></td></tr>';
    echo '<tr><td colspan="2"><a href="' . $_SERVER['PHP_SELF'] . '"><button type="button">Powrót do listy podstron</button></a></td></tr>';
    echo '</table>';
    echo '</form>';

    // Dodanie nowej podstrony po zatwierdzeniu formularza
    if (isset($_POST['add_page'])) {
        $page_title = mysqli_real_escape_string($link, $_POST['page_title']);
        $page_content = mysqli_real_escape_string($link, $_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        // Zapytanie SQL do dodania nowej podstrony
        $insert_query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$page_title', '$page_content', '$status')";

        if (mysqli_query($link, $insert_query)) {
            echo '<p style="color:green;">Podstrona została dodana!</p>';
        } else {
            echo '<p style="color:red;">Błąd dodawania podstrony: ' . mysqli_error($link) . '</p>';
        }
    }
}

// ================================
// Funkcja do usuwania podstrony
// ================================

function UsunPodstrone($id) {
    global $link;

    // Ochrona przed SQL injection przez escapowanie ID
    $id_clear = mysqli_real_escape_string($link, $id);

    // Zapytanie SQL do usunięcia podstrony
    $delete_query = "DELETE FROM page_list WHERE id = '$id_clear' LIMIT 1";

    if (mysqli_query($link, $delete_query)) {
        header("Location: " . $_SERVER['PHP_SELF']); // Przekierowanie po usunięciu
        exit;
    } else {
        echo '<p style="color:red;">Błąd usuwania podstrony: ' . mysqli_error($link) . '</p>';
    }
}

?>
