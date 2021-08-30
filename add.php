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
if($_SERVER["REQUEST_METHOD"] == "POST"){
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
        $sql = "INSERT INTO devices (id, auth_id, model, location) VALUES (0,?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
            mysqli_stmt_bind_param($stmt, "sss", $param_auth_id, $param_model, $param_location);
            
            // Ustawienie parametrów
            $param_auth_id = $auth_id;
            $param_model = $model;
            $param_location = $location;
            
            // Próba wykonania przygotowanego wyrażenia
            if(mysqli_stmt_execute($stmt)){
                // Rekord dodany prawidłowo, przekierowanie do strony głównej
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
}
?>
 
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodawanie rejestratora</title>
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
                    <h2 class="mt-5">Dodawanie rejestratora</h2>
                    <p>Proszę uzupełnić dane nowego rejestratora, który ma zostać autoryzowany.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Autoryzowane ID</label>
                            <input type="text" name="auth_id" class="form-control <?php echo (!empty($auth_id_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $auth_id; ?>">
                            <span class="invalid-feedback"><?php echo $auth_id_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Model</label>
                            <input type="text" name="model" class="form-control <?php echo (!empty($model_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $model; ?>">
                            <span class="invalid-feedback"><?php echo $model_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Lokalizacja</label>
                            <input type="text" name="location" class="form-control <?php echo (!empty($location_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $location; ?>">
                            <span class="invalid-feedback"><?php echo $location_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Dodaj">
                        <a href="index.php" class="btn btn-secondary ml-2">Anuluj</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>