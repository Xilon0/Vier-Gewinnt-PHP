<?php
function winner($field, $currentPlayer) {
    // horizontal
    for ($i = 0; $i < 6; $i++) {
        for ($j = 0; $j < 4; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i][$j+1] == $currentPlayer &&
                $field[$i][$j+2] == $currentPlayer &&
                $field[$i][$j+3] == $currentPlayer) {
                return true;
            }
        }
    }

    // vertical
    for ($i = 0; $i < 3; $i++) {
        for ($j = 0; $j < 7; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i+1][$j] == $currentPlayer &&
                $field[$i+2][$j] == $currentPlayer &&
                $field[$i+3][$j] == $currentPlayer) {
                return true;
            }
        }
    }

    // diagonal (/)
    for ($i = 3; $i < 6; $i++) {
        for ($j = 0; $j < 4; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i-1][$j+1] == $currentPlayer &&
                $field[$i-2][$j+2] == $currentPlayer &&
                $field[$i-3][$j+3] == $currentPlayer) {
                return true;
            }
        }
    }

    // diagonal (\)
    for ($i = 3; $i < 6; $i++) {
        for ($j = 3; $j < 7; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i-1][$j-1] == $currentPlayer &&
                $field[$i-2][$j-2] == $currentPlayer &&
                $field[$i-3][$j-3] == $currentPlayer) {
                return true;
            }
        }
    }

    return false;
}
?>
