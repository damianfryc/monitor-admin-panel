<?php
// Inicjalizacja sesji
session_start();
 
// Sprawdzanie czy użytkownik jest już zalogowany, jesli nie to zostanie przekierowany do strony logowania
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
	// Dołączanie pliku z konfiguracją połączenia do bazy MySQL
    require_once "config.php";
    
    // Przygotowanie zapytania
    $sql = "SELECT * FROM devices WHERE id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Ustawienie parametrów
        $param_id = trim($_GET["id"]);
        
        // Próba wykonania przygotowanego wyrażenia
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
				// Pobiera wpis dla 1 pozycij
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Pobiera wartości pól dla danego wpisu
                $auth_id = $row["auth_id"];
                $model = $row["model"];
                $location = $row["location"];
            } else{
				// URL nie zawiera prawidłwego parametru ID, przekierowanie do strony błędu
                header("location: error.php");
                exit();
            }
            
        } else{
			echo "Coś poszło nie tak. Spróbuj ponownie później.";
        }
    }
     
    // Zakończenie wyrażenia
    mysqli_stmt_close($stmt);
    
    // Zakończenie połączenia
    mysqli_close($link);
} else{
	// URL nie zawiera prawidłwego parametru ID, przekierowanie do strony błędu
    header("location: error.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podgląd pozycji</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-5 mb-3">Podgląd pozycji</h1>
                    <div class="form-group">
                        <label>Autoryzowane ID</label>
                        <p><b><?php echo $row["auth_id"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <p><b><?php echo $row["model"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Lokalizacja</label>
                        <p><b><?php echo $row["location"]; ?></b></p>
                    </div>
                    <p><a href="index.php" class="btn btn-primary">Powrót</a></p>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>