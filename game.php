<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Othello Game</title>
    <link rel="stylesheet" href="public/css/bootstrap.css">
</head>
<body class="bg-dark">
<div class="container pt-3">
    <div class="row justify-content-center">
        <div class="col-md-12 text-center text-primary">
            <h2>Juego del Othello</h2>
        </div>
    </div>

  <?php
    session_start();

    if (!isset($_SESSION['player1']) || !isset($_SESSION['player2']) || !isset($_SESSION['score']) || !isset($_SESSION['currentPlayer']) || !isset($_SESSION['board'])) {
      header("Location: index.php");
      exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      session_destroy();
      header("Location: index.php");
    }

    if (isset($_GET['row']) && isset($_GET['col'])) {
      makeMove($_GET['row'], $_GET['col']);
    }


    getPossibleMoves($_SESSION['board'], $_SESSION['currentPlayer']);

    function reprint()
    {
      $scorePlayer1 = 0;
      $scorePlayer2 = 0;
      $board = $_SESSION['board'];

      foreach ($board as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
          if ($board[$rowIndex][$colIndex] == 3) {
            $board[$rowIndex][$colIndex] = 0;
          } else if ($board[$rowIndex][$colIndex] == 1) {
            $scorePlayer1++;
          } else if ($board[$rowIndex][$colIndex] == 2) {
            $scorePlayer2++;
          }
        }
      }

      $_SESSION['score']['player1'] = $scorePlayer1;
      $_SESSION['score']['player2'] = $scorePlayer2;
      $_SESSION['board'] = $board;
    }

    function makeMove($row, $col)
    {
      $board = $_SESSION['board'];
      $currentPlayer = $_SESSION['currentPlayer'];
      $otherPlayer = ($currentPlayer == 1) ? 2 : 1;

      if ($board[$row][$col] != 3) return;

      $board[$row][$col] = $currentPlayer;

      $directions = array(
        array(-1, 0), array(1, 0), array(0, -1), array(0, 1),
        array(-1, -1), array(-1, 1), array(1, -1), array(1, 1)
      );

      foreach ($directions as $dir) {
        if (isValidDirection($board, $row, $col, $currentPlayer, $dir)) {
          list($deltaRow, $deltaCol) = $dir;
          $newRow = $row + $deltaRow;
          $newCol = $col + $deltaCol;

          while ($newRow >= 0 && $newRow < 8 && $newCol >= 0 && $newCol < 8) {
            if ($board[$newRow][$newCol] == $currentPlayer) {
              break;
            }

            $board[$newRow][$newCol] = $currentPlayer;
            $newRow += $deltaRow;
            $newCol += $deltaCol;
          }
        }
      }

      $_SESSION['board'] = $board;
      $_SESSION['currentPlayer'] = $otherPlayer;
      reprint();
    }

    function getPossibleMoves($board, $currentPlayer)
    {
      $possibleMoves = array();
      $noMoves = true;

      foreach ($board as $rowIndex => $row) {
        foreach ($row as $colIndex => $cell) {
          if (isValidMove($board, $rowIndex, $colIndex, $currentPlayer)) {
            $possibleMoves[] = array($rowIndex, $colIndex);
          }

          if ($cell == 3) {
            $noMoves = false;
          }
        }
      }

      foreach ($possibleMoves as $move) {
        $board[$move[0]][$move[1]] = 3;
        $noMoves = false;
      }

      if ($noMoves) {
        $_SESSION['gameOver'] = true;
      }


      $_SESSION['board'] = $board;
    }

    function isValidMove($board, $row, $col, $currentPlayer)
    {
      if ($board[$row][$col] != 0) return false;

      $directions = array(
        array(-1, 0), array(1, 0), array(0, -1), array(0, 1),
        array(-1, -1), array(-1, 1), array(1, -1), array(1, 1)
      );

      foreach ($directions as $dir) {
        if (isValidDirection($board, $row, $col, $currentPlayer, $dir)) {
          return true;
        }
      }

      return false;
    }


    function isValidDirection($board, $row, $col, $currentPlayer, $dir)
    {
      list($deltaRow, $deltaCol) = $dir;

      $otherPlayer = ($currentPlayer == 1) ? 2 : 1;
      $newRow = $row + $deltaRow;
      $newCol = $col + $deltaCol;

      while ($newRow >= 0 && $newRow < 8 && $newCol >= 0 && $newCol < 8) {
        if ($board[$newRow][$newCol] == $currentPlayer) {
          if ($newRow == $row + $deltaRow && $newCol == $col + $deltaCol) {
            return false;
          }

          return true;
        }

        if ($board[$newRow][$newCol] != $otherPlayer) {
          return false;
        }

        $newRow += $deltaRow;
        $newCol += $deltaCol;
      }

      return false;
    }

  ?>

    <div class='row justify-content-center text-center winner-container text-light'>
      <?php
        $gameOver = $_SESSION['gameOver'];

        if ($gameOver === true) {
          echo "<h1>";
          echo "Fin del Juego el ganador es: <tag class='text-danger'>";
          if ($_SESSION['score']['player1'] > $_SESSION['score']['player2'])
            echo $_SESSION['player1'];
          else
            echo $_SESSION['player2'];
          echo "</tag></h1>";
        } else {
          echo " ";
        }
      ?>
    </div>

    <div class="row justify-content-center mt-3 score-container text-light">
        <div class="col-md-6 text-center">
            <img src="public/images/black.png" width="50">
            <h4><?php echo($_SESSION['player1']) ?></h4>
            <p>Puntos: <?php echo($_SESSION['score']['player1']) ?></p>
        </div>

        <div class="col-md-6 text-center">
            <img src="public/images/white.png" width="50">
            <h4><?php echo($_SESSION['player2']) ?></h4>
            <p>Puntos: <?php echo($_SESSION['score']['player2']) ?></p>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="board-container">
          <?php
            // Imprimir el tablero
            foreach ($_SESSION['board'] as $rowIndex => $row) {
              echo '<div class="row">';
              echo '<div class="col-2"></div>';

              foreach ($row as $colIndex => $cell) {
                echo '<div class="col-1 pt-2 pb-2 ';

                if ($rowIndex % 2 == 0)
                  echo($colIndex % 2 == 0 ? 'bg-secondary ' : 'bg-success ');
                else
                  echo($colIndex % 2 == 0 ? 'bg-success ' : 'bg-secondary ');

                echo 'cell-' . $rowIndex . '-' . $colIndex . '" onclick="cellClick(' . $rowIndex . ',' . $colIndex . ')"';

                echo '">';

                if ($cell == 1) {
                  echo '<img src="public/images/black.png" class="img-fluid" width="30">';
                } elseif ($cell == 2) {
                  echo '<img src="public/images/white.png" class="img-fluid" width="30">';
                } else if ($cell == 3) {
                  echo '<img src="public/images/move.png" class="img-fluid" width="30">';
                } else {
                  echo '&nbsp;';

                }

                echo '</div>';
              }

              echo '<div class="col-2"></div>';

              echo '</div>';
            }
          ?>
        </div>
    </div>

    <div class="row justify-content-center mt-4 mb-2">
        <div class="col-md-6 text-center">
            <form method="post">
                <button type="submit" class="btn btn-lg btn-danger">Terminar Juego</button>
            </form>
        </div>
    </div>
</div>

<script>
    function cellClick(rowIndex, colIndex) {
        let xhttp = new XMLHttpRequest();

        xhttp.onreadystatechange = function () {
            if (this.readyState === 4 && this.status === 200) {
                let parser = new DOMParser();
                let respDoc = parser.parseFromString(this.responseText, 'text/html');

                let boardContent = respDoc.querySelector('.board-container');
                let scoreContent = respDoc.querySelector('.score-container');
                let winnerContent = respDoc.querySelector('.winner-container');

                if (winnerContent) {
                    document.querySelector(".winner-container").innerHTML = winnerContent.innerHTML;
                }

                document.querySelector(".board-container").innerHTML = boardContent.innerHTML;
                document.querySelector(".score-container").innerHTML = scoreContent.innerHTML;
            }
        };

        xhttp.open("GET", "game.php?row=" + rowIndex + "&col=" + colIndex, true);
        xhttp.send();
    }
</script>
</body>
</html>

