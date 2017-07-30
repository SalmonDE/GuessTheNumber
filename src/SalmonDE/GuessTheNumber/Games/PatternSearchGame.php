<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class PatternSearchGame extends NumberGame {

    public function __construct(string $name, string $example, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $example, $prizes, $playPermission, $startPermission);

    }

    public function initGame(){

    }

    public function resetGame(){

    }
}
