<?php
function winner($field, $currentPlayer) {
    // Define grid dimensions for easier bounds checking
    $cols = count($field); // 7 columns
    $rows = count($field[0]); // 6 rows

    // Horizontal check (left to right across rows)
    for ($i = 0; $i < $cols - 3; $i++) { // Check up to cols - 4 to avoid out-of-bounds
        for ($j = 0; $j < $rows; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i+1][$j] == $currentPlayer &&
                $field[$i+2][$j] == $currentPlayer &&
                $field[$i+3][$j] == $currentPlayer) {
                return true;
            }
        }
    }

    // Vertical check (top to bottom down columns)
    for ($i = 0; $i < $cols; $i++) {
        for ($j = 0; $j <= $rows - 4; $j++) { // Check up to rows - 4 to avoid out-of-bounds
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i][$j+1] == $currentPlayer &&
                $field[$i][$j+2] == $currentPlayer &&
                $field[$i][$j+3] == $currentPlayer) {
                return true;
            }
        }
    }

    // Diagonal (\) check (bottom-left to top-right)
    for ($i = 0; $i <= $cols - 4; $i++) {
        for ($j = 0; $j <= $rows - 4; $j++) {
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i+1][$j+1] == $currentPlayer &&
                $field[$i+2][$j+2] == $currentPlayer &&
                $field[$i+3][$j+3] == $currentPlayer) {
                return true;
            }
        }
    }

    // Diagonal (/) check (top-left to bottom-right)
    for ($i = 0; $i <= $cols - 4; $i++) {
        for ($j = 3; $j < $rows; $j++) { // Start from row index 3 to avoid out-of-bounds
            if ($field[$i][$j] == $currentPlayer &&
                $field[$i+1][$j-1] == $currentPlayer &&
                $field[$i+2][$j-2] == $currentPlayer &&
                $field[$i+3][$j-3] == $currentPlayer) {
                return true;
            }
        }
    }

    return false;
}
?>