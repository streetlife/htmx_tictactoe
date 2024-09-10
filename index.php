<?php 
session_start();
define('ICON_DEFAULT', '`');
define('ICON_PLAYER', 'X');
define("ICON_COMPUTER", 'O');

$message = '';
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'reset') {
        $message .= start_new_game();
    } elseif ($_GET['action'] == 'move') {
        // get the game session id
        $game_session_id = $_GET['game_session_id'];
        // update the board, check the win condition or perform the next move.
        $board = $_SESSION[$game_session_id]['board'];
        $move['row'] = $_GET['row'];
        $move['column'] = $_GET['column'];
        $board[$move['row']][$move['column']] = ICON_PLAYER;

        if (check_win_condition($board)) {
            $message .= 'Players Wins';
            $_SESSION['game_over'] = true;
        } else {
            $board = play_computer_move($board);

            if (check_win_condition($board)) {
                $message .= 'Computer Wins';
                $_SESSION['game_over'] = true;
            }

            $_SESSION[$game_session_id]['board'] = $board;          
        }
        $message.= display_board($game_session_id, $board);  
    }
    display_output($message);
}

function start_new_game() {
    $game_session_id = 'game-'.uniqid();
    $board = [];
    for ($i = 1; $i <= 3; $i++) {
        for ($j = 1; $j <= 3; $j++) {
            $board[$i][$j] = ICON_DEFAULT;
        }
    }
    $_SESSION['game_session_id'] = $game_session_id;
    $_SESSION[$game_session_id]['board'] = $board;
    $_SESSION['game_over'] = false;
    return display_board($game_session_id, $board);
}

function display_board($game_session_id, $board) {
    $display_board = '';
    $board = $_SESSION[$game_session_id]['board'];

    for ($i = 1; $i <= 3; $i++) {
        $display_board .= '<div class="row">';
        for ($j = 1; $j <= 3; $j++) {
            $display_board .= '<div class="col-4 border p-2">';
            if ($_SESSION['game_over']) {
                $btn_action = '';
            } else {
                $btn_action = 'hx-get="?game_session_id=' . $game_session_id . '&action=move&row=' . $i . '&column=' . $j . '" hx-target="#game-board"';
            }
            if ($board[$i][$j] == ICON_DEFAULT) {
                $display_board .= '<button class="btn btn-info btn-large p-5" '.$btn_action.'>' . $board[$i][$j] . '</button>';
            } elseif ($board[$i][$j] == ICON_PLAYER) {
                $display_board .= '<button class="btn btn-success btn-large p-5" '.$btn_action.'>' . $board[$i][$j] . '</button>';
            } else {
                $display_board .= '<button class="btn btn-danger btn-large p-5" '.$btn_action.'>' . $board[$i][$j] . '</button>';
                
            }
            $display_board .= '</div>';
        }
        $display_board .= '</div>';
    }
    
    return $display_board;
}

function check_win_condition($board) {
    $win = false;

    // check rows
    for ($i = 1; $i <= 3; $i++) {
        if ($board[$i][1] == $board[$i][2] && $board[$i][1] == $board[$i][3] && $board[$i][1] != ICON_DEFAULT) {
            $win = true;
        }
    }
    // check columns
    for ($i = 1; $i <= 3; $i++) {
        if ($board[1][$i] == $board[2][$i] && $board[1][$i] == $board[3][$i] && $board[1][$i] != ICON_DEFAULT) {
            $win = true;
        }
    }
    // check diagonals
    if ($board[1][1] == $board[2][2] && $board[1][1] == $board[3][3] && $board[1][1] != ICON_DEFAULT) {
        $win = true;
    }   
    if ($board[1][3] == $board[2][2] && $board[1][3] == $board[3][1] && $board[1][3] != ICON_DEFAULT) {
        $win = true;
    }
    
    return $win;
}

function play_computer_move($board) {
    $computer_played = false;

    while ($computer_played == false) {
        $random_move_row = rand(1,3);
        $random_move_column = rand(1,3);
        if ($board[$random_move_row][$random_move_column] == ICON_DEFAULT) {
            $board[$random_move_row][$random_move_column] = ICON_COMPUTER;
            $computer_played = true;
        }
    }

    return $board;
}

function display_output($message) {
    echo $message;
    die();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/htmx.org@2.0.2" integrity="sha384-Y7hw+L/jvKeWIRRkqWYfPcvVxHzVzn5REgzbawhxAuQGwX1XWe70vji+VSeHOThJ" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <title>HTMX TicTacToe</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h2>TicTacToe</h2>
                <button hx-get="?action=reset" hx-target="#game-board">New Game</button>

                <p>Author: Demola Oladipo</p>
                <p><a href="https://oladipo.com.ng">Website</a></p>
                <p><a href="https://x.com/streetlife">X</a></p>
            </div>
            <div class="col-6">
                <div class="row">
                    <div class="col-12">
                        <div id="game-board"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>