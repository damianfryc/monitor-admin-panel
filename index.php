<?php
// Inicjalizacja sesji
session_start();
							
// Sprawdzanie czy użytkownik jest już zalogowany, jesli nie to zostanie przekierowany do strony logowania
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel administracyjny</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        table tr td:last-child{
            width: 120px;
        }
    </style>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();   
        });
    </script>
</head>
<body>
    <div class="wrapper">
	
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
				<div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Cześć, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</h2>
                        <h2 class="pull-right">
							<a href="reset-password.php" class="btn btn-warning">Zmień hasło</a>
							<a href="logout.php" class="btn btn-danger ml-3">Wyloguj się</a>
						</h2>
   
                    </div>
                    <div class="mt-5 mb-3 clearfix">
                        <h2 class="pull-left">Panel administracyjny</h2>
                        <a href="add.php" class="btn btn-success pull-right"><i class="fa fa-plus"></i> Dodaj nowy rejestrator</a>
                    </div>
					<?php
                    // Dołączanie pliku z konfiguracją połączenia do bazy MySQL
                    require_once "config.php";
                    
                    // Próba wykonania przygotowanego wyrażenia
                    $sql = "SELECT * FROM devices";
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo '<table class="table table-bordered table-striped">';
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Autoryzowane ID</th>";
                                        echo "<th>Model</th>";
                                        echo "<th>Lokalizacja</th>";
                                        echo "<th>Akcja</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['auth_id'] . "</td>";
                                        echo "<td>" . $row['model'] . "</td>";
                                        echo "<td>" . $row['location'] . "</td>";
                                        echo "<td>";
                                            echo '<a href="view.php?id='. $row['id'] .'" class="mr-3" title="Wyświetl wpis" data-toggle="tooltip"><span class="fa fa-eye"></span></a>';
                                            echo '<a href="update.php?id='. $row['id'] .'" class="mr-3" title="Aktualizuj wpis" data-toggle="tooltip"><span class="fa fa-pencil"></span></a>';
                                            echo '<a href="delete.php?id='. $row['id'] .'" title="Usuń wpis" data-toggle="tooltip"><span class="fa fa-trash"></span></a>';
                                        echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            mysqli_free_result($result);
                        } else{
							echo '<div class="alert alert-danger"><em>Nie znaleziono żadnych rekordów.</em></div>';
                        }
                    } else{
                        echo "Coś poszło nie tak. Spróbuj ponownie później.";
                    }
                    // Zakończenie połączenia
                    mysqli_close($link);
                    ?>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>