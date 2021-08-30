<?php
// Inicjalizacja sesji
session_start();
 
// Sprawdzanie czy użytkownik jest już zalogowany, jesli nie to zostanie przekierowany do strony logowania
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Dołączanie pliku z konfiguracją połączenia do bazy MySQL
require_once "config.php";
 
// Definiowanie zmiennych i inicjalizowanie z pustymi zmiennymi
$auth_id = $model = $location = "";
$auth_id_err = $model_err = $location_err = "";
 
// Obsługa formularza
if(isset($_POST["id"]) && !empty($_POST["id"])){

    $id = $_POST["id"];
    
    // Waliduj autoryzowane ID
    $input_auth_id = trim($_POST["auth_id"]);
    if(empty($input_auth_id)){
        $auth_id_err = "Proszę podaj autoryzowany ID.";
    } elseif(!ctype_digit($input_auth_id)){
        $auth_id_err = "Autoryzowane ID może składać się tylko z cyfr.";
    } else{
        $auth_id = $input_auth_id;
    }
    
    // Waliduj model
    $input_model = trim($_POST["model"]);
    if(empty($input_model)){
        $model_err = "Proszę podaj model.";
    } else{
        $model = $input_model;
    }
    
    // Waliduj lokalizację
    $input_location = trim($_POST["location"]);
    if(empty($input_location)){
        $location_err = "Proszę podaj lokalizację.";     
    } elseif(!filter_var($input_location, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $location_err = "Lokalizacja może się składać tylko ze znaków [a-z], [A-Z]";
    } else{
        $location = $input_location;
    }
    
    // Sprawdzanie błędów przed dodaniem do bazy
    if(empty($auth_id_err) && empty($model_err) && empty($location_err)){
        // Przygotowanie wyrażenia dodającego do bazy danych
        $sql = "UPDATE devices SET auth_id=?, model=?, location=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
            mysqli_stmt_bind_param($stmt, "sssi", $param_auth_id, $param_model, $param_location, $param_id);
            
            // Ustawienie parametrów
            $param_auth_id = $auth_id;
            $param_model = $model;
            $param_location = $location;
            $param_id = $id;
            
            // Próba wykonania przygotowanego wyrażenia
            if(mysqli_stmt_execute($stmt)){
				// Rekord zaktualizowany prawidłowo, przekierowanie do strony głównej
                header("location: index.php");
                exit();
            } else{
                echo "Coś poszło nie tak. Spróbuj ponownie później.";
            }
        }
         
        // Zakończenie wyrażenia
        mysqli_stmt_close($stmt);
    }
    
    // Zakończenie połączenia
    mysqli_close($link);
} else{
    // Sprawdza czy istnieje parametr ID
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Pobiera parametr z URLa
        $id =  trim($_GET["id"]);
        
        // Przygotowanie zapytania usunięcia rekordu
        $sql = "SELECT * FROM devices WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Ustawienie parametrów
            $param_id = $id;
            
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
    }  else{
        // URL nie zawiera prawidłwego parametru ID, przekierowanie do strony błędu
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Aktualizacja wpisu</title>
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
                    <h2 class="mt-5">Aktualizacja wpisu</h2>
                    <p>Proszę edytuj i zapisz w celu aktualizacji wpisu.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Autoryzowane ID</label>
                            <input type="text" name="auth_id" class="form-control <?php echo (!empty($auth_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $auth_id; ?>">
                            <span class="invalid-feedback"><?php echo $auth_id_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <textarea name="model" class="form-control <?php echo (!empty($model_err)) ? 'is-invalid' : ''; ?>"><?php echo $model; ?></textarea>
                            <span class="invalid-feedback"><?php echo $model_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Lokalizacja</label>
                            <input type="text" name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $location; ?>">
                            <span class="invalid-feedback"><?php echo $location_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Aktualizuj">
                        <a href="index.php" class="btn btn-secondary ml-2">Anuluj</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>