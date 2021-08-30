<?php
// Dołączanie pliku z konfiguracją połączenia do bazy MySQL
require_once "config.php";

// Zmienne przekazywane w URL z rejestratora
$id_rejestratora = $_GET['uid']; // Zmienna przechowująca ID rejestratora
$logi = $_GET['data']; // Zmienne przechowująca ciąg danych z parametru data

preg_match_all('!\d+\.*\d*!', $logi, $matches); // Wyrażenie regularne

foreach ($matches as $key => $val) {
	$sql_insert = "INSERT INTO log2 (ID, date, temp, zas, id_rej) 
	VALUES (0, CURRENT_TIMESTAMP(), '".$val[0]."', '".$val[1]."', '".$id_rejestratora."')";	
}
// Sprawdza czy przekazywany jest ID
if(!empty($id_rejestratora)) {
	$sql = "SELECT * FROM devices WHERE auth_id = ?";
	
	if($stmt = mysqli_prepare($link, $sql)){
		
		// Mapuje zmienne do parametrów w przygodowanym wyrażeniu
		mysqli_stmt_bind_param($stmt, "s", $id_rejestratora);
			            
		// Próba wykonania przygotowanego wyrażenia
        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
                
            // Sprawdza czy rejestrator o przekazywanym ID istnieje
            if(mysqli_stmt_num_rows($stmt) == 1){
				if(mysqli_query($link, $sql_insert)) { // Dodanie logów do bazy
					echo "Sukces"; // Wyświetlenie komunikatu "Sukces" w przypadku powodzenia
				} else {
					echo "Coś poszło nie tak. Spróbuj ponownie później."; // Wyświetlenie błędu w przypadku niepowodzenia
				}
			} else {
				echo "Brak autoryzacji dla rejestratora o ID: ".$id_rejestratora;
			}
		} else{
			echo "Coś poszło nie tak. Spróbuj ponownie później.";
        }
	
		// Zakończenie wyrażenia
		mysqli_stmt_close($stmt);
	}
} else {
	echo "Brak ID rejestratora";
}
// Zakończenie połączenia
mysqli_close($link);

?>