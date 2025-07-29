<?php

// Lätt schackmotor i PHP (endast legala drag och auto-spel)
class ChessGame {
    private $board;
    private $turn;
    private $pieces = ['R', 'N', 'B', 'Q', 'K', 'B', 'N', 'R'];
    private $running = true;

    public function __construct() {
        $this->resetBoard();
    }

    public function resetBoard() {
        $this->board = [];

        // Black
        $this->board[] = array_map(fn($p) => 'b' . $p, $this->pieces);
        $this->board[] = array_fill(0, 8, 'bp');
        // Empty rows
        for ($i = 0; $i < 4; $i++) {
            $this->board[] = array_fill(0, 8, '  ');
        }
        // White
        $this->board[] = array_fill(0, 8, 'wp');
        $this->board[] = array_map(fn($p) => 'w' . $p, $this->pieces);

        $this->turn = 'w';
    }

    public function displayBoard() {
        echo "\033[2J\033[H"; // Rensar skärmen
        echo "  a  b  c  d  e  f  g  h\n";
        foreach (array_reverse($this->board, true) as $i => $row) {
            echo (8 - $i) . " ";
            foreach ($row as $cell) {
                echo $cell . " ";
            }
            echo " " . (8 - $i) . "\n";
        }
        echo "  a  b  c  d  e  f  g  h\n";
    }

    public function getLegalMoves() {
        // Förenklade regler: returnerar alla drag ett lag kan göra
        $moves = [];

        for ($i = 0; $i < 8; $i++) {
            for ($j = 0; $j < 8; $j++) {
                $piece = $this->board[$i][$j];
                if ($piece[0] === $this->turn) {
                    // Endast bönder för enkelhet
                    if (substr($piece, 1) === 'p') {
                        $dir = $this->turn === 'w' ? -1 : 1;
                        $ni = $i + $dir;
                        if ($ni >= 0 && $ni < 8 && $this->board[$ni][$j] === '  ') {
                            $moves[] = [[$i, $j], [$ni, $j]];
                        }
                    }
                }
            }
        }

        return $moves;
    }

    public function makeMove() {
        $moves = $this->getLegalMoves();

        if (empty($moves)) {
            echo strtoupper($this->turn === 'w' ? 'Black' : 'White') . " wins!\n";
            sleep(2);
            $this->resetBoard();
            return;
        }

        $move = $moves[array_rand($moves)];
        [$from, $to] = $move;

        $this->board[$to[0]][$to[1]] = $this->board[$from[0]][$from[1]];
        $this->board[$from[0]][$from[1]] = '  ';

        $this->turn = $this->turn === 'w' ? 'b' : 'w';
    }

    public function run() {
        while ($this->running) {
            $this->displayBoard();
            $this->makeMove();
            sleep(1);
        }
    }
}

$game = new ChessGame();
$game->run();