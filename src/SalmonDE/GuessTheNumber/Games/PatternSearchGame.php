<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class PatternSearchGame extends NumberGame {

    public function __construct(string $name, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $prizes, $playPermission, $startPermission);

    }

    public function initGame(){

    }

    public function resetGame(){

    }

    public function getExample(): string{

    }
}
