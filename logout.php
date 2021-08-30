<?php
// Inicjalizacja sesji
session_start();
 
// Wyczyszczenie wszystkich zmiennych sesji
$_SESSION = array();
 
// Zniszczenie sesji
session_destroy();
 
// Przekierowanie do strony logowania
header("location: login.php");
exit;
?>