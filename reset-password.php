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
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Obsługa formularza
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Waliduj nowe hasło
    if(empty(trim($_POST["new_password"]))){
        $new_password_err = "Proszę wprowadź nowe hasło.";     
    } elseif(strlen(trim($_POST["new_password"])) < 6){
        $new_password_err = "Hasło musi mieć conajmniej 6 znaków.";
    } else{
        $new_password = trim($_POST["new_password"]);
    }
    
    // Waliduj potwierdzenie nowego hasła
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Proszę potwierdź nowe hasło.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
	// Sprawdzanie błędów przed dodaniem do bazy
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Przygotowanie wyrażenia aktualizujące hasło
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
            mysqli_stmt_bind_param($stmt, "si", $param_password, $param_id);
            
            // Ustawienie parametrów
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            
            // Próba wykonania przygotowanego wyrażenia
            if(mysqli_stmt_execute($stmt)){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: login.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

			// Zakończenie wyrażenia
            mysqli_stmt_close($stmt);
        }
    }
    
    // Zakończenie połączenia
    mysqli_close($link);
}
?>
 
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zmiana hasła</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ 
			font: 14px sans-serif; 
		}
        .wrapper{ 
			width: 360px; 
			padding: 20px; 
			margin: 0 auto;
		}
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Zmiana hasła</h2>
        <p>Proszę wprowadź sowje nowe hasło.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>Nowe hasło</label>
                <input type="password" name="new_password" class="form-control <?php echo (!empty($new_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_password; ?>">
                <span class="invalid-feedback"><?php echo $new_password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Potwierdź nowe hasło</label>
                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Zmień">
                <a class="btn btn-link ml-2" href="index.php">Anuluj</a>
            </div>
        </form>
    </div>    
</body>
</html>