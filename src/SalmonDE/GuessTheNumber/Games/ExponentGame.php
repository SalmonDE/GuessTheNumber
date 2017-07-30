<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Games;

class ExponentGame extends NumberGame {

    public function __construct(string $name, string $example, array $options, array $prizes, string $playPermission, string $startPermission){
        parent::__construct($name, $example, $prizes, $playPermission, $startPermission);

        $this->firstIntMin = (int) $options['baseIntMin'];
        $this->firstIntMax = (int) $options['baseIntMax'];

        $this->secondIntMin = (int) $options['exponentMin'];
        $this->secondIntMax = (int) $options['exponentMax'];
    }

    public function initGame(){
        $this->firstInt = random_int($this->firstIntMin, $this->firstIntMax); // base
        $this->secondInt = random_int($this->secondIntMin, $this->secondIntMax); // exponent

        $this->calculation = "({$this->firstInt})^{$this->secondInt}";
        $this->solution = $this->firstInt ** $this->secondInt;
    }

    public function resetGame(){
        $this->firstInt = null;
        $this->secondInt = null;

        $this->calculation = null;
        $this->solution = null;
    }

}
