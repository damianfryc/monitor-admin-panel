<?php
// Inicjalizacja sesji
session_start();
	
// Sprawdzanie czy użytkownik jest już zalogowany, jesli tak to zostanie przekierowany do panelu
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: index.php");
    exit;
}

// Dołączanie pliku z konfiguracją połączenia do bazy MySQL
require_once "config.php";
 
// Definiowanie zmiennych i inicjalizowanie z pustymi zmiennymi
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Obsługa formularza
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Sprawdzenie czy pole użytkownik nie jest puste
    if(empty(trim($_POST["username"]))){
        $username_err = "Proszę wprowadź swoją nazwę użytkownika.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Sprawdzenie czy pole hasło nie jest puste
    if(empty(trim($_POST["password"]))){
        $password_err = "Proszę wprowadź swoje hasło.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Walidacja poświadczeń
    if(empty($username_err) && empty($password_err)){
        // Próba wykonania przygotowanego wyrażenia
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Mapuje zmienne do parametrów w przygodowanym wyrażeniu
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Ustawienie parametrów
            $param_username = $username;
            
            // Próba wykonania przygotowanego wyrażenia
            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_store_result($stmt);
                
                // Sprawdza użytkowink o podanej nazwie istnieje
                if(mysqli_stmt_num_rows($stmt) == 1){                    
   
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){

                            session_start();
                            
                            // Zapisuje dane do sessij

                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;   
								
                            
                            // Przekierowanie do panelu po poprawnym logowaniu
                            header("location: index.php");
                        } else{
							// Hasło nie jest prawidłowe, wyświetlenie ogólnej informacji
                            $login_err = "Nieprawidłowy użytkownik lub hasło.";
                        }
                    }
                } else{
					// Użytkownik nie jest prawidłowy, wyświetlenie ogólnej informacji
                    $login_err = "Nieprawidłowy użytkownik lub hasło.";
                }
            } else{
                echo "Coś poszło nie tak. Spróbuj ponownie później.";
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
    <title>Logowanie</title>
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
        <h2>Logowanie</h2>
        <p>Proszę wprowadź swoje dane logowania.</p>

        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Użytkownik</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Hasło</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Zaloguj się">
            </div>           
        </form>
    </div>
</body>
</html>