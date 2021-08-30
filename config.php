<?php

define('DB_SERVER', '10.1.2.58'); // Host serwera MySQL
define('DB_USERNAME', 'monitor_user'); // Użytkownik z uprawnieniami do zapisu logów do bazy "log"
define('DB_PASSWORD', 'XXXXXX'); // Hasło do bazy danych, ze względów bezpieczeństwa zostało usunięte
define('DB_NAME', 'log'); // Nazwa bazy danych gdzie zapisywane są logi

	// Tworzenie połączenia do bazy danych z wykorzystanie zmiennych
	$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
	// Sprawdzenie połączenia
	if($link === false){
		die("ERROR: Brak połączenia. " . mysqli_connect_error()); // W przypadku niepowodzenia, wyświetlenie błędu
	}
?>