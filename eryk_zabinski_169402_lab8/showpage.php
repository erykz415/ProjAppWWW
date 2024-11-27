<?php
function PokazPodstrone($id)
{
    global $link; 
    $id_clear = mysqli_real_escape_string($link, $id);
    $query = "SELECT page_content FROM page_list WHERE id='$id_clear' AND status=1 LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        die('Błąd zapytania SQL: ' . mysqli_error($link));
    }
    if ($row = mysqli_fetch_assoc($result)) {
        $web = $row['page_content'];
    } else {
        $web = '[nie_znaleziono_strony]';
    }
    return $web;
}
?>
