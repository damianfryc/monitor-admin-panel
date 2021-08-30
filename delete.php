<?php
// Inicjalizacja sesji
session_start();
 
// Sprawdzanie czy użytkownik jest już zalogowany, jesli nie to zostanie przekierowany do strony logowania
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Dołączanie pliku z konfiguracją połączenia do bazy MySQL
    require_once "config.php";
    
    // Przygotowanie zapytania usunięcia rekordu
    $sql = "DELETE FROM devices WHERE id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Ustawienie parametrów
        $param_id = trim($_POST["id"]);
        
        // Próba wykonania przygotowanego wyrażenia
        if(mysqli_stmt_execute($stmt)){
            // Rekord usunięty prawidłowo, przekierowanie do strony głównej
            header("location: index.php");
            exit();
        } else{
            echo "Coś poszło nie tak. Spróbuj ponownie później.";
        }
    }
     
    // Zakończenie wyrażenia
    mysqli_stmt_close($stmt);
    
    // Zakończenie połączenia
    mysqli_close($link);
} else{
    // Sprawdza czy istnieje parametr ID
    if(empty(trim($_GET["id"]))){
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
    <title>Usunięcie wpisu</title>
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
                    <h2 class="mt-5 mb-3">Usunięcie wpisu</h2>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Czy na pewno chcesz usunąć ten wpis?</p>
                            <p>
                                <input type="submit" value="Tak" class="btn btn-danger">
                                <a href="index.php" class="btn btn-secondary">Nie</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>