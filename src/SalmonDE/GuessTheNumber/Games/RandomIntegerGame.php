<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class RandomIntegerGame extends NumberGame {

    public function __construct(string $name, string $example, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $example, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['min'];
        $this->firstIntMax = (int) $options['max'];
    }

    public function initGame(){
        $this->solution = random_int($this->firstIntMin, $this->firstIntMax);
        $this->firstInt = &$this->solution;
    }

    public function resetGame(){
        $this->solution = null;
        $this->firstInt = null;
    }
}