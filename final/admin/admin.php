<?php
session_start();
error_reporting(E_ALL);
include('cfg.php');

echo '<link rel="stylesheet" href="css/styles.css">';

if (isset($_SESSION['zalogowany']) && $_SESSION['zalogowany'] === true) {
    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
            <button type="submit" name="logout" class="custom-button-long-g">Wyloguj się</button>
          </form>';

    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
            <button type="submit" name="manage_pages" class="custom-button-long-b">Zarządzaj podstronami</button>
          </form>';

    if (isset($_POST['manage_pages'])) {
        $_SESSION['show_pages'] = true;
    }

    if (isset($_POST['hide_pages'])) {
        $_SESSION['show_pages'] = false;
    }

    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        unset($_SESSION['show_pages']);
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    if (isset($_GET['edit_id'])) {
        $edit_id = intval($_GET['edit_id']);
        EdytujPodstrone($edit_id);
    } elseif (isset($_GET['add'])) {
        DodajNowaPodstrone();
    } elseif (isset($_GET['delete_id'])) {
        $delete_id = intval($_GET['delete_id']);
        UsunPodstrone($delete_id);
    } elseif (isset($_SESSION['show_pages']) && $_SESSION['show_pages'] === true) {
        ListaPodstron();
        echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
                <button type="submit" name="hide_pages" class="custom-button-long-r">Ukryj podstrony</button>
              </form>';
    }
} else {
    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
            <button type="submit" name="show_login_form" class="custom-button-long-g">Zaloguj się jako admin</button>
          </form>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['x1_submit'])) {
        $email = $_POST['login_email'] ?? '';
        $haslo = $_POST['login_pass'] ?? '';
        
        if ($email === $login && $haslo === $pass) {
            $_SESSION['zalogowany'] = true;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $error_message = 'Nieprawidłowy login lub hasło!';
        }
    }

    if (isset($_POST['show_login_form'])) {
        $_SESSION['show_login_form'] = true;
    }

    if (isset($_SESSION['show_login_form']) && $_SESSION['show_login_form'] === true) {
        echo FormularzLogowania();
    }
}

include('shop.php');
include('products.php');

function FormularzLogowania() {
    global $error_message;

    $error_html = '';
    if (isset($error_message)) {
        $error_html = '<p style="color:red;">' . $error_message . '</p>';
    }

    return '
    <div id="loginForm" class="logowanie" style="display:block;">
        <h2 class="heading">Zaloguj się do panelu CMS:</h2>
        <form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
            <table class="logowanie">
                <tr><td>Login:</td><td><input type="text" name="login_email" class="logowanie" required /></td></tr>
                <tr><td>Hasło:</td><td><input type="password" name="login_pass" class="logowanie" required /></td></tr>
                <tr><td></td><td><input type="submit" name="x1_submit" value="Zaloguj" class="custom-button" /></td></tr>
            </table>
        </form>
        ' . $error_html . '
    </div>';
}

echo '<script>
document.getElementById("adminLoginBtn").addEventListener("click", function() {
    document.getElementById("loginForm").style.display = "block";
    this.style.display = "none";
});
</script>';

function ListaPodstron() {
    global $link;

    if (!isset($_SESSION['show_pages']) || $_SESSION['show_pages'] !== true) {
        return;
    }

    $query = "SELECT id, page_title FROM page_list ORDER BY id ASC LIMIT 100";
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link));
    }

    if (mysqli_num_rows($result) > 0) {
        echo '<tr><td colspan="3"><a href="?add=true"><button type="button" class="custom-button-long-g">Dodaj nową podstronę</button></a></td></tr>';
        echo '<div class="table-container">';
        echo '<table>';
        echo '<thead>';
        echo '<tr><th>ID</th><th>Tytuł podstrony</th><th>Akcje</th></tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = mysqli_fetch_assoc($result)) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['id']) . '</td>';
            echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
            echo '<td>';
            echo '<a href="?edit_id=' . htmlspecialchars($row['id']) . '"><button class="custom-button-edit">Edytuj</button></a>';
            echo '<a href="?delete_id=' . htmlspecialchars($row['id']) . '" onclick="return confirm(\'Czy na pewno chcesz usunąć tę podstronę?\')"><button class="custom-button-delete">Usuń</button></a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';
    } else {
        echo 'Brak podstron w bazie danych.';
    }

    mysqli_free_result($result);
}

function EdytujPodstrone($id) {
    global $link;

    $id_clear = mysqli_real_escape_string($link, $id);
    $query = "SELECT page_title, page_content, status FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link));
    }

    if ($row = mysqli_fetch_assoc($result)) {
        $page_title = $row['page_title'];
        $page_content = $row['page_content'];
        $status = $row['status'];
    } else {
        echo 'Strona o podanym ID nie istnieje.';
        return;
    }

    echo '<h2>Edytuj podstronę: ' . htmlspecialchars($page_title) . '</h2>';
    echo '<form method="post" action="">';
    echo '<table>';
    echo '<tr><td>Tytuł:</td><td><input type="text" name="page_title" value="' . htmlspecialchars($page_title) . '" required /></td></tr>';
    echo '<tr><td>Treść:</td><td><textarea name="page_content" rows="10" cols="50" required>' . htmlspecialchars($page_content) . '</textarea></td></tr>';
    echo '<tr><td>Status (Aktywna):</td><td><input type="checkbox" name="status" value="1" ' . ($status == 1 ? 'checked' : '') . ' /></td></tr>';
    echo '<tr><td colspan="2"><button type="submit" name="update_page" class="custom-button-bu">Zaktualizuj</button></td></tr>';
    echo '<tr><td colspan="2"><a href="' . $_SERVER['PHP_SELF'] . '"><button type="button" class="custom-button-bu">Powrót do listy podstron</button></a></td></tr>';
    echo '</table>';
    echo '</form>';

    if (isset($_POST['update_page'])) {
        $new_page_title = mysqli_real_escape_string($link, $_POST['page_title']);
        $new_page_content = mysqli_real_escape_string($link, $_POST['page_content']);
        $new_status = isset($_POST['status']) ? 1 : 0;

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

function DodajNowaPodstrone() {
    global $link;

    echo '<h2>Dodaj nową podstronę</h2>';
    echo '<form method="post" action="">';
    echo '<table>';
    echo '<tr><td>Tytuł:</td><td><input type="text" name="page_title" required /></td></tr>';
    echo '<tr><td>Treść:</td><td><textarea name="page_content" rows="10" cols="50" required></textarea></td></tr>';
    echo '<tr><td>Status (Aktywna):</td><td><input type="checkbox" name="status" value="1" /></td></tr>';
    echo '<tr><td colspan="2"><button type="submit" name="add_page" class="custom-button">Dodaj</button></td></tr>';
    echo '<tr><td colspan="2"><a href="' . $_SERVER['PHP_SELF'] . '"><button type="button" class="custom-button">Powrót do listy podstron</button></a></td></tr>';
    echo '</table>';
    echo '</form>';

    if (isset($_POST['add_page'])) {
        $page_title = mysqli_real_escape_string($link, $_POST['page_title']);
        $page_content = mysqli_real_escape_string($link, $_POST['page_content']);
        $status = isset($_POST['status']) ? 1 : 0;

        $insert_query = "INSERT INTO page_list (page_title, page_content, status) VALUES ('$page_title', '$page_content', '$status')";

        if (mysqli_query($link, $insert_query)) {
            echo '<p style="color:green;">Podstrona została dodana!</p>';
        } else {
            echo '<p style="color:red;">Błąd dodawania podstrony: ' . mysqli_error($link) . '</p>';
        }
    }
}

function UsunPodstrone($id) {
    global $link;

    $id_clear = mysqli_real_escape_string($link, $id);

    $delete_query = "DELETE FROM page_list WHERE id = '$id_clear' LIMIT 1";

    if (mysqli_query($link, $delete_query)) {
        echo '<p style="color:green;">Podstrona została usunięta!</p>';
    } else {
        echo '<p style="color:red;">Błąd usuwania podstrony: ' . mysqli_error($link) . '</p>';
    }
}

?>
