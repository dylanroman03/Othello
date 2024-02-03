<?php
  session_start();

  if (isset($_SESSION['player1']) && isset($_SESSION['player2']) && isset($_SESSION['score']) && isset($_SESSION['currentPlayer']) && isset($_SESSION['board'])) {
    header("Location: game.php");
    exit();
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['player1']) && !empty($_POST['player2'])) {
      $_SESSION['player1'] = $_POST['player1'];
      $_SESSION['player2'] = $_POST['player2'];
      $_SESSION['currentPlayer'] = 1;
      $_SESSION['board'] = initializeBoard();
      $_SESSION['score'] = ['player1' => 2, 'player2' => 2];
      header("Location: game.php");
      exit();
    } else {
      $error_message = "Los nombres de los jugadores no pueden estar vacíos.";
    }
  }

  function initializeBoard()
  {
    $board = array_fill(0, 8, array_fill(0, 8, 0));

    $board[3][3] = $board[4][4] = 1;
    $board[3][4] = $board[4][3] = 2;

    return $board;
  }

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Othello Game</title>
    <!--link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">-->
    <link rel="stylesheet" href="public/css/bootstrap.css">
</head>
<body class="bg-dark">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card bg-secondary my-auto">
                <div class="card-body text-light">
                    <div class="row text-center">
                        <h5 class="card-title">Ingresa los Nombres de los Jugadores</h5>
                    </div>
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                      <?php if (isset($error_message)): ?>
                          <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                          </div>
                      <?php endif; ?>
                        <div class="form-group row mt-3">
                            <div class="col col-3">
                                <img src="public/images/black.png" width="30">
                                <label for="player1">Jugador 1:</label>
                            </div>
                            <div class="col col-8">
                                <input type="text" class="form-control" name="player1">
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col col-3">
                                <img src="public/images/white.png" width="30">
                                <label for="player2">Jugador 2:</label>
                            </div>
                            <div class="col col-8">
                                <input type="text" class="form-control" name="player2">
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-primary">Comenzar Juego</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="public/js/bootstrap.js"></script>
</body>
</html>
