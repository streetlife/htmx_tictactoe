<?php 
session_start();
define('ICON_DEFAULT', '`');
define('ICON_PLAYER', 'X');
define("ICON_COMPUTER", 'O');

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'reset') {
        echo start_new_game();
    } elseif ($_GET['action'] == 'move') {
        // get the game session id
        $game_session_id = $_GET['game_session_id'];
        // update the board, check the win condition or perform the next move.
        $board = $_SESSION[$game_session_id]['board'];
        $move['row'] = $_GET['row'];
        $move['column'] = $_GET['column'];
        $board[$move['row']][$move['column']] = ICON_PLAYER;

        if (check_win_condition($board)) {
            echo 'Players Wins';
            $_SESSION['game_over'] = true;
        } else {
            $board = play_computer_move($board);

            if (check_win_condition($board)) {
                echo 'Computer Wins';
                $_SESSION['game_over'] = true;
            }

            $_SESSION[$game_session_id]['board'] = $board;          
        }
        echo display_board($game_session_id, $board);  
    }
} else {
    echo 'Do something';
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
                $btn_action = 'hx-get="game.php?game_session_id=' . $game_session_id . '&action=move&row=' . $i . '&column=' . $j . '" hx-target="#game-board"';
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