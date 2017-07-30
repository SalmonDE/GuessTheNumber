<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class FactorialGame extends NumberGame {

    public function __construct(string $name, string $example, array $options, array $prizes, string $permission){
        parent::__construct($name, $example, $prizes, $permission);

        $this->firstIntMin = $options['min'] > 0 ? $options['min'] : 0;
        $this->firstIntMax = $options['max'];
    }

    public function initGame(){
        $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax);

        $this->calculation = $this->firstInt.'!';
        $this->solution = gmp_fact($this->firstInt);
    }

    public function resetGame(){
        $this->firstInt = null;
        $this->calculation = null;

        $this->solution = null;
    }

}
